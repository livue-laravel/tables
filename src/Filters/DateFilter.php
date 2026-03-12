<?php

namespace Primix\Tables\Filters;

use Carbon\CarbonInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class DateFilter extends Filter
{
    protected bool|Closure $isRange = false;

    protected ?string $format = null;

    protected CarbonInterface|string|Closure|null $minDate = null;

    protected CarbonInterface|string|Closure|null $maxDate = null;

    public function range(bool|Closure $condition = true): static
    {
        $this->isRange = $condition;

        return $this;
    }

    public function format(?string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function minDate(CarbonInterface|string|Closure|null $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    public function maxDate(CarbonInterface|string|Closure|null $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    public function isRange(): bool
    {
        return (bool) $this->evaluate($this->isRange);
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getMinDate(): CarbonInterface|string|null
    {
        return $this->evaluate($this->minDate);
    }

    public function getMaxDate(): CarbonInterface|string|null
    {
        return $this->evaluate($this->maxDate);
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($this->isRange()) {
            if (! is_array($value)) {
                return $query;
            }

            $from = $value['from'] ?? null;
            $until = $value['until'] ?? null;

            if ($from && $until) {
                return $query->whereDate($this->getName(), '>=', $from)
                    ->whereDate($this->getName(), '<=', $until);
            }

            if ($from) {
                return $query->whereDate($this->getName(), '>=', $from);
            }

            if ($until) {
                return $query->whereDate($this->getName(), '<=', $until);
            }

            return $query;
        }

        if ($value === null || $value === '') {
            return $query;
        }

        return $query->whereDate($this->getName(), $value);
    }

    public function getView(): string
    {
        return 'primix-tables::filters.date-filter';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'range' => $this->isRange(),
            'format' => $this->getFormat(),
            'minDate' => $this->getMinDate() instanceof CarbonInterface
                ? $this->getMinDate()->toDateString()
                : $this->getMinDate(),
            'maxDate' => $this->getMaxDate() instanceof CarbonInterface
                ? $this->getMaxDate()->toDateString()
                : $this->getMaxDate(),
        ]);
    }
}

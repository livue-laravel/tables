<?php

namespace Primix\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class TrashedFilter extends Filter
{
    protected string|Closure $withLabel = 'With trashed';

    protected string|Closure $withoutLabel = 'Without trashed';

    protected string|Closure $onlyLabel = 'Only trashed';

    public function withLabel(string|Closure $label): static
    {
        $this->withLabel = $label;

        return $this;
    }

    public function withoutLabel(string|Closure $label): static
    {
        $this->withoutLabel = $label;

        return $this;
    }

    public function onlyLabel(string|Closure $label): static
    {
        $this->onlyLabel = $label;

        return $this;
    }

    public function getWithLabel(): string
    {
        return $this->evaluate($this->withLabel);
    }

    public function getWithoutLabel(): string
    {
        return $this->evaluate($this->withoutLabel);
    }

    public function getOnlyLabel(): string
    {
        return $this->evaluate($this->onlyLabel);
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($value === null || $value === '' || $value === 'without') {
            return $query;
        }

        if ($value === 'with') {
            return $query->withTrashed();
        }

        if ($value === 'only') {
            return $query->onlyTrashed();
        }

        return $query;
    }

    public function getView(): string
    {
        return 'primix-tables::filters.trashed-filter';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'withLabel' => $this->getWithLabel(),
            'withoutLabel' => $this->getWithoutLabel(),
            'onlyLabel' => $this->getOnlyLabel(),
        ]);
    }
}

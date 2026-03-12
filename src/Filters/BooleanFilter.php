<?php

namespace Primix\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class BooleanFilter extends Filter
{
    protected string|Closure $trueLabel = 'Yes';

    protected string|Closure $falseLabel = 'No';

    public function trueLabel(string|Closure $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    public function falseLabel(string|Closure $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    public function getTrueLabel(): string
    {
        return $this->evaluate($this->trueLabel);
    }

    public function getFalseLabel(): string
    {
        return $this->evaluate($this->falseLabel);
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($value === null) {
            return $query;
        }

        return $query->where($this->getName(), (bool) $value);
    }

    public function getView(): string
    {
        return 'primix-tables::filters.boolean-filter';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'trueLabel' => $this->getTrueLabel(),
            'falseLabel' => $this->getFalseLabel(),
        ]);
    }
}

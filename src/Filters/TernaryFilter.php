<?php

namespace Primix\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class TernaryFilter extends Filter
{
    protected string|Closure $trueLabel = 'Yes';

    protected string|Closure $falseLabel = 'No';

    protected string|Closure $allLabel = 'All';

    protected bool|Closure $isNullable = false;

    protected ?string $column = null;

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

    public function allLabel(string|Closure $label): static
    {
        $this->allLabel = $label;

        return $this;
    }

    public function nullable(bool|Closure $condition = true): static
    {
        $this->isNullable = $condition;

        return $this;
    }

    public function column(?string $column): static
    {
        $this->column = $column;

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

    public function getAllLabel(): string
    {
        return $this->evaluate($this->allLabel);
    }

    public function isNullable(): bool
    {
        return (bool) $this->evaluate($this->isNullable);
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getName();
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($value === null) {
            return $query;
        }

        $column = $this->getColumn();

        if ($this->isNullable()) {
            if ($value) {
                return $query->whereNotNull($column);
            }

            return $query->whereNull($column);
        }

        return $query->where($column, (bool) $value);
    }

    public function getView(): string
    {
        return 'primix-tables::filters.ternary-filter';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'trueLabel' => $this->getTrueLabel(),
            'falseLabel' => $this->getFalseLabel(),
            'allLabel' => $this->getAllLabel(),
            'nullable' => $this->isNullable(),
        ]);
    }
}

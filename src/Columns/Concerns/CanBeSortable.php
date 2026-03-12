<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait CanBeSortable
{
    protected bool|Closure $isSortable = false;

    protected ?string $sortColumn = null;

    public function sortable(bool|Closure $condition = true, ?string $column = null): static
    {
        $this->isSortable = $condition;
        $this->sortColumn = $column;

        return $this;
    }

    public function isSortable(): bool
    {
        return (bool) $this->evaluate($this->isSortable);
    }

    public function getSortColumn(): string
    {
        return $this->sortColumn ?? $this->getName();
    }
}

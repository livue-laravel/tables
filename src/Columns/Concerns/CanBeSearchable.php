<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait CanBeSearchable
{
    protected bool|Closure $isSearchable = false;

    protected bool|Closure $isGloballySearchable = true;

    protected bool|Closure $isIndividuallySearchable = false;

    protected ?string $searchColumn = null;

    public function searchable(
        bool|Closure $condition = true,
        ?string $column = null,
        bool $isIndividual = false,
        bool $isGlobal = true,
    ): static {
        $this->isSearchable = $condition;
        $this->searchColumn = $column;
        $this->isIndividuallySearchable = $isIndividual ? $condition : false;
        $this->isGloballySearchable = $isGlobal ? $condition : false;

        return $this;
    }

    public function globallySearchable(bool|Closure $condition = true): static
    {
        $this->isGloballySearchable = $condition;

        return $this;
    }

    public function individuallySearchable(bool|Closure $condition = true): static
    {
        $this->isIndividuallySearchable = $condition;

        if ($condition) {
            $this->isSearchable = true;
        }

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function isGloballySearchable(): bool
    {
        return (bool) $this->evaluate($this->isGloballySearchable);
    }

    public function isIndividuallySearchable(): bool
    {
        return (bool) $this->evaluate($this->isIndividuallySearchable);
    }

    public function getSearchColumn(): string
    {
        return $this->searchColumn ?? $this->getName();
    }
}

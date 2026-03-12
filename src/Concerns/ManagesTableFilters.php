<?php

namespace Primix\Tables\Concerns;

trait ManagesTableFilters
{
    public array $tableFilters = [];

    public bool $showFilterModal = false;

    public function setTableFilter(string $name, mixed $value): void
    {
        $this->tableFilters[$name] = $value;
        $this->resetPage();
    }

    public function resetTableFilters(): void
    {
        $this->tableFilters = [];
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        foreach ($this->tableFilters as $value) {
            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }
}

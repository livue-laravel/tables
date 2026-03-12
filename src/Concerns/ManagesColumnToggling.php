<?php

namespace Primix\Tables\Concerns;

trait ManagesColumnToggling
{
    public array $toggledHiddenColumns = [];

    public bool $tableColumnsInitialized = false;

    public function toggleColumn(string $columnName): void
    {
        if (in_array($columnName, $this->toggledHiddenColumns)) {
            $this->toggledHiddenColumns = array_values(
                array_diff($this->toggledHiddenColumns, [$columnName])
            );
        } else {
            $this->toggledHiddenColumns[] = $columnName;
        }
    }

    protected function initializeToggleableColumns(): void
    {
        if ($this->tableColumnsInitialized) {
            return;
        }

        $this->tableColumnsInitialized = true;

        foreach ($this->getTable()->getColumns() as $column) {
            if ($column->isToggleable() && $column->isToggledHiddenByDefault()) {
                $this->toggledHiddenColumns[] = $column->getName();
            }
        }
    }
}

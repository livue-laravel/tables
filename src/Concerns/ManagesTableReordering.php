<?php

namespace Primix\Tables\Concerns;

trait ManagesTableReordering
{
    public array $collapsedGroups = [];

    public function reorderTable(int|string $item, int $position): void
    {
        $table = $this->getTable();
        $query = $table->getQuery();
        $keyName = $table->getRecordKeyName();
        $orderColumn = $table->getOrderColumn();

        $record = (clone $query)->where($keyName, $item)->firstOrFail();
        $oldPosition = $record->{$orderColumn};

        if ($oldPosition === $position) {
            return;
        }

        if ($oldPosition < $position) {
            (clone $query)->whereBetween($orderColumn, [$oldPosition + 1, $position])
                ->decrement($orderColumn);
        } else {
            (clone $query)->whereBetween($orderColumn, [$position, $oldPosition - 1])
                ->increment($orderColumn);
        }

        $record->{$orderColumn} = $position;
        $record->save();
    }

    public function isReorderEnabled(): bool
    {
        $table = $this->getTable();

        if (! $table->isReorderable()) {
            return false;
        }

        // Disable reorder when user has active sort, search, or filters
        if ($this->tableSortColumn !== '') {
            return false;
        }

        if ($this->tableSearch !== '') {
            return false;
        }

        if ($this->hasActiveFilters()) {
            return false;
        }

        return true;
    }

    public function toggleGroupCollapse(string $groupKey): void
    {
        if (in_array($groupKey, $this->collapsedGroups)) {
            $this->collapsedGroups = array_values(
                array_diff($this->collapsedGroups, [$groupKey])
            );
        } else {
            $this->collapsedGroups[] = $groupKey;
        }
    }

    public function isGroupCollapsed(string $groupKey): bool
    {
        return in_array($groupKey, $this->collapsedGroups);
    }
}

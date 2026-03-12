<?php

namespace Primix\Tables\Concerns;

trait ManagesRecordSelection
{
    public array $selectedRecords = [];

    public function toggleSelectAll(): void
    {
        $allIds = $this->getTableRecords()
            ->pluck($this->getTable()->getRecordKeyName())
            ->toArray();

        if (count($this->selectedRecords) === count($allIds)) {
            $this->selectedRecords = [];
        } else {
            $this->selectedRecords = $allIds;
        }
    }

    public function toggleSelectRecord(int|string $id): void
    {
        if (in_array($id, $this->selectedRecords)) {
            $this->selectedRecords = array_values(array_diff($this->selectedRecords, [$id]));
        } else {
            $this->selectedRecords[] = $id;
        }
    }

    public function deselectAllRecords(): void
    {
        $this->selectedRecords = [];
    }

    public function isRecordSelected(int|string $id): bool
    {
        return in_array($id, $this->selectedRecords);
    }

    public function getSelectedRecordsCount(): int
    {
        return count($this->selectedRecords);
    }
}

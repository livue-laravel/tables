<?php

namespace Primix\Tables\Concerns;

use Primix\Actions\BulkAction;

trait ExecutesBulkActions
{
    public function callBulkAction(array $arguments): void
    {
        $name = $arguments['name'] ?? null;
        $table = $this->getTable();

        $action = collect($table->getBulkActions())
            ->first(fn ($a) => $a->getName() === $name);

        if (! $action || ! $action instanceof BulkAction) {
            return;
        }

        if ($action->isHidden() || $action->isDisabled()) {
            return;
        }

        if (empty($this->selectedRecords)) {
            return;
        }

        $query = clone $table->getQuery();
        $records = $query->whereIn($table->getRecordKeyName(), $this->selectedRecords)->get();

        $action->records($records);
        $action->call();

        $this->deselectAllRecords();
    }
}

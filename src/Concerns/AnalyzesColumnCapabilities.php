<?php

namespace Primix\Tables\Concerns;

use Primix\Tables\Columns\Column;

trait AnalyzesColumnCapabilities
{
    public function getSearchableColumns(): array
    {
        return array_filter($this->columns, fn (Column $column) => $column->isSearchable());
    }

    public function getGloballySearchableColumns(): array
    {
        return array_filter(
            $this->columns,
            fn (Column $column) => $column->isSearchable() && $column->isGloballySearchable(),
        );
    }

    public function hasGloballySearchableColumns(): bool
    {
        return count($this->getGloballySearchableColumns()) > 0;
    }

    public function getEditableColumns(): array
    {
        return array_filter($this->columns, function (Column $column) {
            return method_exists($column, 'isEditable') && $column->isEditable();
        });
    }

    public function hasEditableColumns(): bool
    {
        return count($this->getEditableColumns()) > 0;
    }

    public function getSummarizableColumns(): array
    {
        return array_filter($this->columns, fn (Column $column) => $column->hasSummarizers());
    }

    public function hasSummarizableColumns(): bool
    {
        return count($this->getSummarizableColumns()) > 0;
    }

    public function getMaxSummarizersCount(): int
    {
        $max = 0;

        foreach ($this->columns as $column) {
            $count = count($column->getSummarizers());
            if ($count > $max) {
                $max = $count;
            }
        }

        return $max;
    }
}

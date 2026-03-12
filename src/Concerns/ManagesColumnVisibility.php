<?php

namespace Primix\Tables\Concerns;

use Primix\Tables\Columns\Column;

trait ManagesColumnVisibility
{
    public function getVisibleColumns(): array
    {
        $toggledHidden = [];
        $livue = $this->getLiVue();

        if ($livue && property_exists($livue, 'toggledHiddenColumns')) {
            $toggledHidden = $livue->toggledHiddenColumns;
        }

        return array_filter(
            $this->columns,
            fn (Column $column) => $column->isVisible()
                && ! in_array($column->getName(), $toggledHidden),
        );
    }

    public function getToggleableColumns(): array
    {
        return array_filter($this->columns, fn (Column $column) => $column->isToggleable());
    }

    public function hasToggleableColumns(): bool
    {
        return count($this->getToggleableColumns()) > 0;
    }

    public function hasIndividuallySearchableColumns(): bool
    {
        return count(array_filter(
            $this->columns,
            fn (Column $column) => $column->isIndividuallySearchable(),
        )) > 0;
    }
}

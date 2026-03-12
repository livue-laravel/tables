<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Support\Concerns\HasIcon;
use Primix\Tables\Columns\Concerns\ResolvesEnumState;

class BadgeColumn extends Column
{
    use HasIcon;
    use ResolvesEnumState;

    protected array|Closure $colors = [];

    protected array|Closure $icons = [];

    public function colors(array|Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function icons(array|Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function getColors(): array
    {
        return $this->evaluate($this->colors);
    }

    public function getIcons(): array
    {
        return $this->evaluate($this->icons);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.badge-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'colors' => $this->getColors(),
            'icons' => $this->getIcons(),
        ]);
    }
}

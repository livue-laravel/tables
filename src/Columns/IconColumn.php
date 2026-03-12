<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Support\Colors\Color;
use Primix\Support\Concerns\HasSize;
use Primix\Tables\Columns\Concerns\ResolvesEnumState;

class IconColumn extends Column
{
    use HasSize;
    use ResolvesEnumState;

    protected array|Closure $icons = [];

    protected array|Closure $colors = [];

    protected bool|Closure $isBoolean = false;

    protected string|Closure|null $trueIcon = 'pi pi-check-circle';

    protected string|Closure|null $falseIcon = 'pi pi-times-circle';

    protected Color|string|Closure|null $trueColor = 'success';

    protected Color|string|Closure|null $falseColor = 'danger';

    public function icons(array|Closure $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function colors(array|Closure $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function boolean(): static
    {
        $this->isBoolean = true;

        return $this;
    }

    public function trueIcon(string|Closure|null $icon): static
    {
        $this->trueIcon = $icon;

        return $this;
    }

    public function falseIcon(string|Closure|null $icon): static
    {
        $this->falseIcon = $icon;

        return $this;
    }

    public function trueColor(Color|string|Closure|null $color): static
    {
        $this->trueColor = $color;

        return $this;
    }

    public function falseColor(Color|string|Closure|null $color): static
    {
        $this->falseColor = $color;

        return $this;
    }

    public function getIcons(): array
    {
        return $this->evaluate($this->icons);
    }

    public function getColors(): array
    {
        return $this->evaluate($this->colors);
    }

    public function isBoolean(): bool
    {
        return (bool) $this->evaluate($this->isBoolean);
    }

    public function getTrueIcon(): ?string
    {
        return $this->evaluate($this->trueIcon);
    }

    public function getFalseIcon(): ?string
    {
        return $this->evaluate($this->falseIcon);
    }

    public function getTrueColor(): ?string
    {
        $color = $this->evaluate($this->trueColor);

        if ($color instanceof Color) {
            return $color->toHex();
        }

        return $color;
    }

    public function getFalseColor(): ?string
    {
        $color = $this->evaluate($this->falseColor);

        if ($color instanceof Color) {
            return $color->toHex();
        }

        return $color;
    }

    public function getView(): string
    {
        return 'primix-tables::columns.icon-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'size' => $this->getSize() ?? 'md',
            'icons' => $this->getIcons(),
            'colors' => $this->getColors(),
            'isBoolean' => $this->isBoolean(),
            'trueIcon' => $this->getTrueIcon(),
            'falseIcon' => $this->getFalseIcon(),
            'trueColor' => $this->getTrueColor(),
            'falseColor' => $this->getFalseColor(),
        ]);
    }
}

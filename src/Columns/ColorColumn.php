<?php

namespace Primix\Tables\Columns;

use Closure;

class ColorColumn extends Column
{
    protected string|Closure $swatchShape = 'rounded';

    protected string|Closure|null $swatchSize = null;

    public function swatchShape(string|Closure $shape): static
    {
        $this->swatchShape = $shape;

        return $this;
    }

    public function swatchSize(string|Closure|null $size): static
    {
        $this->swatchSize = $size;

        return $this;
    }

    public function getSwatchShape(): string
    {
        return $this->evaluate($this->swatchShape);
    }

    public function getSwatchSize(): string
    {
        return $this->evaluate($this->swatchSize) ?? 'md';
    }

    public function getView(): string
    {
        return 'primix-tables::columns.color-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'swatchShape' => $this->getSwatchShape(),
            'swatchSize' => $this->getSwatchSize(),
        ]);
    }
}

<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait HasAlignment
{
    protected string|Closure|null $alignment = null;

    public function alignment(string|Closure|null $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function alignLeft(): static
    {
        return $this->alignment('left');
    }

    public function alignCenter(): static
    {
        return $this->alignment('center');
    }

    public function alignRight(): static
    {
        return $this->alignment('right');
    }

    public function getAlignment(): string
    {
        return $this->evaluate($this->alignment) ?? 'left';
    }
}

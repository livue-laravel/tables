<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait HasInlineStyle
{
    protected bool|Closure $isInlineInput = false;

    public function inlineInput(bool|Closure $condition = true): static
    {
        $this->isInlineInput = $condition;

        return $this;
    }

    public function isInlineInput(): bool
    {
        return (bool) $this->evaluate($this->isInlineInput);
    }
}

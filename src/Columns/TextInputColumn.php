<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Tables\Columns\Concerns\CanUpdateState;
use Primix\Tables\Columns\Concerns\HasInlineStyle;

class TextInputColumn extends Column
{
    use CanUpdateState;
    use HasInlineStyle;

    protected string|Closure $inputType = 'text';

    protected int|Closure|null $step = null;

    public function type(string|Closure $type): static
    {
        $this->inputType = $type;

        return $this;
    }

    public function numeric(int|Closure|null $step = null): static
    {
        $this->inputType = 'number';
        $this->step = $step;

        return $this;
    }

    public function email(): static
    {
        return $this->type('email');
    }

    public function url(): static
    {
        return $this->type('url');
    }

    public function tel(): static
    {
        return $this->type('tel');
    }

    public function step(int|Closure|null $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getInputType(): string
    {
        return $this->evaluate($this->inputType);
    }

    public function getStep(): ?int
    {
        return $this->evaluate($this->step);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.text-input-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'inputType' => $this->getInputType(),
            'editable' => $this->isEditable(),
            'step' => $this->getStep(),
            'inlineInput' => $this->isInlineInput(),
        ]);
    }
}

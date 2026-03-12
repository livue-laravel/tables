<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Support\Colors\Color;
use Primix\Tables\Columns\Concerns\CanUpdateState;
use Primix\Tables\Columns\Concerns\HasInlineStyle;

class ToggleColumn extends Column
{
    use CanUpdateState;
    use HasInlineStyle;

    protected Color|string|Closure|null $onColor = null;

    protected Color|string|Closure|null $offColor = null;

    protected string|Closure|null $onLabel = null;

    protected string|Closure|null $offLabel = null;

    protected string|Closure|null $onIcon = null;

    protected string|Closure|null $offIcon = null;

    public function onColor(Color|string|Closure|null $color): static
    {
        $this->onColor = $color;

        return $this;
    }

    public function offColor(Color|string|Closure|null $color): static
    {
        $this->offColor = $color;

        return $this;
    }

    public function onLabel(string|Closure|null $label): static
    {
        $this->onLabel = $label;

        return $this;
    }

    public function offLabel(string|Closure|null $label): static
    {
        $this->offLabel = $label;

        return $this;
    }

    public function onIcon(string|Closure|null $icon): static
    {
        $this->onIcon = $icon;

        return $this;
    }

    public function offIcon(string|Closure|null $icon): static
    {
        $this->offIcon = $icon;

        return $this;
    }

    public function getOnColor(): ?string
    {
        $color = $this->evaluate($this->onColor);

        if ($color instanceof Color) {
            return $color->toHex();
        }

        return $color;
    }

    public function getOffColor(): ?string
    {
        $color = $this->evaluate($this->offColor);

        if ($color instanceof Color) {
            return $color->toHex();
        }

        return $color;
    }

    public function getOnLabel(): ?string
    {
        return $this->evaluate($this->onLabel);
    }

    public function getOffLabel(): ?string
    {
        return $this->evaluate($this->offLabel);
    }

    public function getOnIcon(): ?string
    {
        return $this->evaluate($this->onIcon);
    }

    public function getOffIcon(): ?string
    {
        return $this->evaluate($this->offIcon);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.toggle-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'editable' => $this->isEditable(),
            'onColor' => $this->getOnColor(),
            'offColor' => $this->getOffColor(),
            'onLabel' => $this->getOnLabel(),
            'offLabel' => $this->getOffLabel(),
            'onIcon' => $this->getOnIcon(),
            'offIcon' => $this->getOffIcon(),
            'inlineInput' => $this->isInlineInput(),
        ]);
    }
}

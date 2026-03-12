<?php

namespace Primix\Tables\Columns\Concerns;

use BackedEnum;
use Primix\Support\Contracts\HasColor as HasColorContract;
use Primix\Support\Contracts\HasIcon as HasIconContract;
use Primix\Support\Contracts\HasLabel as HasLabelContract;

trait ResolvesEnumState
{
    public function getColorForState(mixed $state): ?string
    {
        $colors = $this->getColors();
        $key = $state instanceof BackedEnum ? $state->value : $state;

        if ($key !== null && array_key_exists($key, $colors)) {
            return $colors[$key];
        }

        if ($state instanceof HasColorContract) {
            return $state->getColor();
        }

        return $this->getColor();
    }

    public function getIconForState(mixed $state): ?string
    {
        $icons = $this->getIcons();
        $key = $state instanceof BackedEnum ? $state->value : $state;

        if ($key !== null && array_key_exists($key, $icons)) {
            return $icons[$key];
        }

        if ($state instanceof HasIconContract) {
            return $state->getIcon();
        }

        return null;
    }

    public function getLabelForState(mixed $state): ?string
    {
        if ($state instanceof HasLabelContract) {
            return $state->getLabel();
        }

        if ($state instanceof BackedEnum) {
            return $state->value;
        }

        return $state;
    }
}

<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait HasDescription
{
    protected string|Closure|null $description = null;

    protected string|Closure|null $descriptionPosition = 'below';

    public function description(string|Closure|null $description, ?string $position = null): static
    {
        $this->description = $description;

        if ($position !== null) {
            $this->descriptionPosition = $position;
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function getDescriptionPosition(): string
    {
        return $this->evaluate($this->descriptionPosition) ?? 'below';
    }
}

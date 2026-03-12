<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait HasWeight
{
    protected string|Closure|null $weight = null;

    public function weight(string|Closure|null $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function bold(): static
    {
        return $this->weight('bold');
    }

    public function medium(): static
    {
        return $this->weight('medium');
    }

    public function semibold(): static
    {
        return $this->weight('semibold');
    }

    public function getWeight(): ?string
    {
        return $this->evaluate($this->weight);
    }
}

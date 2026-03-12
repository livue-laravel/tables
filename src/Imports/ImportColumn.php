<?php

namespace Primix\Tables\Imports;

use Closure;
use Primix\Support\Concerns\EvaluatesClosures;
use Primix\Support\Concerns\Makeable;

class ImportColumn
{
    use EvaluatesClosures;
    use Makeable;

    protected ?string $label = null;

    protected array $rules = [];

    protected mixed $default = null;

    protected bool $hasDefault = false;

    protected ?Closure $castUsing = null;

    protected ?string $mapFrom = null;

    public function __construct(
        protected string $name,
    ) {}

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function rules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function required(): static
    {
        if (! in_array('required', $this->rules)) {
            array_unshift($this->rules, 'required');
        }

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;
        $this->hasDefault = true;

        return $this;
    }

    public function castUsing(?Closure $callback): static
    {
        $this->castUsing = $callback;

        return $this;
    }

    public function mapFrom(?string $csvHeader): static
    {
        $this->mapFrom = $csvHeader;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->name)->headline()->toString();
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    public function getMapFrom(): ?string
    {
        return $this->mapFrom;
    }

    public function resolveValue(mixed $raw): mixed
    {
        if (($raw === null || $raw === '') && $this->hasDefault) {
            $raw = $this->default;
        }

        if ($this->castUsing !== null) {
            return $this->evaluate($this->castUsing, [
                'state' => $raw,
                'value' => $raw,
            ]);
        }

        return $raw;
    }
}

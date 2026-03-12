<?php

namespace Primix\Tables\Exports;

use Closure;
use Primix\Support\Concerns\EvaluatesClosures;
use Primix\Support\Concerns\Makeable;

class ExportColumn
{
    use EvaluatesClosures;
    use Makeable;

    protected ?string $label = null;

    protected ?Closure $formatStateUsing = null;

    protected ?Closure $getStateUsing = null;

    public function __construct(
        protected string $name,
    ) {}

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

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

    public function getState(mixed $record): mixed
    {
        if ($this->getStateUsing !== null) {
            $state = $this->evaluate($this->getStateUsing, [
                'record' => $record,
            ]);
        } else {
            $state = data_get($record, $this->name);
        }

        if ($this->formatStateUsing !== null) {
            $state = $this->evaluate($this->formatStateUsing, [
                'state' => $state,
                'record' => $record,
            ]);
        }

        return $state;
    }
}

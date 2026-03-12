<?php

namespace Primix\Tables;

use Closure;
use Primix\Support\Concerns\EvaluatesClosures;
use Primix\Support\Concerns\Makeable;

class TableGroup
{
    use EvaluatesClosures;
    use Makeable;

    protected string $column;

    protected ?string $label = null;

    protected ?Closure $getTitleUsing = null;

    protected ?Closure $getDescriptionUsing = null;

    protected bool|Closure $isCollapsible = true;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getTitleUsing(?Closure $callback): static
    {
        $this->getTitleUsing = $callback;

        return $this;
    }

    public function getDescriptionUsing(?Closure $callback): static
    {
        $this->getDescriptionUsing = $callback;

        return $this;
    }

    public function collapsible(bool|Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getLabel(): string
    {
        return $this->label ?? str($this->column)->headline()->toString();
    }

    public function getTitle(mixed $value): string
    {
        if ($this->getTitleUsing !== null) {
            return ($this->getTitleUsing)($value);
        }

        return (string) ($value ?? 'N/A');
    }

    public function getDescription(mixed $value, int $count): ?string
    {
        if ($this->getDescriptionUsing !== null) {
            return ($this->getDescriptionUsing)($value, $count);
        }

        return null;
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }
}

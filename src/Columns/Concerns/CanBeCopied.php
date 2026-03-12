<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;

trait CanBeCopied
{
    protected bool|Closure $isCopyable = false;

    protected string|Closure|null $copyMessage = null;

    protected int|Closure $copyMessageDuration = 2000;

    public function copyable(bool|Closure $condition = true): static
    {
        $this->isCopyable = $condition;

        return $this;
    }

    public function copyMessage(string|Closure|null $message): static
    {
        $this->copyMessage = $message;

        return $this;
    }

    public function copyMessageDuration(int|Closure $duration): static
    {
        $this->copyMessageDuration = $duration;

        return $this;
    }

    public function isCopyable(): bool
    {
        return (bool) $this->evaluate($this->isCopyable);
    }

    public function getCopyMessage(): string
    {
        return $this->evaluate($this->copyMessage) ?? 'Copied!';
    }

    public function getCopyMessageDuration(): int
    {
        return (int) $this->evaluate($this->copyMessageDuration);
    }
}

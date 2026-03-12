<?php

namespace Primix\Tables\Concerns;

use Closure;

trait HasGridLayout
{
    protected string|Closure $layout = 'table';

    protected int|Closure $gridColumns = 3;

    protected bool|Closure $isVirtualScroll = false;

    protected int|Closure $virtualScrollItemSize = 46;

    public function grid(int|Closure $columns = 3): static
    {
        $this->layout = 'grid';
        $this->gridColumns = $columns;

        return $this;
    }

    public function getLayout(): string
    {
        return $this->evaluate($this->layout);
    }

    public function getGridColumns(): int
    {
        return (int) $this->evaluate($this->gridColumns);
    }

    public function virtualScroll(bool|Closure $condition = true, int $itemSize = 46): static
    {
        $this->isVirtualScroll = $condition;
        $this->virtualScrollItemSize = $itemSize;

        return $this;
    }

    public function isVirtualScroll(): bool
    {
        return (bool) $this->evaluate($this->isVirtualScroll);
    }

    public function getVirtualScrollItemSize(): int
    {
        return (int) $this->evaluate($this->virtualScrollItemSize);
    }
}

<?php

namespace Primix\Tables\Concerns;

use Closure;

trait HasGridLayout
{
    protected string|Closure $layout = 'table';

    protected int|Closure $gridColumns = 3;

    protected bool|Closure $isLayoutSwitchable = false;

    protected bool|Closure $isVirtualScroll = false;

    protected int|Closure $virtualScrollItemSize = 46;

    public function layout(string|Closure $layout): static
    {
        if (is_string($layout) && ! in_array($layout, ['table', 'grid'], true)) {
            throw new \InvalidArgumentException("Unsupported table layout [{$layout}]. Supported layouts are [table, grid].");
        }

        $this->layout = $layout;

        return $this;
    }

    public function tableLayout(): static
    {
        return $this->layout('table');
    }

    public function grid(int|Closure $columns = 3): static
    {
        $this->layout('grid');
        $this->gridColumns = $columns;

        return $this;
    }

    public function getLayout(): string
    {
        $layout = (string) $this->evaluate($this->layout);

        return in_array($layout, ['table', 'grid'], true) ? $layout : 'table';
    }

    public function getGridColumns(): int
    {
        return (int) $this->evaluate($this->gridColumns);
    }

    public function switchableLayout(bool|Closure $condition = true): static
    {
        $this->isLayoutSwitchable = $condition;

        return $this;
    }

    public function isLayoutSwitchable(): bool
    {
        return (bool) $this->evaluate($this->isLayoutSwitchable);
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

<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Support\Concerns\HasSize;

class ImageColumn extends Column
{
    use HasSize;

    protected string|Closure $shape = 'rounded';

    protected string|Closure|null $height = null;

    protected string|Closure|null $disk = null;

    protected string|Closure|null $defaultImageUrl = null;

    protected bool|Closure $isStacked = false;

    protected int|Closure $stackLimit = 3;

    protected int|Closure|null $stackOverlap = null;

    public function circular(): static
    {
        $this->shape = 'circular';

        return $this;
    }

    public function rounded(): static
    {
        $this->shape = 'rounded';

        return $this;
    }

    public function square(): static
    {
        $this->shape = 'square';

        return $this;
    }

    public function height(string|Closure|null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function disk(string|Closure|null $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function defaultImageUrl(string|Closure|null $url): static
    {
        $this->defaultImageUrl = $url;

        return $this;
    }

    public function stacked(bool|Closure $condition = true, int|Closure $limit = 3): static
    {
        $this->isStacked = $condition;
        $this->stackLimit = $limit;

        return $this;
    }

    public function stackOverlap(int|Closure|null $overlap): static
    {
        $this->stackOverlap = $overlap;

        return $this;
    }

    public function getShape(): string
    {
        return $this->evaluate($this->shape);
    }

    public function getHeight(): ?string
    {
        return $this->evaluate($this->height);
    }

    public function getDisk(): ?string
    {
        return $this->evaluate($this->disk);
    }

    public function getDefaultImageUrl(): ?string
    {
        return $this->evaluate($this->defaultImageUrl);
    }

    public function isStacked(): bool
    {
        return (bool) $this->evaluate($this->isStacked);
    }

    public function getStackLimit(): int
    {
        return (int) $this->evaluate($this->stackLimit);
    }

    public function getStackOverlap(): ?int
    {
        return $this->evaluate($this->stackOverlap);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.image-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'shape' => $this->getShape(),
            'height' => $this->getHeight(),
            'size' => $this->getSize(),
            'disk' => $this->getDisk(),
            'defaultImageUrl' => $this->getDefaultImageUrl(),
            'isStacked' => $this->isStacked(),
            'stackLimit' => $this->getStackLimit(),
            'stackOverlap' => $this->getStackOverlap(),
        ]);
    }
}

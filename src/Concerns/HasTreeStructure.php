<?php

namespace Primix\Tables\Concerns;

use Closure;

trait HasTreeStructure
{
    protected bool|Closure $isTree = false;

    protected ?string $childrenRelationship = null;

    protected ?string $parentKeyColumn = null;

    public function tree(string $childrenRelationship = 'children', ?string $parentKeyColumn = 'parent_id'): static
    {
        $this->isTree = true;
        $this->childrenRelationship = $childrenRelationship;
        $this->parentKeyColumn = $parentKeyColumn;

        return $this;
    }

    public function isTree(): bool
    {
        return (bool) $this->evaluate($this->isTree);
    }

    public function getChildrenRelationship(): ?string
    {
        return $this->childrenRelationship;
    }

    public function getParentKeyColumn(): ?string
    {
        return $this->parentKeyColumn;
    }
}

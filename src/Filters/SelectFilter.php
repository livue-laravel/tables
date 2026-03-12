<?php

namespace Primix\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Primix\Forms\Concerns\HasOptions;

class SelectFilter extends Filter
{
    use HasOptions;

    protected bool|Closure $isMultiple = false;

    protected bool|Closure $isSearchable = false;

    protected bool|Closure $isNative = false;

    protected ?string $relationship = null;

    protected ?string $relationshipTitleAttribute = null;

    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function searchable(bool|Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function native(bool|Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function relationship(string $name, string $titleAttribute): static
    {
        $this->relationship = $name;
        $this->relationshipTitleAttribute = $titleAttribute;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return $query;
        }

        if ($this->relationship) {
            return $query->whereHas($this->relationship, function (Builder $query) use ($value) {
                if (is_array($value)) {
                    $query->whereIn('id', $value);
                } else {
                    $query->where('id', $value);
                }
            });
        }

        if (is_array($value)) {
            return $query->whereIn($this->getName(), $value);
        }

        return $query->where($this->getName(), $value);
    }

    public function getView(): string
    {
        return 'primix-tables::filters.select-filter';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'options' => $this->getOptions(),
            'multiple' => $this->isMultiple(),
            'searchable' => $this->isSearchable(),
            'native' => $this->isNative(),
        ]);
    }
}

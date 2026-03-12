<?php

namespace Primix\Tables\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Primix\Support\Components\Schema\Component;
use Primix\Support\Concerns\HasSchemaComponentIdentifier;
use Primix\Tables\Concerns\HasName;

abstract class Filter extends Component
{
    use HasName;
    use HasSchemaComponentIdentifier;

    protected static ?string $schemaComponentCategory = 'filter';

    protected ?Closure $query = null;

    protected mixed $defaultValue = null;

    public static function make(string $name): static
    {
        $instance = new static();
        $instance->name($name);
        $instance->label(str($name)->headline()->toString());
        $instance->configure();

        return $instance;
    }

    public function query(?Closure $callback): static
    {
        $this->query = $callback;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function apply(Builder $query, mixed $value): Builder
    {
        if ($this->query !== null) {
            return ($this->query)($query, $value);
        }

        if ($value !== null && $value !== '') {
            return $query->where($this->getName(), $value);
        }

        return $query;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    abstract public function getView(): string;
}

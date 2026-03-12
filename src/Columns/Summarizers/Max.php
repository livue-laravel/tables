<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Max extends Summarizer
{
    public function getDefaultLabel(): string
    {
        return 'Max';
    }

    public function calculate(Builder $query, string $column): mixed
    {
        return $query->max($column);
    }
}

<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Min extends Summarizer
{
    public function getDefaultLabel(): string
    {
        return 'Min';
    }

    public function calculate(Builder $query, string $column): mixed
    {
        return $query->min($column);
    }
}

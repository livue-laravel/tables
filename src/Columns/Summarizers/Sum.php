<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Sum extends Summarizer
{
    public function getDefaultLabel(): string
    {
        return 'Sum';
    }

    public function calculate(Builder $query, string $column): mixed
    {
        return $query->sum($column);
    }
}

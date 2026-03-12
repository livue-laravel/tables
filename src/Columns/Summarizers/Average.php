<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Average extends Summarizer
{
    public function getDefaultLabel(): string
    {
        return 'Average';
    }

    public function calculate(Builder $query, string $column): mixed
    {
        return $query->avg($column);
    }
}

<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Count extends Summarizer
{
    public function getDefaultLabel(): string
    {
        return 'Count';
    }

    public function calculate(Builder $query, string $column): mixed
    {
        return $query->count();
    }
}

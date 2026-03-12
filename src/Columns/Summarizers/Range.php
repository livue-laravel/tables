<?php

namespace Primix\Tables\Columns\Summarizers;

use Illuminate\Database\Eloquent\Builder;

class Range extends Summarizer
{
    protected string $separator = ' - ';

    public function getDefaultLabel(): string
    {
        return 'Range';
    }

    public function separator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }

    public function calculate(Builder $query, string $column): mixed
    {
        $min = (clone $query)->min($column);
        $max = (clone $query)->max($column);

        if ($min === null && $max === null) {
            return null;
        }

        return $min . $this->separator . $max;
    }
}

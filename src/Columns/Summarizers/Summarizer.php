<?php

namespace Primix\Tables\Columns\Summarizers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Primix\Support\Concerns\EvaluatesClosures;
use Primix\Support\Concerns\Makeable;

abstract class Summarizer
{
    use EvaluatesClosures;
    use Makeable;

    protected ?string $label = null;

    protected ?Closure $formatStateUsing = null;

    protected ?Closure $using = null;

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->getDefaultLabel();
    }

    abstract public function getDefaultLabel(): string;

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function using(?Closure $callback): static
    {
        $this->using = $callback;

        return $this;
    }

    public function numeric(int $decimals = 0, string $decimalSeparator = ',', string $thousandsSeparator = '.'): static
    {
        $this->formatStateUsing = function (mixed $state) use ($decimals, $decimalSeparator, $thousandsSeparator): string {
            if ($state === null) {
                return '-';
            }

            return number_format((float) $state, $decimals, $decimalSeparator, $thousandsSeparator);
        };

        return $this;
    }

    public function money(string $currency = 'EUR', string $locale = 'it'): static
    {
        $this->formatStateUsing = function (mixed $state) use ($currency, $locale): string {
            if ($state === null) {
                return '-';
            }

            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

            return $formatter->formatCurrency((float) $state, $currency);
        };

        return $this;
    }

    /**
     * Calculate the aggregate value from the query.
     */
    abstract public function calculate(Builder $query, string $column): mixed;

    /**
     * Resolve the summarizer: calculate and format the value.
     *
     * @return array{label: string, value: string|null}
     */
    public function resolve(Builder $query, string $column): array
    {
        if ($this->using !== null) {
            $value = $this->evaluate($this->using, [
                'query' => $query,
                'column' => $column,
            ]);
        } else {
            $value = $this->calculate($query, $column);
        }

        if ($this->formatStateUsing !== null) {
            $value = $this->evaluate($this->formatStateUsing, [
                'state' => $value,
                'value' => $value,
            ]);
        }

        return [
            'label' => $this->getLabel(),
            'value' => $value,
        ];
    }
}

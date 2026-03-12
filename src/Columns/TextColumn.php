<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Tables\Columns\Concerns\HasDescription;
use Primix\Tables\Columns\Concerns\HasWeight;

class TextColumn extends Column
{
    use HasDescription;
    use HasWeight;

    protected int|Closure|null $characterLimit = null;

    protected int|Closure|null $wordLimit = null;

    protected bool|Closure $isMarkdown = false;

    protected bool|Closure $isHtml = false;

    protected bool|Closure $isBadge = false;

    protected bool|Closure $isMoney = false;

    protected bool|Closure $isNumeric = false;

    protected ?string $currency = null;

    protected ?string $moneyLocale = null;

    protected ?string $dateFormat = null;

    protected ?string $timeFormat = null;

    protected ?string $timezone = null;

    protected string|Closure|null $url = null;

    protected bool|Closure $shouldOpenUrlInNewTab = false;

    public function limit(int|Closure|null $length): static
    {
        $this->characterLimit = $length;

        return $this;
    }

    public function words(int|Closure|null $words): static
    {
        $this->wordLimit = $words;

        return $this;
    }

    public function markdown(bool|Closure $condition = true): static
    {
        $this->isMarkdown = $condition;

        return $this;
    }

    public function html(bool|Closure $condition = true): static
    {
        $this->isHtml = $condition;

        return $this;
    }

    public function badge(bool|Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function money(?string $currency = null, ?string $locale = null): static
    {
        $this->isMoney = true;
        $this->currency = $currency ?? 'USD';
        $this->moneyLocale = $locale;

        $this->formatStateUsing(function ($state) {
            if (! is_numeric($state)) {
                return $state;
            }

            return $this->formatCurrency((float) $state);
        });

        return $this;
    }

    public function numeric(int $decimalPlaces = 0, ?string $decimalSeparator = '.', ?string $thousandsSeparator = ','): static
    {
        $this->isNumeric = true;

        $this->formatStateUsing(function ($state) use ($decimalPlaces, $decimalSeparator, $thousandsSeparator) {
            if (! is_numeric($state)) {
                return $state;
            }

            return number_format((float) $state, $decimalPlaces, $decimalSeparator, $thousandsSeparator);
        });

        return $this;
    }

    public function date(?string $format = null, ?string $timezone = null): static
    {
        $this->dateFormat = $format ?? 'M j, Y';
        $this->timezone = $timezone;

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return null;
            }

            $date = \Carbon\Carbon::parse($state);

            if ($this->timezone !== null) {
                $date = $date->setTimezone($this->timezone);
            }

            return $date->format($this->dateFormat ?? 'M j, Y');
        });

        return $this;
    }

    public function dateTime(?string $format = null, ?string $timezone = null): static
    {
        $this->dateFormat = $format ?? 'M j, Y H:i';
        $this->timezone = $timezone;

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return null;
            }

            $date = \Carbon\Carbon::parse($state);

            if ($this->timezone !== null) {
                $date = $date->setTimezone($this->timezone);
            }

            return $date->format($this->dateFormat ?? 'M j, Y H:i');
        });

        return $this;
    }

    public function time(?string $format = null, ?string $timezone = null): static
    {
        $this->timeFormat = $format ?? 'H:i';
        $this->timezone = $timezone;

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return null;
            }

            $date = \Carbon\Carbon::parse($state);

            if ($this->timezone !== null) {
                $date = $date->setTimezone($this->timezone);
            }

            return $date->format($this->timeFormat ?? 'H:i');
        });

        return $this;
    }

    public function since(?string $timezone = null): static
    {
        $this->timezone = $timezone;

        $this->formatStateUsing(function ($state) {
            if (! $state) {
                return null;
            }

            return \Carbon\Carbon::parse($state)->diffForHumans();
        });

        return $this;
    }

    public function url(string|Closure|null $url, bool|Closure $shouldOpenInNewTab = false): static
    {
        $this->url = $url;
        $this->shouldOpenUrlInNewTab = $shouldOpenInNewTab;

        return $this;
    }

    public function openUrlInNewTab(bool|Closure $condition = true): static
    {
        $this->shouldOpenUrlInNewTab = $condition;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->evaluate($this->url);
    }

    public function shouldOpenUrlInNewTab(): bool
    {
        return (bool) $this->evaluate($this->shouldOpenUrlInNewTab);
    }

    public function getCharacterLimit(): ?int
    {
        return $this->evaluate($this->characterLimit);
    }

    public function getWordLimit(): ?int
    {
        return $this->evaluate($this->wordLimit);
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.text-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'weight' => $this->getWeight(),
            'description' => $this->getDescription(),
            'descriptionPosition' => $this->getDescriptionPosition(),
            'characterLimit' => $this->getCharacterLimit(),
            'wordLimit' => $this->getWordLimit(),
            'isBadge' => $this->isBadge(),
            'dateFormat' => $this->dateFormat,
            'timeFormat' => $this->timeFormat,
        ]);
    }

    protected function formatCurrency(float $amount): string
    {
        $currency = $this->currency ?? 'USD';
        $locale = $this->moneyLocale ?? app()->getLocale();
        $locale = str_replace('-', '_', $locale);

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

            $formatted = $formatter->formatCurrency($amount, $currency);

            if ($formatted !== false) {
                return $formatted;
            }
        }

        return $currency . ' ' . number_format($amount, 2, '.', ',');
    }
}

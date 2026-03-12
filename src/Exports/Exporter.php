<?php

namespace Primix\Tables\Exports;

use Closure;

abstract class Exporter
{
    protected static ?string $model = null;

    /**
     * @return array<ExportColumn>
     */
    abstract public static function getColumns(): array;

    public static function getModel(): ?string
    {
        return static::$model;
    }

    public static function getFileName(): ?string
    {
        return null;
    }

    public static function getDelimiter(): string
    {
        return ',';
    }

    public static function withHeader(): bool
    {
        return true;
    }

    public static function modifyQueryUsing(): ?Closure
    {
        return null;
    }
}

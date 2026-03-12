<?php

namespace Primix\Tables\Imports;

use Closure;

abstract class Importer
{
    protected static ?string $model = null;

    /**
     * @return array<ImportColumn>
     */
    abstract public static function getColumns(): array;

    public static function getModel(): ?string
    {
        return static::$model;
    }

    public static function getDelimiter(): string
    {
        return ',';
    }

    public static function getHandleRecordCreation(): ?Closure
    {
        return null;
    }

    public static function beforeImport(): void
    {
        //
    }

    public static function afterImport(int $created, array $errors): void
    {
        //
    }
}

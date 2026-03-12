<?php

use Primix\Tables\Imports\Importer;
use Primix\Tables\Imports\ImportColumn;

it('requires getColumns to be implemented', function () {
    expect(Importer::class)->toBeAbstract();
});

it('returns null model by default', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($importer::getModel())->toBeNull();
});

it('returns model when set', function () {
    $importer = new class extends Importer {
        protected static ?string $model = 'App\\Models\\Product';

        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($importer::getModel())->toBe('App\\Models\\Product');
});

it('returns columns from getColumns', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [
                ImportColumn::make('name')->required(),
                ImportColumn::make('price')->rules(['numeric']),
            ];
        }
    };

    $columns = $importer::getColumns();

    expect($columns)->toHaveCount(2);
    expect($columns[0]->getName())->toBe('name');
    expect($columns[1]->getName())->toBe('price');
});

it('returns comma delimiter by default', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($importer::getDelimiter())->toBe(',');
});

it('can override delimiter', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }

        public static function getDelimiter(): string
        {
            return ';';
        }
    };

    expect($importer::getDelimiter())->toBe(';');
});

it('returns null handleRecordCreation by default', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($importer::getHandleRecordCreation())->toBeNull();
});

it('can override handleRecordCreation', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }

        public static function getHandleRecordCreation(): ?\Closure
        {
            return fn (array $data) => null;
        }
    };

    expect($importer::getHandleRecordCreation())->toBeInstanceOf(Closure::class);
});

it('has empty beforeImport hook by default', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }
    };

    // Should not throw
    $importer::beforeImport();
    expect(true)->toBeTrue();
});

it('has empty afterImport hook by default', function () {
    $importer = new class extends Importer {
        public static function getColumns(): array
        {
            return [];
        }
    };

    // Should not throw
    $importer::afterImport(0, []);
    expect(true)->toBeTrue();
});

it('can override beforeImport hook', function () {
    $called = false;

    $importer = new class extends Importer {
        public static bool $hookCalled = false;

        public static function getColumns(): array
        {
            return [];
        }

        public static function beforeImport(): void
        {
            static::$hookCalled = true;
        }
    };

    $importer::beforeImport();

    expect($importer::$hookCalled)->toBeTrue();
});

it('can override afterImport hook', function () {
    $importer = new class extends Importer {
        public static int $created = 0;
        public static array $errors = [];

        public static function getColumns(): array
        {
            return [];
        }

        public static function afterImport(int $created, array $errors): void
        {
            static::$created = $created;
            static::$errors = $errors;
        }
    };

    $importer::afterImport(5, [['row' => 3, 'message' => 'Invalid']]);

    expect($importer::$created)->toBe(5);
    expect($importer::$errors)->toHaveCount(1);
});

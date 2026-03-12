<?php

use Primix\Tables\Exports\Exporter;
use Primix\Tables\Exports\ExportColumn;

it('requires getColumns to be implemented', function () {
    expect(Exporter::class)->toBeAbstract();
});

it('returns null model by default', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::getModel())->toBeNull();
});

it('returns model when set', function () {
    $exporter = new class extends Exporter {
        protected static ?string $model = 'App\\Models\\Product';

        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::getModel())->toBe('App\\Models\\Product');
});

it('returns columns from getColumns', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [
                ExportColumn::make('name'),
                ExportColumn::make('price'),
            ];
        }
    };

    $columns = $exporter::getColumns();

    expect($columns)->toHaveCount(2);
    expect($columns[0]->getName())->toBe('name');
    expect($columns[1]->getName())->toBe('price');
});

it('returns null fileName by default', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::getFileName())->toBeNull();
});

it('can override getFileName', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }

        public static function getFileName(): ?string
        {
            return 'products-export';
        }
    };

    expect($exporter::getFileName())->toBe('products-export');
});

it('returns comma delimiter by default', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::getDelimiter())->toBe(',');
});

it('can override delimiter', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }

        public static function getDelimiter(): string
        {
            return ';';
        }
    };

    expect($exporter::getDelimiter())->toBe(';');
});

it('returns true for withHeader by default', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::withHeader())->toBeTrue();
});

it('can override withHeader', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }

        public static function withHeader(): bool
        {
            return false;
        }
    };

    expect($exporter::withHeader())->toBeFalse();
});

it('returns null modifyQueryUsing by default', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    expect($exporter::modifyQueryUsing())->toBeNull();
});

it('can override modifyQueryUsing', function () {
    $exporter = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }

        public static function modifyQueryUsing(): ?\Closure
        {
            return fn ($query) => $query->where('active', true);
        }
    };

    expect($exporter::modifyQueryUsing())->toBeInstanceOf(Closure::class);
});

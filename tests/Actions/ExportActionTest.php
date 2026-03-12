<?php

use Primix\Tables\Actions\ExportAction;
use Primix\Tables\Exports\ExportColumn;
use Primix\Tables\Exports\Exporter;
use Primix\Forms\Components\Fields\CheckboxList;

it('has default name export', function () {
    $action = ExportAction::make();

    expect($action->getName())->toBe('export');
});

it('has default label, icon, and color', function () {
    $action = ExportAction::make();

    expect($action->getLabel())->toBe('Export');
    expect($action->getIcon())->toBe('heroicon-o-arrow-down-tray');
    expect($action->getColor())->toBe('gray');
});

it('is modal by default', function () {
    $action = ExportAction::make();

    expect($action->isModal())->toBeTrue();
});

it('can set export columns', function () {
    $columns = [
        ExportColumn::make('name'),
        ExportColumn::make('email'),
    ];

    $action = ExportAction::make()
        ->exportColumns($columns);

    expect($action->getExportColumns())->toHaveCount(2);
    expect($action->getExportColumns()[0]->getName())->toBe('name');
    expect($action->getExportColumns()[1]->getName())->toBe('email');
});

it('can set file name and auto-appends csv extension', function () {
    $action = ExportAction::make()
        ->fileName('my-export');

    expect($action->getFileName())->toBe('my-export.csv');
});

it('does not double-append csv extension', function () {
    $action = ExportAction::make()
        ->fileName('my-export.csv');

    expect($action->getFileName())->toBe('my-export.csv');
});

it('generates default file name with timestamp', function () {
    $action = ExportAction::make();

    $fileName = $action->getFileName();

    expect($fileName)->toStartWith('export-');
    expect($fileName)->toEndWith('.csv');
    // Format: export-YYYY-MM-DD-HHmmss.csv
    expect($fileName)->toMatch('/^export-\d{4}-\d{2}-\d{2}-\d{6}\.csv$/');
});

it('can set delimiter', function () {
    $action = ExportAction::make()
        ->delimiter(';');

    // We can verify the action was configured by checking it does not throw
    expect($action)->toBeInstanceOf(ExportAction::class);
});

it('can set withoutHeader', function () {
    $action = ExportAction::make()
        ->withoutHeader();

    expect($action)->toBeInstanceOf(ExportAction::class);
});

it('getFormSchema returns array with CheckboxList', function () {
    $action = ExportAction::make()
        ->exportColumns([
            ExportColumn::make('name'),
            ExportColumn::make('email'),
        ]);

    $schema = $action->getFormSchema();

    expect($schema)->toBeArray();
    expect($schema)->toHaveCount(1);
    expect($schema[0])->toBeInstanceOf(CheckboxList::class);
});

it('can set exporter class', function () {
    $exporterClass = new class extends Exporter {
        public static function getColumns(): array
        {
            return [
                ExportColumn::make('name'),
                ExportColumn::make('price'),
            ];
        }
    };

    $action = ExportAction::make()
        ->exporter($exporterClass::class);

    expect($action->getExporterClass())->toBe($exporterClass::class);
});

it('resolves columns from exporter class', function () {
    $exporterClass = new class extends Exporter {
        public static function getColumns(): array
        {
            return [
                ExportColumn::make('title'),
                ExportColumn::make('description'),
                ExportColumn::make('price'),
            ];
        }
    };

    $action = ExportAction::make()
        ->exporter($exporterClass::class);

    $columns = $action->getExportColumns();

    expect($columns)->toHaveCount(3);
    expect($columns[0]->getName())->toBe('title');
    expect($columns[1]->getName())->toBe('description');
    expect($columns[2]->getName())->toBe('price');
});

it('exporter class columns take priority over inline columns', function () {
    $exporterClass = new class extends Exporter {
        public static function getColumns(): array
        {
            return [
                ExportColumn::make('from_exporter'),
            ];
        }
    };

    $action = ExportAction::make()
        ->exporter($exporterClass::class)
        ->exportColumns([
            ExportColumn::make('inline_column'),
        ]);

    $columns = $action->getExportColumns();

    expect($columns)->toHaveCount(1);
    expect($columns[0]->getName())->toBe('from_exporter');
});

it('resolves file name from exporter class', function () {
    $exporterClass = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }

        public static function getFileName(): ?string
        {
            return 'products-export';
        }
    };

    $action = ExportAction::make()
        ->exporter($exporterClass::class);

    expect($action->getFileName())->toBe('products-export.csv');
});

it('falls back to default file name when exporter returns null', function () {
    $exporterClass = new class extends Exporter {
        public static function getColumns(): array
        {
            return [];
        }
    };

    $action = ExportAction::make()
        ->exporter($exporterClass::class);

    expect($action->getFileName())->toMatch('/^export-\d{4}-\d{2}-\d{2}-\d{6}\.csv$/');
});

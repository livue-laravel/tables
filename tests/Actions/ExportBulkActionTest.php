<?php

use Primix\Tables\Actions\ExportBulkAction;
use Primix\Tables\Exports\ExportColumn;
use Primix\Tables\Exports\Exporter;
use Primix\Forms\Components\Fields\CheckboxList;

it('has default name export', function () {
    $action = ExportBulkAction::make();

    expect($action->getName())->toBe('export');
});

it('has default label Export selected, icon, and color', function () {
    $action = ExportBulkAction::make();

    expect($action->getLabel())->toBe('Export selected');
    expect($action->getIcon())->toBe('heroicon-o-arrow-down-tray');
    expect($action->getColor())->toBe('gray');
});

it('is modal by default', function () {
    $action = ExportBulkAction::make();

    expect($action->isModal())->toBeTrue();
});

it('can set export columns', function () {
    $columns = [
        ExportColumn::make('name'),
        ExportColumn::make('email'),
        ExportColumn::make('status'),
    ];

    $action = ExportBulkAction::make()
        ->exportColumns($columns);

    expect($action->getExportColumns())->toHaveCount(3);
    expect($action->getExportColumns()[0]->getName())->toBe('name');
    expect($action->getExportColumns()[1]->getName())->toBe('email');
    expect($action->getExportColumns()[2]->getName())->toBe('status');
});

it('can set file name', function () {
    $action = ExportBulkAction::make()
        ->fileName('selected-records');

    expect($action->getFileName())->toBe('selected-records.csv');
});

it('getFormSchema returns array with CheckboxList', function () {
    $action = ExportBulkAction::make()
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

    $action = ExportBulkAction::make()
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
            ];
        }
    };

    $action = ExportBulkAction::make()
        ->exporter($exporterClass::class);

    $columns = $action->getExportColumns();

    expect($columns)->toHaveCount(2);
    expect($columns[0]->getName())->toBe('title');
    expect($columns[1]->getName())->toBe('description');
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

    $action = ExportBulkAction::make()
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
            return 'bulk-export';
        }
    };

    $action = ExportBulkAction::make()
        ->exporter($exporterClass::class);

    expect($action->getFileName())->toBe('bulk-export.csv');
});

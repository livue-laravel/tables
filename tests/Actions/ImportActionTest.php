<?php

use Primix\Tables\Actions\ImportAction;
use Primix\Tables\Imports\ImportColumn;
use Primix\Tables\Imports\Importer;

it('has default name import', function () {
    $action = ImportAction::make();

    expect($action->getName())->toBe('import');
});

it('has default label, icon, and color', function () {
    $action = ImportAction::make();

    expect($action->getLabel())->toBe('Import');
    expect($action->getIcon())->toBe('heroicon-o-arrow-up-tray');
    expect($action->getColor())->toBe('gray');
});

it('is modal by default', function () {
    $action = ImportAction::make();

    expect($action->isModal())->toBeTrue();
});

it('can set import columns', function () {
    $columns = [
        ImportColumn::make('name'),
        ImportColumn::make('email'),
    ];

    $action = ImportAction::make()
        ->importColumns($columns);

    expect($action)->toBeInstanceOf(ImportAction::class);
});

it('getImportColumns returns set columns', function () {
    $columns = [
        ImportColumn::make('name'),
        ImportColumn::make('email'),
    ];

    $action = ImportAction::make()
        ->importColumns($columns);

    expect($action->getImportColumns())->toHaveCount(2);
    expect($action->getImportColumns()[0]->getName())->toBe('name');
    expect($action->getImportColumns()[1]->getName())->toBe('email');
});

it('can set delimiter', function () {
    $action = ImportAction::make()
        ->delimiter(';');

    expect($action)->toBeInstanceOf(ImportAction::class);
});

it('can set handleRecordCreation callback', function () {
    $action = ImportAction::make()
        ->handleRecordCreation(fn (array $data) => null);

    expect($action)->toBeInstanceOf(ImportAction::class);
});

it('can set beforeImport callback', function () {
    $action = ImportAction::make()
        ->beforeImport(fn () => null);

    expect($action)->toBeInstanceOf(ImportAction::class);
});

it('can set afterImport callback', function () {
    $action = ImportAction::make()
        ->afterImport(fn (int $created, array $errors) => null);

    expect($action)->toBeInstanceOf(ImportAction::class);
});

it('has modal width lg by default', function () {
    $action = ImportAction::make();

    expect($action->getModalWidth())->toBe('lg');
});

it('can set importer class', function () {
    $importerClass = new class extends Importer {
        public static function getColumns(): array
        {
            return [
                ImportColumn::make('name'),
                ImportColumn::make('price'),
            ];
        }
    };

    $action = ImportAction::make()
        ->importer($importerClass::class);

    expect($action->getImporterClass())->toBe($importerClass::class);
});

it('resolves columns from importer class', function () {
    $importerClass = new class extends Importer {
        public static function getColumns(): array
        {
            return [
                ImportColumn::make('name')->required(),
                ImportColumn::make('email'),
                ImportColumn::make('price')->rules(['numeric']),
            ];
        }
    };

    $action = ImportAction::make()
        ->importer($importerClass::class);

    $columns = $action->getImportColumns();

    expect($columns)->toHaveCount(3);
    expect($columns[0]->getName())->toBe('name');
    expect($columns[1]->getName())->toBe('email');
    expect($columns[2]->getName())->toBe('price');
});

it('importer class columns take priority over inline columns', function () {
    $importerClass = new class extends Importer {
        public static function getColumns(): array
        {
            return [
                ImportColumn::make('from_importer'),
            ];
        }
    };

    $action = ImportAction::make()
        ->importer($importerClass::class)
        ->importColumns([
            ImportColumn::make('inline_column'),
        ]);

    $columns = $action->getImportColumns();

    expect($columns)->toHaveCount(1);
    expect($columns[0]->getName())->toBe('from_importer');
});

it('returns inline columns when no importer class is set', function () {
    $action = ImportAction::make()
        ->importColumns([
            ImportColumn::make('name'),
            ImportColumn::make('email'),
        ]);

    $columns = $action->getImportColumns();

    expect($columns)->toHaveCount(2);
    expect($columns[0]->getName())->toBe('name');
    expect($columns[1]->getName())->toBe('email');
});

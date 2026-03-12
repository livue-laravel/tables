<?php

use Primix\Tables\Exports\ExportColumn;

it('can be created with make', function () {
    $column = ExportColumn::make('title');

    expect($column)->toBeInstanceOf(ExportColumn::class);
    expect($column->getName())->toBe('title');
});

it('auto-generates label from name', function () {
    $column = ExportColumn::make('first_name');

    expect($column->getLabel())->toBe('First Name');
});

it('can set custom label', function () {
    $column = ExportColumn::make('title')->label('Post Title');

    expect($column->getLabel())->toBe('Post Title');
});

it('gets state from record using dot notation', function () {
    $column = ExportColumn::make('name');

    $record = (object) ['name' => 'John Doe'];
    $state = $column->getState($record);

    expect($state)->toBe('John Doe');
});

it('can use getStateUsing callback', function () {
    $column = ExportColumn::make('full_name')
        ->getStateUsing(fn ($record) => $record->first . ' ' . $record->last);

    $record = (object) ['first' => 'John', 'last' => 'Doe'];

    expect($column->getState($record))->toBe('John Doe');
});

it('can use formatStateUsing callback', function () {
    $column = ExportColumn::make('name')
        ->formatStateUsing(fn ($state) => strtoupper($state));

    $record = (object) ['name' => 'john'];

    expect($column->getState($record))->toBe('JOHN');
});

it('formatStateUsing receives state and record', function () {
    $column = ExportColumn::make('price')
        ->formatStateUsing(fn ($state, $record) => $record->currency . ' ' . number_format($state, 2));

    $record = (object) ['price' => 99.9, 'currency' => 'USD'];

    expect($column->getState($record))->toBe('USD 99.90');
});

it('returns null for missing property', function () {
    $column = ExportColumn::make('nonexistent');

    $record = (object) ['name' => 'John'];

    expect($column->getState($record))->toBeNull();
});

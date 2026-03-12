<?php

use Primix\Tables\Imports\ImportColumn;

it('can be created with make', function () {
    $column = ImportColumn::make('title');

    expect($column)->toBeInstanceOf(ImportColumn::class);
    expect($column->getName())->toBe('title');
});

it('auto-generates label from name', function () {
    $column = ImportColumn::make('first_name');

    expect($column->getLabel())->toBe('First Name');
});

it('can set custom label', function () {
    $column = ImportColumn::make('title')->label('Post Title');

    expect($column->getLabel())->toBe('Post Title');
});

it('can set rules', function () {
    $column = ImportColumn::make('email')->rules(['email', 'max:255']);

    expect($column->getRules())->toBe(['email', 'max:255']);
});

it('required adds required rule to front', function () {
    $column = ImportColumn::make('email')
        ->rules(['email', 'max:255'])
        ->required();

    expect($column->getRules())->toBe(['required', 'email', 'max:255']);
});

it('can set default value', function () {
    $column = ImportColumn::make('status')->default('draft');

    expect($column->getDefault())->toBe('draft');
});

it('hasDefault is false by default', function () {
    $column = ImportColumn::make('title');

    expect($column->hasDefault())->toBeFalse();
});

it('hasDefault is true when default is set', function () {
    $column = ImportColumn::make('status')->default('active');

    expect($column->hasDefault())->toBeTrue();
});

it('can set castUsing', function () {
    $column = ImportColumn::make('price')
        ->castUsing(fn ($state) => (float) $state);

    expect($column)->toBeInstanceOf(ImportColumn::class);
});

it('can set mapFrom', function () {
    $column = ImportColumn::make('email')->mapFrom('Email Address');

    expect($column->getMapFrom())->toBe('Email Address');
});

it('resolveValue returns raw value when no cast', function () {
    $column = ImportColumn::make('name');

    expect($column->resolveValue('John'))->toBe('John');
});

it('resolveValue applies default when value is null', function () {
    $column = ImportColumn::make('status')->default('draft');

    expect($column->resolveValue(null))->toBe('draft');
});

it('resolveValue applies default when value is empty string', function () {
    $column = ImportColumn::make('status')->default('draft');

    expect($column->resolveValue(''))->toBe('draft');
});

it('resolveValue applies castUsing callback', function () {
    $column = ImportColumn::make('price')
        ->castUsing(fn ($state) => (float) $state * 100);

    expect($column->resolveValue('9.99'))->toBe(999.0);
});

it('resolveValue applies default before cast', function () {
    $column = ImportColumn::make('price')
        ->default('0')
        ->castUsing(fn ($state) => (int) $state);

    expect($column->resolveValue(null))->toBe(0);
});

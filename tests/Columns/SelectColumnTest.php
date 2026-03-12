<?php

use Primix\Tables\Columns\SelectColumn;

it('can be created with make', function () {
    $column = SelectColumn::make('status');

    expect($column)->toBeInstanceOf(SelectColumn::class);
});

it('is editable by default', function () {
    $column = SelectColumn::make('status');

    expect($column->isEditable())->toBeTrue();
});

it('can disable editable', function () {
    $column = SelectColumn::make('status')->editable(false);

    expect($column->isEditable())->toBeFalse();
});

it('can set options', function () {
    $column = SelectColumn::make('status')->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ]);

    expect($column->getOptions())->toBe([
        'draft' => 'Draft',
        'published' => 'Published',
    ]);
});

it('has null select placeholder by default', function () {
    $column = SelectColumn::make('status');

    expect($column->getSelectPlaceholder())->toBeNull();
});

it('can set select placeholder', function () {
    $column = SelectColumn::make('status')->selectPlaceholder('Choose...');

    expect($column->getSelectPlaceholder())->toBe('Choose...');
});

it('can set update state using callback', function () {
    $callback = fn () => null;
    $column = SelectColumn::make('status')->updateStateUsing($callback);

    expect($column)->toBeInstanceOf(SelectColumn::class);
});

it('can set before and after state updated callbacks', function () {
    $before = fn () => null;
    $after = fn () => null;

    $column = SelectColumn::make('status')
        ->beforeStateUpdated($before)
        ->afterStateUpdated($after);

    expect($column)->toBeInstanceOf(SelectColumn::class);
});

it('returns correct view', function () {
    $column = SelectColumn::make('status');

    expect($column->getView())->toBe('primix-tables::columns.select-column');
});

it('returns vue props', function () {
    $column = SelectColumn::make('status')
        ->options(['draft' => 'Draft', 'published' => 'Published'])
        ->selectPlaceholder('Choose...');

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('options')
        ->toHaveKey('editable', true)
        ->toHaveKey('selectPlaceholder', 'Choose...');
});

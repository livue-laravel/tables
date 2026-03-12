<?php

use Primix\Tables\Columns\TextInputColumn;

it('can be created with make', function () {
    $column = TextInputColumn::make('quantity');

    expect($column)->toBeInstanceOf(TextInputColumn::class);
});

it('has default input type of text', function () {
    $column = TextInputColumn::make('quantity');

    expect($column->getInputType())->toBe('text');
});

it('can set numeric type', function () {
    $column = TextInputColumn::make('quantity')->numeric();

    expect($column->getInputType())->toBe('number');
});

it('can set email type', function () {
    $column = TextInputColumn::make('email')->email();

    expect($column->getInputType())->toBe('email');
});

it('can set url type', function () {
    $column = TextInputColumn::make('website')->url();

    expect($column->getInputType())->toBe('url');
});

it('can set tel type', function () {
    $column = TextInputColumn::make('phone')->tel();

    expect($column->getInputType())->toBe('tel');
});

it('has null step by default', function () {
    $column = TextInputColumn::make('quantity');

    expect($column->getStep())->toBeNull();
});

it('can set step', function () {
    $column = TextInputColumn::make('quantity')->step(5);

    expect($column->getStep())->toBe(5);
});

it('numeric sets step when provided', function () {
    $column = TextInputColumn::make('quantity')->numeric(10);

    expect($column->getInputType())->toBe('number');
    expect($column->getStep())->toBe(10);
});

it('is editable by default', function () {
    $column = TextInputColumn::make('quantity');

    expect($column->isEditable())->toBeTrue();
});

it('returns correct view', function () {
    $column = TextInputColumn::make('quantity');

    expect($column->getView())->toBe('primix-tables::columns.text-input-column');
});

it('returns vue props', function () {
    $column = TextInputColumn::make('quantity')->numeric(1);

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('inputType', 'number')
        ->toHaveKey('editable', true)
        ->toHaveKey('step', 1);
});

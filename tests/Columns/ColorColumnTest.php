<?php

use Primix\Tables\Columns\ColorColumn;

it('can be created with make', function () {
    $column = ColorColumn::make('color');

    expect($column)->toBeInstanceOf(ColorColumn::class);
});

it('has default swatch shape of rounded', function () {
    $column = ColorColumn::make('color');

    expect($column->getSwatchShape())->toBe('rounded');
});

it('can set swatch shape', function () {
    $column = ColorColumn::make('color')->swatchShape('square');

    expect($column->getSwatchShape())->toBe('square');
});

it('has default swatch size of md', function () {
    $column = ColorColumn::make('color');

    expect($column->getSwatchSize())->toBe('md');
});

it('can set swatch size', function () {
    $column = ColorColumn::make('color')->swatchSize('lg');

    expect($column->getSwatchSize())->toBe('lg');
});

it('returns correct view', function () {
    $column = ColorColumn::make('color');

    expect($column->getView())->toBe('primix-tables::columns.color-column');
});

it('returns vue props', function () {
    $column = ColorColumn::make('color')
        ->swatchShape('square')
        ->swatchSize('lg');

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('swatchShape', 'square')
        ->toHaveKey('swatchSize', 'lg');
});

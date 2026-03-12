<?php

use Primix\Tables\Columns\ImageColumn;

it('can be created with make', function () {
    $column = ImageColumn::make('avatar');

    expect($column)->toBeInstanceOf(ImageColumn::class);
});

it('has default shape of rounded', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getShape())->toBe('rounded');
});

it('can set circular shape', function () {
    $column = ImageColumn::make('avatar')->circular();

    expect($column->getShape())->toBe('circular');
});

it('can set square shape', function () {
    $column = ImageColumn::make('avatar')->square();

    expect($column->getShape())->toBe('square');
});

it('has null height by default', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getHeight())->toBeNull();
});

it('can set height', function () {
    $column = ImageColumn::make('avatar')->height('40px');

    expect($column->getHeight())->toBe('40px');
});

it('has null disk by default', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getDisk())->toBeNull();
});

it('can set disk', function () {
    $column = ImageColumn::make('avatar')->disk('s3');

    expect($column->getDisk())->toBe('s3');
});

it('has null default image url by default', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getDefaultImageUrl())->toBeNull();
});

it('can set default image url', function () {
    $column = ImageColumn::make('avatar')->defaultImageUrl('/images/placeholder.png');

    expect($column->getDefaultImageUrl())->toBe('/images/placeholder.png');
});

it('is not stacked by default', function () {
    $column = ImageColumn::make('avatar');

    expect($column->isStacked())->toBeFalse();
});

it('can enable stacked mode', function () {
    $column = ImageColumn::make('avatar')->stacked();

    expect($column->isStacked())->toBeTrue();
});

it('has default stack limit of 3', function () {
    $column = ImageColumn::make('avatar')->stacked();

    expect($column->getStackLimit())->toBe(3);
});

it('can set custom stack limit', function () {
    $column = ImageColumn::make('avatar')->stacked(limit: 5);

    expect($column->getStackLimit())->toBe(5);
});

it('has null stack overlap by default', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getStackOverlap())->toBeNull();
});

it('can set stack overlap', function () {
    $column = ImageColumn::make('avatar')->stacked()->stackOverlap(8);

    expect($column->getStackOverlap())->toBe(8);
});

it('returns correct view', function () {
    $column = ImageColumn::make('avatar');

    expect($column->getView())->toBe('primix-tables::columns.image-column');
});

it('returns vue props', function () {
    $column = ImageColumn::make('avatar')
        ->circular()
        ->height('48px')
        ->disk('s3')
        ->stacked(limit: 5);

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('shape', 'circular')
        ->toHaveKey('height', '48px')
        ->toHaveKey('disk', 's3')
        ->toHaveKey('isStacked', true)
        ->toHaveKey('stackLimit', 5);
});

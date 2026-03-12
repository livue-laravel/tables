<?php

use Primix\Tables\Table;

it('is not reorderable by default', function () {
    $table = Table::make();

    expect($table->isReorderable())->toBeFalse();
});

it('can enable reorderable', function () {
    $table = Table::make()->reorderable();

    expect($table->isReorderable())->toBeTrue();
});

it('can disable reorderable', function () {
    $table = Table::make()->reorderable(false);

    expect($table->isReorderable())->toBeFalse();
});

it('has default order column', function () {
    $table = Table::make()->reorderable();

    expect($table->getOrderColumn())->toBe('sort_order');
});

it('can set custom order column', function () {
    $table = Table::make()->reorderable(column: 'position');

    expect($table->getOrderColumn())->toBe('position');
});

it('supports closure for reorderable', function () {
    $table = Table::make()->reorderable(fn () => true);

    expect($table->isReorderable())->toBeTrue();
});

<?php

use Primix\Tables\Table;

it('has no default sort by default', function () {
    $table = Table::make();

    expect($table->getDefaultSortColumn())->toBeNull()
        ->and($table->getDefaultSortDirection())->toBe('asc');
});

it('can set a default sort column ascending', function () {
    $table = Table::make()->defaultSort('name');

    expect($table->getDefaultSortColumn())->toBe('name')
        ->and($table->getDefaultSortDirection())->toBe('asc');
});

it('can set a default sort column descending', function () {
    $table = Table::make()->defaultSort('created_at', 'desc');

    expect($table->getDefaultSortColumn())->toBe('created_at')
        ->and($table->getDefaultSortDirection())->toBe('desc');
});

it('normalizes invalid directions to asc', function () {
    $table = Table::make()->defaultSort('created_at', 'sideways');

    expect($table->getDefaultSortDirection())->toBe('asc');
});

it('accepts uppercase direction', function () {
    $table = Table::make()->defaultSort('created_at', 'DESC');

    expect($table->getDefaultSortDirection())->toBe('desc');
});

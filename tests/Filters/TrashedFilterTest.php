<?php

use Primix\Tables\Filters\TrashedFilter;

it('can be created with make', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter)->toBeInstanceOf(TrashedFilter::class);
});

it('sets name via constructor', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getName())->toBe('trashed');
});

it('auto-generates label from name', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getLabel())->toBe('Trashed');
});

it('can set custom label', function () {
    $filter = TrashedFilter::make('trashed')->label('Soft Deleted');

    expect($filter->getLabel())->toBe('Soft Deleted');
});

it('has default with label', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getWithLabel())->toBe('With trashed');
});

it('can set custom with label', function () {
    $filter = TrashedFilter::make('trashed')->withLabel('Include deleted');

    expect($filter->getWithLabel())->toBe('Include deleted');
});

it('has default without label', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getWithoutLabel())->toBe('Without trashed');
});

it('can set custom without label', function () {
    $filter = TrashedFilter::make('trashed')->withoutLabel('Active only');

    expect($filter->getWithoutLabel())->toBe('Active only');
});

it('has default only label', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getOnlyLabel())->toBe('Only trashed');
});

it('can set custom only label', function () {
    $filter = TrashedFilter::make('trashed')->onlyLabel('Deleted only');

    expect($filter->getOnlyLabel())->toBe('Deleted only');
});

it('can set default value', function () {
    $filter = TrashedFilter::make('trashed')->default('with');

    expect($filter->getDefaultValue())->toBe('with');
});

it('has null default value by default', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getDefaultValue())->toBeNull();
});

it('returns correct view', function () {
    $filter = TrashedFilter::make('trashed');

    expect($filter->getView())->toBe('primix-tables::filters.trashed-filter');
});

it('returns vue props', function () {
    $filter = TrashedFilter::make('trashed')
        ->withLabel('Include deleted')
        ->withoutLabel('Active only')
        ->onlyLabel('Deleted only');

    $props = $filter->toVueProps();

    expect($props)
        ->toHaveKey('withLabel', 'Include deleted')
        ->toHaveKey('withoutLabel', 'Active only')
        ->toHaveKey('onlyLabel', 'Deleted only');
});

it('supports closure for with label', function () {
    $filter = TrashedFilter::make('trashed')->withLabel(fn () => 'Include deleted');

    expect($filter->getWithLabel())->toBe('Include deleted');
});

it('supports closure for without label', function () {
    $filter = TrashedFilter::make('trashed')->withoutLabel(fn () => 'Active only');

    expect($filter->getWithoutLabel())->toBe('Active only');
});

it('supports closure for only label', function () {
    $filter = TrashedFilter::make('trashed')->onlyLabel(fn () => 'Deleted only');

    expect($filter->getOnlyLabel())->toBe('Deleted only');
});

<?php

use Primix\Tables\Filters\BooleanFilter;

it('can be created with make', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter)->toBeInstanceOf(BooleanFilter::class);
});

it('sets name via constructor', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getName())->toBe('is_active');
});

it('auto-generates label from name', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getLabel())->toBe('Is Active');
});

it('can set custom label', function () {
    $filter = BooleanFilter::make('is_active')->label('Active Status');

    expect($filter->getLabel())->toBe('Active Status');
});

it('has default true label', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getTrueLabel())->toBe('Yes');
});

it('can set custom true label', function () {
    $filter = BooleanFilter::make('is_active')->trueLabel('Active');

    expect($filter->getTrueLabel())->toBe('Active');
});

it('has default false label', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getFalseLabel())->toBe('No');
});

it('can set custom false label', function () {
    $filter = BooleanFilter::make('is_active')->falseLabel('Inactive');

    expect($filter->getFalseLabel())->toBe('Inactive');
});

it('can set default value', function () {
    $filter = BooleanFilter::make('is_active')->default(true);

    expect($filter->getDefaultValue())->toBeTrue();
});

it('has null default value by default', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getDefaultValue())->toBeNull();
});

it('returns correct view', function () {
    $filter = BooleanFilter::make('is_active');

    expect($filter->getView())->toBe('primix-tables::filters.boolean-filter');
});

it('returns vue props', function () {
    $filter = BooleanFilter::make('is_active')
        ->trueLabel('Active')
        ->falseLabel('Inactive');

    $props = $filter->toVueProps();

    expect($props)
        ->toHaveKey('trueLabel', 'Active')
        ->toHaveKey('falseLabel', 'Inactive');
});

it('supports closure for true label', function () {
    $filter = BooleanFilter::make('is_active')->trueLabel(fn () => 'Enabled');

    expect($filter->getTrueLabel())->toBe('Enabled');
});

it('supports closure for false label', function () {
    $filter = BooleanFilter::make('is_active')->falseLabel(fn () => 'Disabled');

    expect($filter->getFalseLabel())->toBe('Disabled');
});

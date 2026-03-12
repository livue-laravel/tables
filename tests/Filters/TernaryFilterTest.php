<?php

use Primix\Tables\Filters\TernaryFilter;

it('can be created with make', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter)->toBeInstanceOf(TernaryFilter::class);
});

it('sets name via constructor', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getName())->toBe('verified_at');
});

it('auto-generates label from name', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getLabel())->toBe('Verified At');
});

it('can set custom label', function () {
    $filter = TernaryFilter::make('verified_at')->label('Email Verified');

    expect($filter->getLabel())->toBe('Email Verified');
});

it('has default true label', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getTrueLabel())->toBe('Yes');
});

it('can set custom true label', function () {
    $filter = TernaryFilter::make('verified_at')->trueLabel('Verified');

    expect($filter->getTrueLabel())->toBe('Verified');
});

it('has default false label', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getFalseLabel())->toBe('No');
});

it('can set custom false label', function () {
    $filter = TernaryFilter::make('verified_at')->falseLabel('Not Verified');

    expect($filter->getFalseLabel())->toBe('Not Verified');
});

it('has default all label', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getAllLabel())->toBe('All');
});

it('can set custom all label', function () {
    $filter = TernaryFilter::make('verified_at')->allLabel('Any');

    expect($filter->getAllLabel())->toBe('Any');
});

it('is not nullable by default', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->isNullable())->toBeFalse();
});

it('can enable nullable', function () {
    $filter = TernaryFilter::make('verified_at')->nullable();

    expect($filter->isNullable())->toBeTrue();
});

it('can disable nullable', function () {
    $filter = TernaryFilter::make('verified_at')->nullable(false);

    expect($filter->isNullable())->toBeFalse();
});

it('uses name as default column', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getColumn())->toBe('verified_at');
});

it('can set custom column', function () {
    $filter = TernaryFilter::make('verified')->column('email_verified_at');

    expect($filter->getColumn())->toBe('email_verified_at');
});

it('can set default value', function () {
    $filter = TernaryFilter::make('verified_at')->default(true);

    expect($filter->getDefaultValue())->toBeTrue();
});

it('has null default value by default', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getDefaultValue())->toBeNull();
});

it('returns correct view', function () {
    $filter = TernaryFilter::make('verified_at');

    expect($filter->getView())->toBe('primix-tables::filters.ternary-filter');
});

it('returns vue props', function () {
    $filter = TernaryFilter::make('verified_at')
        ->trueLabel('Verified')
        ->falseLabel('Not Verified')
        ->allLabel('Any')
        ->nullable();

    $props = $filter->toVueProps();

    expect($props)
        ->toHaveKey('trueLabel', 'Verified')
        ->toHaveKey('falseLabel', 'Not Verified')
        ->toHaveKey('allLabel', 'Any')
        ->toHaveKey('nullable', true);
});

it('supports closure for true label', function () {
    $filter = TernaryFilter::make('verified_at')->trueLabel(fn () => 'Verified');

    expect($filter->getTrueLabel())->toBe('Verified');
});

it('supports closure for false label', function () {
    $filter = TernaryFilter::make('verified_at')->falseLabel(fn () => 'Unverified');

    expect($filter->getFalseLabel())->toBe('Unverified');
});

it('supports closure for all label', function () {
    $filter = TernaryFilter::make('verified_at')->allLabel(fn () => 'Any');

    expect($filter->getAllLabel())->toBe('Any');
});

it('supports closure for nullable', function () {
    $filter = TernaryFilter::make('verified_at')->nullable(fn () => true);

    expect($filter->isNullable())->toBeTrue();
});

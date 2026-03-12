<?php

use Carbon\Carbon;
use Primix\Tables\Filters\DateFilter;

it('can be created with make', function () {
    $filter = DateFilter::make('created_at');

    expect($filter)->toBeInstanceOf(DateFilter::class);
});

it('sets name via constructor', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getName())->toBe('created_at');
});

it('auto-generates label from name', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getLabel())->toBe('Created At');
});

it('can set custom label', function () {
    $filter = DateFilter::make('created_at')->label('Date Created');

    expect($filter->getLabel())->toBe('Date Created');
});

it('is not range by default', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->isRange())->toBeFalse();
});

it('can enable range', function () {
    $filter = DateFilter::make('created_at')->range();

    expect($filter->isRange())->toBeTrue();
});

it('can disable range', function () {
    $filter = DateFilter::make('created_at')->range(false);

    expect($filter->isRange())->toBeFalse();
});

it('has no format by default', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getFormat())->toBeNull();
});

it('can set format', function () {
    $filter = DateFilter::make('created_at')->format('dd/mm/yy');

    expect($filter->getFormat())->toBe('dd/mm/yy');
});

it('has no minDate by default', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getMinDate())->toBeNull();
});

it('can set minDate as string', function () {
    $filter = DateFilter::make('created_at')->minDate('2024-01-01');

    expect($filter->getMinDate())->toBe('2024-01-01');
});

it('can set minDate as Carbon', function () {
    $date = Carbon::parse('2024-01-01');
    $filter = DateFilter::make('created_at')->minDate($date);

    expect($filter->getMinDate())->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

it('has no maxDate by default', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getMaxDate())->toBeNull();
});

it('can set maxDate as string', function () {
    $filter = DateFilter::make('created_at')->maxDate('2024-12-31');

    expect($filter->getMaxDate())->toBe('2024-12-31');
});

it('can set maxDate as Carbon', function () {
    $date = Carbon::parse('2024-12-31');
    $filter = DateFilter::make('created_at')->maxDate($date);

    expect($filter->getMaxDate())->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

it('can set default value', function () {
    $filter = DateFilter::make('created_at')->default('2024-06-15');

    expect($filter->getDefaultValue())->toBe('2024-06-15');
});

it('has null default value by default', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getDefaultValue())->toBeNull();
});

it('returns correct view', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getView())->toBe('primix-tables::filters.date-filter');
});

it('returns vue props', function () {
    $filter = DateFilter::make('created_at')
        ->range()
        ->format('dd/mm/yy')
        ->minDate('2024-01-01')
        ->maxDate('2024-12-31');

    $props = $filter->toVueProps();

    expect($props)
        ->toHaveKey('range', true)
        ->toHaveKey('format', 'dd/mm/yy')
        ->toHaveKey('minDate', '2024-01-01')
        ->toHaveKey('maxDate', '2024-12-31');
});

it('converts Carbon minDate to string in vue props', function () {
    $filter = DateFilter::make('created_at')
        ->minDate(Carbon::parse('2024-01-01'));

    $props = $filter->toVueProps();

    expect($props['minDate'])->toBe('2024-01-01');
});

it('converts Carbon maxDate to string in vue props', function () {
    $filter = DateFilter::make('created_at')
        ->maxDate(Carbon::parse('2024-12-31'));

    $props = $filter->toVueProps();

    expect($props['maxDate'])->toBe('2024-12-31');
});

it('supports closure for range', function () {
    $filter = DateFilter::make('created_at')->range(fn () => true);

    expect($filter->isRange())->toBeTrue();
});

it('supports closure for minDate', function () {
    $filter = DateFilter::make('created_at')->minDate(fn () => '2024-01-01');

    expect($filter->getMinDate())->toBe('2024-01-01');
});

it('supports closure for maxDate', function () {
    $filter = DateFilter::make('created_at')->maxDate(fn () => '2024-12-31');

    expect($filter->getMaxDate())->toBe('2024-12-31');
});

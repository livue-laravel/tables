<?php

use Primix\Tables\Columns\IconColumn;

it('can be created with make', function () {
    $column = IconColumn::make('status');

    expect($column)->toBeInstanceOf(IconColumn::class);
});

it('has empty icons by default', function () {
    $column = IconColumn::make('status');

    expect($column->getIcons())->toBe([]);
});

it('can set icons map', function () {
    $column = IconColumn::make('status')->icons([
        'active' => 'pi pi-check',
        'inactive' => 'pi pi-times',
    ]);

    expect($column->getIcons())->toHaveCount(2);
});

it('has empty colors by default', function () {
    $column = IconColumn::make('status');

    expect($column->getColors())->toBe([]);
});

it('can set colors map', function () {
    $column = IconColumn::make('status')->colors([
        'active' => 'success',
        'inactive' => 'danger',
    ]);

    expect($column->getColors())->toHaveCount(2);
});

it('is not boolean by default', function () {
    $column = IconColumn::make('is_active');

    expect($column->isBoolean())->toBeFalse();
});

it('can enable boolean mode', function () {
    $column = IconColumn::make('is_active')->boolean();

    expect($column->isBoolean())->toBeTrue();
});

it('has default true icon', function () {
    $column = IconColumn::make('is_active')->boolean();

    expect($column->getTrueIcon())->toBe('pi pi-check-circle');
});

it('has default false icon', function () {
    $column = IconColumn::make('is_active')->boolean();

    expect($column->getFalseIcon())->toBe('pi pi-times-circle');
});

it('can set custom true icon', function () {
    $column = IconColumn::make('is_active')->boolean()->trueIcon('pi pi-thumbs-up');

    expect($column->getTrueIcon())->toBe('pi pi-thumbs-up');
});

it('can set custom false icon', function () {
    $column = IconColumn::make('is_active')->boolean()->falseIcon('pi pi-thumbs-down');

    expect($column->getFalseIcon())->toBe('pi pi-thumbs-down');
});

it('has default true color of success', function () {
    $column = IconColumn::make('is_active')->boolean();

    expect($column->getTrueColor())->toBe('success');
});

it('has default false color of danger', function () {
    $column = IconColumn::make('is_active')->boolean();

    expect($column->getFalseColor())->toBe('danger');
});

it('can set custom true color', function () {
    $column = IconColumn::make('is_active')->boolean()->trueColor('info');

    expect($column->getTrueColor())->toBe('info');
});

it('can set custom false color', function () {
    $column = IconColumn::make('is_active')->boolean()->falseColor('warning');

    expect($column->getFalseColor())->toBe('warning');
});

it('returns correct view', function () {
    $column = IconColumn::make('status');

    expect($column->getView())->toBe('primix-tables::columns.icon-column');
});

it('returns vue props', function () {
    $column = IconColumn::make('is_active')->boolean();

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('isBoolean', true)
        ->toHaveKey('trueIcon', 'pi pi-check-circle')
        ->toHaveKey('falseIcon', 'pi pi-times-circle')
        ->toHaveKey('trueColor', 'success')
        ->toHaveKey('falseColor', 'danger');
});

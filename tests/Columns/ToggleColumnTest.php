<?php

use Primix\Tables\Columns\ToggleColumn;

it('can be created with make', function () {
    $column = ToggleColumn::make('is_active');

    expect($column)->toBeInstanceOf(ToggleColumn::class);
});

it('is editable by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->isEditable())->toBeTrue();
});

it('can disable editable', function () {
    $column = ToggleColumn::make('is_active')->editable(false);

    expect($column->isEditable())->toBeFalse();
});

it('has null on color by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOnColor())->toBeNull();
});

it('can set on color', function () {
    $column = ToggleColumn::make('is_active')->onColor('success');

    expect($column->getOnColor())->toBe('success');
});

it('has null off color by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOffColor())->toBeNull();
});

it('can set off color', function () {
    $column = ToggleColumn::make('is_active')->offColor('danger');

    expect($column->getOffColor())->toBe('danger');
});

it('has null on label by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOnLabel())->toBeNull();
});

it('can set on label', function () {
    $column = ToggleColumn::make('is_active')->onLabel('Yes');

    expect($column->getOnLabel())->toBe('Yes');
});

it('has null off label by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOffLabel())->toBeNull();
});

it('can set off label', function () {
    $column = ToggleColumn::make('is_active')->offLabel('No');

    expect($column->getOffLabel())->toBe('No');
});

it('has null on icon by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOnIcon())->toBeNull();
});

it('can set on icon', function () {
    $column = ToggleColumn::make('is_active')->onIcon('pi pi-check');

    expect($column->getOnIcon())->toBe('pi pi-check');
});

it('has null off icon by default', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getOffIcon())->toBeNull();
});

it('can set off icon', function () {
    $column = ToggleColumn::make('is_active')->offIcon('pi pi-times');

    expect($column->getOffIcon())->toBe('pi pi-times');
});

it('returns correct view', function () {
    $column = ToggleColumn::make('is_active');

    expect($column->getView())->toBe('primix-tables::columns.toggle-column');
});

it('returns vue props', function () {
    $column = ToggleColumn::make('is_active')
        ->onColor('success')
        ->offColor('danger')
        ->onLabel('Active')
        ->offLabel('Inactive')
        ->onIcon('pi pi-check')
        ->offIcon('pi pi-times');

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('editable', true)
        ->toHaveKey('onColor', 'success')
        ->toHaveKey('offColor', 'danger')
        ->toHaveKey('onLabel', 'Active')
        ->toHaveKey('offLabel', 'Inactive')
        ->toHaveKey('onIcon', 'pi pi-check')
        ->toHaveKey('offIcon', 'pi pi-times');
});

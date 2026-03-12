<?php

use Primix\Tables\Columns\BadgeColumn;

it('can be created with make', function () {
    $column = BadgeColumn::make('status');

    expect($column)->toBeInstanceOf(BadgeColumn::class);
});

it('has empty colors by default', function () {
    $column = BadgeColumn::make('status');

    expect($column->getColors())->toBe([]);
});

it('can set colors', function () {
    $column = BadgeColumn::make('status')->colors([
        'success' => 'published',
        'warning' => 'draft',
        'danger' => 'archived',
    ]);

    expect($column->getColors())->toHaveCount(3);
});

it('has empty icons by default', function () {
    $column = BadgeColumn::make('status');

    expect($column->getIcons())->toBe([]);
});

it('can set icons', function () {
    $column = BadgeColumn::make('status')->icons([
        'published' => 'pi pi-check',
        'draft' => 'pi pi-pencil',
    ]);

    expect($column->getIcons())->toHaveCount(2);
});

it('resolves color for state from colors map', function () {
    $column = BadgeColumn::make('status')->colors([
        'active' => 'success',
        'inactive' => 'danger',
    ]);

    expect($column->getColorForState('active'))->toBe('success');
});

it('resolves icon for state from icons map', function () {
    $column = BadgeColumn::make('status')->icons([
        'active' => 'pi pi-check',
        'inactive' => 'pi pi-times',
    ]);

    expect($column->getIconForState('active'))->toBe('pi pi-check');
});

it('returns correct view', function () {
    $column = BadgeColumn::make('status');

    expect($column->getView())->toBe('primix-tables::columns.badge-column');
});

it('returns vue props with colors and icons', function () {
    $colors = ['active' => 'success'];
    $icons = ['active' => 'pi pi-check'];

    $column = BadgeColumn::make('status')
        ->colors($colors)
        ->icons($icons);

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('colors', $colors)
        ->toHaveKey('icons', $icons);
});

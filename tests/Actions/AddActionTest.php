<?php

use Primix\Actions\Action;
use Primix\Tables\Actions\AddAction;

it('has inline-create as default name', function () {
    expect(AddAction::getDefaultName())->toBe('inline-create');
});

it('has correct default label', function () {
    expect(AddAction::make()->getLabel())->toBe(__('primix-tables::tables.add'));
});

it('has correct default icon', function () {
    expect(AddAction::make()->getIcon())->toBe('pi pi-plus');
});

it('has correct default color', function () {
    expect(AddAction::make()->getColor())->toBe('primary');
});

it('is an instance of Action', function () {
    expect(AddAction::make())->toBeInstanceOf(Action::class);
});

it('is an icon button by default', function () {
    expect(AddAction::make()->isIconButton())->toBeTrue();
});

it('is not hidden by default', function () {
    expect(AddAction::make()->isHidden())->toBeFalse();
});

it('can override label', function () {
    expect(AddAction::make()->label('New row')->getLabel())->toBe('New row');
});

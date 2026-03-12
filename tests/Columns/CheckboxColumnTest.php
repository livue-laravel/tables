<?php

use Primix\Tables\Columns\CheckboxColumn;

it('can be created with make', function () {
    $column = CheckboxColumn::make('is_active');

    expect($column)->toBeInstanceOf(CheckboxColumn::class);
});

it('is editable by default', function () {
    $column = CheckboxColumn::make('is_active');

    expect($column->isEditable())->toBeTrue();
});

it('can disable editable', function () {
    $column = CheckboxColumn::make('is_active')->editable(false);

    expect($column->isEditable())->toBeFalse();
});

it('can set rules', function () {
    $column = CheckboxColumn::make('is_active')->rules('required|boolean');

    expect($column->getRules())->toBe('required|boolean');
});

it('can set validation messages', function () {
    $messages = ['required' => 'This field is required'];
    $column = CheckboxColumn::make('is_active')->validationMessages($messages);

    expect($column->getValidationMessages())->toBe($messages);
});

it('returns correct view', function () {
    $column = CheckboxColumn::make('is_active');

    expect($column->getView())->toBe('primix-tables::columns.checkbox-column');
});

it('returns vue props with editable', function () {
    $column = CheckboxColumn::make('is_active');

    $props = $column->toVueProps();

    expect($props)->toHaveKey('editable', true);
});

<?php

use Primix\Tables\Filters\SelectFilter;

it('can be created with make', function () {
    $filter = SelectFilter::make('status');

    expect($filter)->toBeInstanceOf(SelectFilter::class);
});

it('sets name via constructor', function () {
    $filter = SelectFilter::make('status');

    expect($filter->getName())->toBe('status');
});

it('auto-generates label from name', function () {
    $filter = SelectFilter::make('category_id');

    expect($filter->getLabel())->toBe('Category Id');
});

it('can set custom label', function () {
    $filter = SelectFilter::make('status')->label('Post Status');

    expect($filter->getLabel())->toBe('Post Status');
});

it('has empty options by default', function () {
    $filter = SelectFilter::make('status');

    expect($filter->getOptions())->toBe([]);
});

it('can set options', function () {
    $filter = SelectFilter::make('status')->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ]);

    expect($filter->getOptions())->toHaveCount(2);
});

it('is not multiple by default', function () {
    $filter = SelectFilter::make('status');

    expect($filter->isMultiple())->toBeFalse();
});

it('can enable multiple', function () {
    $filter = SelectFilter::make('status')->multiple();

    expect($filter->isMultiple())->toBeTrue();
});

it('is not searchable by default', function () {
    $filter = SelectFilter::make('status');

    expect($filter->isSearchable())->toBeFalse();
});

it('can enable searchable', function () {
    $filter = SelectFilter::make('status')->searchable();

    expect($filter->isSearchable())->toBeTrue();
});

it('is not native by default', function () {
    $filter = SelectFilter::make('status');

    expect($filter->isNative())->toBeFalse();
});

it('can enable native', function () {
    $filter = SelectFilter::make('status')->native();

    expect($filter->isNative())->toBeTrue();
});

it('can set relationship', function () {
    $filter = SelectFilter::make('category')
        ->relationship('category', 'name');

    expect($filter)->toBeInstanceOf(SelectFilter::class);
});

it('can set default value', function () {
    $filter = SelectFilter::make('status')->default('published');

    expect($filter->getDefaultValue())->toBe('published');
});

it('has null default value by default', function () {
    $filter = SelectFilter::make('status');

    expect($filter->getDefaultValue())->toBeNull();
});

it('returns correct view', function () {
    $filter = SelectFilter::make('status');

    expect($filter->getView())->toBe('primix-tables::filters.select-filter');
});

it('returns vue props', function () {
    $filter = SelectFilter::make('status')
        ->options(['draft' => 'Draft'])
        ->multiple()
        ->searchable();

    $props = $filter->toVueProps();

    expect($props)
        ->toHaveKey('multiple', true)
        ->toHaveKey('searchable', true)
        ->toHaveKey('native', false);
});

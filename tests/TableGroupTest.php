<?php

use Primix\Tables\TableGroup;

it('can be created with make', function () {
    $group = TableGroup::make('status');

    expect($group)->toBeInstanceOf(TableGroup::class);
});

it('sets column via constructor', function () {
    $group = TableGroup::make('status');

    expect($group->getColumn())->toBe('status');
});

it('auto-generates label from column', function () {
    $group = TableGroup::make('category_id');

    expect($group->getLabel())->toBe('Category Id');
});

it('can set custom label', function () {
    $group = TableGroup::make('status')->label('Post Status');

    expect($group->getLabel())->toBe('Post Status');
});

it('returns value as default title', function () {
    $group = TableGroup::make('status');

    expect($group->getTitle('published'))->toBe('published');
});

it('returns N/A for null value title', function () {
    $group = TableGroup::make('status');

    expect($group->getTitle(null))->toBe('N/A');
});

it('can customize title with callback', function () {
    $group = TableGroup::make('status')
        ->getTitleUsing(fn ($value) => "Status: {$value}");

    expect($group->getTitle('published'))->toBe('Status: published');
});

it('returns null description by default', function () {
    $group = TableGroup::make('status');

    expect($group->getDescription('published', 5))->toBeNull();
});

it('can customize description with callback', function () {
    $group = TableGroup::make('status')
        ->getDescriptionUsing(fn ($value, $count) => "{$count} items");

    expect($group->getDescription('published', 5))->toBe('5 items');
});

it('is collapsible by default', function () {
    $group = TableGroup::make('status');

    expect($group->isCollapsible())->toBeTrue();
});

it('can disable collapsible', function () {
    $group = TableGroup::make('status')->collapsible(false);

    expect($group->isCollapsible())->toBeFalse();
});

it('supports closure for collapsible', function () {
    $group = TableGroup::make('status')->collapsible(fn () => false);

    expect($group->isCollapsible())->toBeFalse();
});

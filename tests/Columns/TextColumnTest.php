<?php

use Primix\Tables\Columns\TextColumn;

it('has null character limit by default', function () {
    $column = TextColumn::make('title');

    expect($column->getCharacterLimit())->toBeNull();
});

it('can set character limit', function () {
    $column = TextColumn::make('title')->limit(50);

    expect($column->getCharacterLimit())->toBe(50);
});

it('has null word limit by default', function () {
    $column = TextColumn::make('title');

    expect($column->getWordLimit())->toBeNull();
});

it('can set word limit', function () {
    $column = TextColumn::make('title')->words(10);

    expect($column->getWordLimit())->toBe(10);
});

it('is not badge by default', function () {
    $column = TextColumn::make('status');

    expect($column->isBadge())->toBeFalse();
});

it('can enable badge', function () {
    $column = TextColumn::make('status')->badge();

    expect($column->isBadge())->toBeTrue();
});

it('can set money format', function () {
    $column = TextColumn::make('price')->money('USD');

    expect($column)->toBeInstanceOf(TextColumn::class);
});

it('can transform text column into a link', function () {
    $column = TextColumn::make('website')->url('https://example.com');

    expect($column->getUrl())->toBe('https://example.com')
        ->and($column->shouldOpenUrlInNewTab())->toBeFalse();
});

it('can resolve link url from closure using record', function () {
    $column = TextColumn::make('slug')->url(
        fn (array $record): string => 'https://example.com/products/' . $record['slug'],
    );

    $column->getState(['slug' => 'sku-123']);

    expect($column->getUrl())->toBe('https://example.com/products/sku-123');
});

it('can open link in new tab', function () {
    $column = TextColumn::make('website')
        ->url('https://example.com', true);

    expect($column->shouldOpenUrlInNewTab())->toBeTrue();
});

it('formats money values when money is enabled', function () {
    $column = TextColumn::make('price')->money('USD', 'en_US');

    $formatted = $column->getState(['price' => 1234.5]);

    expect($formatted)->not->toBe('1234.5')
        ->and($formatted)->toBeString();
});

it('formats euro money values when currency is EUR', function () {
    $column = TextColumn::make('price')->money('EUR', 'it_IT');

    $formatted = $column->getState(['price' => 1234.5]);

    expect(str_contains($formatted, '€') || str_contains($formatted, 'EUR'))->toBeTrue();
});

it('can set date format', function () {
    $column = TextColumn::make('created_at')->date();

    $props = $column->toVueProps();

    expect($props)->toHaveKey('dateFormat', 'M j, Y');
});

it('can set custom date format', function () {
    $column = TextColumn::make('created_at')->date('d/m/Y');

    $props = $column->toVueProps();

    expect($props)->toHaveKey('dateFormat', 'd/m/Y');
});

it('can set dateTime format', function () {
    $column = TextColumn::make('created_at')->dateTime();

    $props = $column->toVueProps();

    expect($props)->toHaveKey('dateFormat', 'M j, Y H:i');
});

it('can set time format', function () {
    $column = TextColumn::make('created_at')->time();

    $props = $column->toVueProps();

    expect($props)->toHaveKey('timeFormat', 'H:i');
});

it('has null weight by default', function () {
    $column = TextColumn::make('title');

    expect($column->getWeight())->toBeNull();
});

it('can set bold weight', function () {
    $column = TextColumn::make('title')->bold();

    expect($column->getWeight())->toBe('bold');
});

it('can set medium weight', function () {
    $column = TextColumn::make('title')->medium();

    expect($column->getWeight())->toBe('medium');
});

it('can set semibold weight', function () {
    $column = TextColumn::make('title')->semibold();

    expect($column->getWeight())->toBe('semibold');
});

it('has null description by default', function () {
    $column = TextColumn::make('title');

    expect($column->getDescription())->toBeNull();
});

it('can set description', function () {
    $column = TextColumn::make('title')->description('A short excerpt');

    expect($column->getDescription())->toBe('A short excerpt');
});

it('has default description position of below', function () {
    $column = TextColumn::make('title')->description('Text');

    expect($column->getDescriptionPosition())->toBe('below');
});

it('can set description position', function () {
    $column = TextColumn::make('title')->description('Text', 'above');

    expect($column->getDescriptionPosition())->toBe('above');
});

it('returns correct view', function () {
    $column = TextColumn::make('title');

    expect($column->getView())->toBe('primix-tables::columns.text-column');
});

it('returns complete vue props', function () {
    $column = TextColumn::make('title')
        ->bold()
        ->description('Desc')
        ->limit(100)
        ->words(20)
        ->badge();

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('weight', 'bold')
        ->toHaveKey('description', 'Desc')
        ->toHaveKey('descriptionPosition', 'below')
        ->toHaveKey('characterLimit', 100)
        ->toHaveKey('wordLimit', 20)
        ->toHaveKey('isBadge', true);
});

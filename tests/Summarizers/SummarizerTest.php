<?php

use Primix\Tables\Columns\Summarizers\Count;
use Primix\Tables\Columns\Summarizers\Sum;
use Primix\Tables\Columns\Summarizers\Average;
use Primix\Tables\Columns\Summarizers\Min;
use Primix\Tables\Columns\Summarizers\Max;
use Primix\Tables\Columns\Summarizers\Range;

it('Count has default label', function () {
    $summarizer = Count::make();

    expect($summarizer->getLabel())->toBe('Count');
});

it('Sum has default label', function () {
    $summarizer = Sum::make();

    expect($summarizer->getLabel())->toBe('Sum');
});

it('Average has default label', function () {
    $summarizer = Average::make();

    expect($summarizer->getLabel())->toBe('Average');
});

it('Min has default label', function () {
    $summarizer = Min::make();

    expect($summarizer->getLabel())->toBe('Min');
});

it('Max has default label', function () {
    $summarizer = Max::make();

    expect($summarizer->getLabel())->toBe('Max');
});

it('Range has default label', function () {
    $summarizer = Range::make();

    expect($summarizer->getLabel())->toBe('Range');
});

it('can set custom label', function () {
    $summarizer = Count::make()->label('Total Records');

    expect($summarizer->getLabel())->toBe('Total Records');
});

it('can set format state using callback', function () {
    $summarizer = Count::make()->formatStateUsing(fn ($state) => "Total: {$state}");

    expect($summarizer)->toBeInstanceOf(Count::class);
});

it('can set using callback', function () {
    $summarizer = Count::make()->using(fn ($query, $column) => 42);

    expect($summarizer)->toBeInstanceOf(Count::class);
});

it('Range can set separator', function () {
    $summarizer = Range::make()->separator(' to ');

    expect($summarizer)->toBeInstanceOf(Range::class);
});

it('can set numeric formatting', function () {
    $summarizer = Sum::make()->numeric(2, ',', '.');

    expect($summarizer)->toBeInstanceOf(Sum::class);
});

it('can set money formatting', function () {
    $summarizer = Sum::make()->money('EUR', 'it');

    expect($summarizer)->toBeInstanceOf(Sum::class);
});

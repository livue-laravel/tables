<?php

use Primix\Tables\Columns\TextColumn;
use Primix\Tables\Columns\Summarizers\Count;
use Primix\Tables\Columns\Summarizers\Sum;

// We use TextColumn as a concrete implementation of the abstract Column class

it('can be created with make', function () {
    $column = TextColumn::make('title');

    expect($column)->toBeInstanceOf(TextColumn::class);
    expect($column->getName())->toBe('title');
});

it('auto-generates label from name', function () {
    $column = TextColumn::make('first_name');

    expect($column->getLabel())->toBe('First Name');
});

it('auto-generates label from dot notation name', function () {
    $column = TextColumn::make('user.email');

    expect($column->getLabel())->toBe('Email');
});

it('can set custom label', function () {
    $column = TextColumn::make('title')->label('Post Title');

    expect($column->getLabel())->toBe('Post Title');
});

it('is not sortable by default', function () {
    $column = TextColumn::make('title');

    expect($column->isSortable())->toBeFalse();
});

it('can be sortable', function () {
    $column = TextColumn::make('title')->sortable();

    expect($column->isSortable())->toBeTrue();
});

it('sort column defaults to name', function () {
    $column = TextColumn::make('title')->sortable();

    expect($column->getSortColumn())->toBe('title');
});

it('can set custom sort column', function () {
    $column = TextColumn::make('title')->sortable(column: 'posts.title');

    expect($column->getSortColumn())->toBe('posts.title');
});

it('is not searchable by default', function () {
    $column = TextColumn::make('title');

    expect($column->isSearchable())->toBeFalse();
});

it('can be searchable', function () {
    $column = TextColumn::make('title')->searchable();

    expect($column->isSearchable())->toBeTrue();
});

it('search column defaults to name', function () {
    $column = TextColumn::make('title')->searchable();

    expect($column->getSearchColumn())->toBe('title');
});

it('can set custom search column', function () {
    $column = TextColumn::make('title')->searchable(column: 'posts.title');

    expect($column->getSearchColumn())->toBe('posts.title');
});

it('is not individually searchable by default', function () {
    $column = TextColumn::make('title');

    expect($column->isIndividuallySearchable())->toBeFalse();
});

it('can be individually searchable', function () {
    $column = TextColumn::make('title')->individuallySearchable();

    expect($column->isIndividuallySearchable())->toBeTrue();
});

it('individually searchable also sets searchable', function () {
    $column = TextColumn::make('title')->individuallySearchable();

    expect($column->isSearchable())->toBeTrue();
});

it('is globally searchable when searchable', function () {
    $column = TextColumn::make('title')->searchable();

    expect($column->isGloballySearchable())->toBeTrue();
});

it('is not copyable by default', function () {
    $column = TextColumn::make('title');

    expect($column->isCopyable())->toBeFalse();
});

it('can be copyable', function () {
    $column = TextColumn::make('title')->copyable();

    expect($column->isCopyable())->toBeTrue();
});

it('has default copy message of Copied!', function () {
    $column = TextColumn::make('title')->copyable();

    expect($column->getCopyMessage())->toBe('Copied!');
});

it('can set custom copy message', function () {
    $column = TextColumn::make('title')->copyable()->copyMessage('Text copied');

    expect($column->getCopyMessage())->toBe('Text copied');
});

it('has default copy message duration of 2000', function () {
    $column = TextColumn::make('title')->copyable();

    expect($column->getCopyMessageDuration())->toBe(2000);
});

it('can set copy message duration', function () {
    $column = TextColumn::make('title')->copyable()->copyMessageDuration(5000);

    expect($column->getCopyMessageDuration())->toBe(5000);
});

it('has default alignment of left', function () {
    $column = TextColumn::make('title');

    expect($column->getAlignment())->toBe('left');
});

it('can align center', function () {
    $column = TextColumn::make('title')->alignCenter();

    expect($column->getAlignment())->toBe('center');
});

it('can align right', function () {
    $column = TextColumn::make('title')->alignRight();

    expect($column->getAlignment())->toBe('right');
});

it('has null tooltip by default', function () {
    $column = TextColumn::make('title');

    expect($column->getTooltip())->toBeNull();
});

it('can set tooltip', function () {
    $column = TextColumn::make('title')->tooltip('Click to edit');

    expect($column->getTooltip())->toBe('Click to edit');
});

it('is not toggleable by default', function () {
    $column = TextColumn::make('title');

    expect($column->isToggleable())->toBeFalse();
});

it('can be toggleable', function () {
    $column = TextColumn::make('title')->toggleable();

    expect($column->isToggleable())->toBeTrue();
});

it('is not toggled hidden by default', function () {
    $column = TextColumn::make('title')->toggleable();

    expect($column->isToggledHiddenByDefault())->toBeFalse();
});

it('can be toggled hidden by default', function () {
    $column = TextColumn::make('title')->toggleable(isToggledHiddenByDefault: true);

    expect($column->isToggledHiddenByDefault())->toBeTrue();
});

it('has null placeholder by default', function () {
    $column = TextColumn::make('title');

    expect($column->getPlaceholder())->toBeNull();
});

it('can set placeholder', function () {
    $column = TextColumn::make('title')->placeholder('-');

    expect($column->getPlaceholder())->toBe('-');
});

it('can set format state using callback', function () {
    $column = TextColumn::make('title')->formatStateUsing(fn ($state) => strtoupper($state));

    $record = (object) ['title' => 'hello'];
    $state = $column->getState($record);

    expect($state)->toBe('HELLO');
});

it('can set get state using callback', function () {
    $column = TextColumn::make('title')->getStateUsing(fn () => 'custom value');

    $record = (object) ['title' => 'original'];
    $state = $column->getState($record);

    expect($state)->toBe('custom value');
});

it('gets state from record using data_get', function () {
    $column = TextColumn::make('title');

    $record = (object) ['title' => 'My Post'];
    $state = $column->getState($record);

    expect($state)->toBe('My Post');
});

it('has null color by default', function () {
    $column = TextColumn::make('title');

    expect($column->getColor())->toBeNull();
});

it('can set color', function () {
    $column = TextColumn::make('title')->color('primary');

    expect($column->getColor())->toBe('primary');
});

it('has no summarizers by default', function () {
    $column = TextColumn::make('title');

    expect($column->hasSummarizers())->toBeFalse();
    expect($column->getSummarizers())->toBe([]);
});

it('can set a single summarizer', function () {
    $column = TextColumn::make('amount')->summarize(Count::make());

    expect($column->hasSummarizers())->toBeTrue();
    expect($column->getSummarizers())->toHaveCount(1);
});

it('can set multiple summarizers', function () {
    $column = TextColumn::make('amount')->summarize([
        Count::make(),
        Sum::make(),
    ]);

    expect($column->getSummarizers())->toHaveCount(2);
});

it('returns base vue props', function () {
    $column = TextColumn::make('title')
        ->label('Title')
        ->sortable()
        ->searchable()
        ->alignCenter()
        ->color('primary')
        ->copyable()
        ->tooltip('Tooltip')
        ->toggleable();

    $props = $column->toVueProps();

    expect($props)
        ->toHaveKey('name', 'title')
        ->toHaveKey('label', 'Title')
        ->toHaveKey('sortable', true)
        ->toHaveKey('searchable', true)
        ->toHaveKey('alignment', 'center')
        ->toHaveKey('color', 'primary')
        ->toHaveKey('copyable', true)
        ->toHaveKey('tooltip', 'Tooltip')
        ->toHaveKey('toggleable', true)
        ->toHaveKey('type', 'TextColumn');
});

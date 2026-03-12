<?php

use Primix\Actions\Action;
use Primix\Tables\Table;
use Primix\Tables\Columns\TextColumn;
use Primix\Tables\Columns\CheckboxColumn;
use Primix\Tables\Columns\Summarizers\Count;
use Primix\Tables\Columns\Summarizers\Sum;
use Primix\Tables\Filters\SelectFilter;
use Primix\Tables\Enums\FiltersLayout;

it('can be created with make', function () {
    $table = Table::make();

    expect($table)->toBeInstanceOf(Table::class);
});

it('has empty columns by default', function () {
    $table = Table::make();

    expect($table->getColumns())->toBe([]);
});

it('can set columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
        TextColumn::make('status'),
    ]);

    expect($table->getColumns())->toHaveCount(2);
});

it('has empty filters by default', function () {
    $table = Table::make();

    expect($table->getFilters())->toBe([]);
});

it('can set filters', function () {
    $table = Table::make()->filters([
        SelectFilter::make('status')->options(['draft' => 'Draft']),
    ]);

    expect($table->getFilters())->toHaveCount(1);
});

it('has empty actions by default', function () {
    $table = Table::make();

    expect($table->getActions())->toBe([]);
});

it('can set actions', function () {
    $table = Table::make()->actions([
        Action::make('edit'),
        Action::make('delete'),
    ]);

    expect($table->getActions())->toHaveCount(2);
});

it('can set bulk actions', function () {
    $table = Table::make()->bulkActions([
        Action::make('deleteSelected'),
    ]);

    expect($table->getBulkActions())->toHaveCount(1);
});

it('can set header actions', function () {
    $table = Table::make()->headerActions([
        Action::make('create'),
    ]);

    expect($table->getHeaderActions())->toHaveCount(1);
});

it('has a default bulk bar action', function () {
    $table = Table::make();
    $actions = $table->getBulkBarActions();

    expect($actions)->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(Action::class)
        ->and($actions[0]->getName())->toBe('clearSelection')
        ->and($actions[0]->isLink())->toBeTrue();
});

it('can set bulk bar actions', function () {
    $table = Table::make()->bulkBarActions([
        Action::make('clearSelectionCustom'),
    ]);

    expect($table->getBulkBarActions())->toHaveCount(1)
        ->and($table->getBulkBarActions()[0]->getName())->toBe('clearSelectionCustom');
});

it('has a default column toggle action', function () {
    $table = Table::make();
    $actions = $table->getColumnToggleActions();

    expect($actions)->toHaveCount(1)
        ->and($actions[0])->toBeInstanceOf(Action::class)
        ->and($actions[0]->getName())->toBe('toggleColumns')
        ->and($actions[0]->isIconButton())->toBeTrue();
});

it('can set column toggle actions', function () {
    $table = Table::make()->columnToggleActions([
        Action::make('customToggle'),
    ]);

    expect($table->getColumnToggleActions())->toHaveCount(1)
        ->and($table->getColumnToggleActions()[0]->getName())->toBe('customToggle');
});

it('has default per page of 10', function () {
    $table = Table::make();

    expect($table->getDefaultPerPage())->toBe(10);
});

it('can set default per page', function () {
    $table = Table::make()->defaultPerPage(25);

    expect($table->getDefaultPerPage())->toBe(25);
});

it('has default per page options', function () {
    $table = Table::make();

    expect($table->getPerPageOptions())->toBe([10, 25, 50, 100]);
});

it('can set per page options', function () {
    $table = Table::make()->perPageOptions([5, 10, 20]);

    expect($table->getPerPageOptions())->toBe([5, 10, 20]);
});

it('is not searchable by default when no searchable columns exist', function () {
    $table = Table::make();

    expect($table->isSearchable())->toBeFalse();
});

it('is searchable when at least one column is globally searchable', function () {
    $table = Table::make()->columns([
        TextColumn::make('title')->searchable(),
    ]);

    expect($table->isSearchable())->toBeTrue();
});

it('is not searchable when no columns are globally searchable', function () {
    $table = Table::make()->columns([
        TextColumn::make('title')->searchable(true, isIndividual: true, isGlobal: false),
    ]);

    expect($table->isSearchable())->toBeFalse();
});

it('can disable searchable', function () {
    $table = Table::make()->searchable(false);

    expect($table->isSearchable())->toBeFalse();
});

it('has default search placeholder', function () {
    $table = Table::make();

    expect($table->getSearchPlaceholder())->toBe('Search...');
});

it('can set search placeholder', function () {
    $table = Table::make()->searchPlaceholder('Find records...');

    expect($table->getSearchPlaceholder())->toBe('Find records...');
});

it('has default empty state heading', function () {
    $table = Table::make();

    expect($table->getEmptyStateHeading())->toBe('No records found');
});

it('can set empty state heading', function () {
    $table = Table::make()->emptyStateHeading('Nothing here');

    expect($table->getEmptyStateHeading())->toBe('Nothing here');
});

it('has default empty state icon', function () {
    $table = Table::make();

    expect($table->getEmptyStateIcon())->toBe('pi pi-search');
});

it('can set empty state icon', function () {
    $table = Table::make()->emptyStateIcon('pi pi-inbox');

    expect($table->getEmptyStateIcon())->toBe('pi pi-inbox');
});

it('has null empty state description by default', function () {
    $table = Table::make();

    expect($table->getEmptyStateDescription())->toBeNull();
});

it('can set empty state description', function () {
    $table = Table::make()->emptyStateDescription('Try adjusting your search');

    expect($table->getEmptyStateDescription())->toBe('Try adjusting your search');
});

it('has default record key name of id', function () {
    $table = Table::make();

    expect($table->getRecordKeyName())->toBe('id');
});

it('can set record key', function () {
    $table = Table::make()->recordKey('uuid');

    expect($table->getRecordKeyName())->toBe('uuid');
});

it('is not striped by default', function () {
    $table = Table::make();

    expect($table->isStriped())->toBeFalse();
});

it('can enable striped', function () {
    $table = Table::make()->striped();

    expect($table->isStriped())->toBeTrue();
});

it('is not selectable without bulk actions', function () {
    $table = Table::make();

    expect($table->isSelectable())->toBeFalse();
});

it('is selectable when bulk actions are present', function () {
    $table = Table::make()->bulkActions([
        \Primix\Actions\BulkAction::make('delete'),
    ]);

    expect($table->isSelectable())->toBeTrue();
});

it('can disable selectable even with bulk actions', function () {
    $table = Table::make()
        ->selectable(false)
        ->bulkActions([
            \Primix\Actions\BulkAction::make('delete'),
        ]);

    expect($table->isSelectable())->toBeFalse();
});

it('has default filters layout of dropdown', function () {
    $table = Table::make();

    expect($table->getFiltersLayout())->toBe(FiltersLayout::Dropdown);
});

it('can set filters layout', function () {
    $table = Table::make()->filtersLayout(FiltersLayout::AboveContent);

    expect($table->getFiltersLayout())->toBe(FiltersLayout::AboveContent);
});

it('detects toggleable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
        TextColumn::make('status')->toggleable(),
    ]);

    expect($table->hasToggleableColumns())->toBeTrue();
    expect($table->getToggleableColumns())->toHaveCount(1);
});

it('detects no toggleable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
    ]);

    expect($table->hasToggleableColumns())->toBeFalse();
});

it('detects individually searchable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title')->individuallySearchable(),
        TextColumn::make('status'),
    ]);

    expect($table->hasIndividuallySearchableColumns())->toBeTrue();
});

it('detects editable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
        CheckboxColumn::make('is_active'),
    ]);

    expect($table->hasEditableColumns())->toBeTrue();
});

it('detects summarizable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
        TextColumn::make('amount')->summarize(Count::make()),
    ]);

    expect($table->hasSummarizableColumns())->toBeTrue();
});

it('calculates max summarizers count', function () {
    $table = Table::make()->columns([
        TextColumn::make('title'),
        TextColumn::make('amount')->summarize([Count::make(), Sum::make()]),
        TextColumn::make('price')->summarize(Count::make()),
    ]);

    expect($table->getMaxSummarizersCount())->toBe(2);
});

it('gets visible actions excluding hidden', function () {
    $table = Table::make()->actions([
        Action::make('edit'),
        Action::make('delete')->hidden(),
    ]);

    expect($table->getVisibleActions())->toHaveCount(1);
});

it('gets searchable columns', function () {
    $table = Table::make()->columns([
        TextColumn::make('title')->searchable(),
        TextColumn::make('status'),
    ]);

    expect($table->getSearchableColumns())->toHaveCount(1);
});

it('serializes to array', function () {
    $table = Table::make()->columns([
        TextColumn::make('title')->searchable()->sortable(),
    ]);

    $array = $table->toArray();

    expect($array)
        ->toHaveKey('columns')
        ->toHaveKey('filters')
        ->toHaveKey('actions')
        ->toHaveKey('bulkActions')
        ->toHaveKey('headerActions')
        ->toHaveKey('bulkBarActions')
        ->toHaveKey('columnToggleActions')
        ->toHaveKey('searchable', true)
        ->toHaveKey('searchPlaceholder', 'Search...')
        ->toHaveKey('perPage', 10)
        ->toHaveKey('perPageOptions')
        ->toHaveKey('striped', false)
        ->toHaveKey('selectable', false)
        ->toHaveKey('filtersLayout', 'dropdown')
        ->toHaveKey('emptyState');
});

it('is not tree by default', function () {
    $table = Table::make();

    expect($table->isTree())->toBeFalse();
});

it('can enable tree mode', function () {
    $table = Table::make()->tree('children', 'parent_id');

    expect($table->isTree())->toBeTrue();
    expect($table->getChildrenRelationship())->toBe('children');
    expect($table->getParentKeyColumn())->toBe('parent_id');
});

it('has table layout by default', function () {
    $table = Table::make();

    expect($table->getLayout())->toBe('table');
});

it('can enable grid layout', function () {
    $table = Table::make()->grid(4);

    expect($table->getLayout())->toBe('grid');
    expect($table->getGridColumns())->toBe(4);
});

it('has default grid columns of 3', function () {
    $table = Table::make()->grid();

    expect($table->getGridColumns())->toBe(3);
});

it('is not virtual scroll by default', function () {
    $table = Table::make();

    expect($table->isVirtualScroll())->toBeFalse();
});

it('can enable virtual scroll', function () {
    $table = Table::make()->virtualScroll();

    expect($table->isVirtualScroll())->toBeTrue();
    expect($table->getVirtualScrollItemSize())->toBe(46);
});

it('can set virtual scroll item size', function () {
    $table = Table::make()->virtualScroll(true, 60);

    expect($table->getVirtualScrollItemSize())->toBe(60);
});

it('includes tree and grid props in array', function () {
    $table = Table::make()->tree()->grid(4);

    $array = $table->toArray();

    expect($array)
        ->toHaveKey('tree', true)
        ->toHaveKey('childrenRelationship', 'children')
        ->toHaveKey('layout', 'grid')
        ->toHaveKey('gridColumns', 4)
        ->toHaveKey('virtualScroll', false);
});

<?php

namespace Primix\Tables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use LiVue\Features\SupportPagination\UsePagination;
use Primix\Tables\Columns\Column;
use Primix\Tables\Concerns\ExecutesBulkActions;
use Primix\Tables\Concerns\ManagesColumnToggling;
use Primix\Tables\Concerns\ManagesRecordSelection;
use Primix\Tables\Concerns\ManagesTableFilters;
use Primix\Tables\Concerns\ManagesTableReordering;

trait HasTable
{
    use ExecutesBulkActions;
    use ManagesColumnToggling;
    use ManagesRecordSelection;
    use ManagesTableFilters;
    use ManagesTableReordering;
    use UsePagination;

    public string $tableSearch = '';

    public string $tableSortColumn = '';

    public string $tableSortDirection = 'asc';

    public int $tablePerPage = 10;

    public string $tableLayout = '';

    public array $tableColumnSearches = [];

    public array $tableInlineCreateData = [];

    protected ?Table $cachedTable = null;

    abstract protected function table(Table $table): Table;

    public function getTable(): Table
    {
        if ($this->cachedTable === null) {
            $this->cachedTable = $this->table(Table::make()->livue($this));
            $this->initializeTableLayoutState($this->cachedTable);
        }

        return $this->cachedTable;
    }

    public function getTableRecords(): LengthAwarePaginator
    {
        $table = $this->getTable();

        if ($table->isEmbedded()) {
            return $this->getEmbeddedTableRecords($table);
        }

        $query = $this->getFilteredTableQuery();

        // Apply group ordering first (ensures grouped records are contiguous)
        if ($table->isGrouped()) {
            $query->orderBy($table->getGroup()->getColumn());
        }

        // Apply sorting
        if ($this->tableSortColumn) {
            $query->orderBy($this->tableSortColumn, $this->tableSortDirection);
        } elseif ($table->isReorderable()) {
            // Default sort by order column when reorderable and no user sort
            $query->orderBy($table->getOrderColumn());
        }

        return $this->paginate($query, $this->tablePerPage);
    }

    /**
     * Build the filtered query (search + filters applied, no sorting or pagination).
     */
    public function getFilteredTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $table = $this->getTable();
        $query = clone $table->getQuery();

        $this->initializeToggleableColumns();

        // Apply global search
        if ($this->tableSearch) {
            $searchableColumns = collect($table->getGloballySearchableColumns())
                ->map(fn (Column $col) => $col->getSearchColumn());

            if ($searchableColumns->isNotEmpty()) {
                $query->where(function ($q) use ($searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhere($column, 'like', "%{$this->tableSearch}%");
                    }
                });
            }
        }

        // Apply per-column search
        if (! empty($this->tableColumnSearches)) {
            foreach ($this->tableColumnSearches as $columnName => $searchValue) {
                if ($searchValue === '' || $searchValue === null) {
                    continue;
                }

                $col = collect($table->getColumns())
                    ->first(fn (Column $c) => $c->getName() === $columnName);

                if ($col && $col->isIndividuallySearchable()) {
                    $query->where($col->getSearchColumn(), 'like', "%{$searchValue}%");
                }
            }
        }

        // Apply filters
        foreach ($table->getFilters() as $filter) {
            $value = $this->tableFilters[$filter->getName()] ?? null;

            if ($value !== null && $value !== '') {
                $query = $filter->apply($query, $value);
            }
        }

        return $query;
    }

    /**
     * Calculate summary values for all columns with summarizers.
     *
     * @return array<string, array<array{label: string, value: string|null}>>
     */
    public function getTableSummary(): array
    {
        $table = $this->getTable();

        if ($table->isEmbedded()) {
            return [];
        }

        if (! $table->hasSummarizableColumns()) {
            return [];
        }

        $query = $this->getFilteredTableQuery();

        $summary = [];

        foreach ($table->getVisibleColumns() as $column) {
            if (! $column->hasSummarizers()) {
                $summary[$column->getName()] = [];

                continue;
            }

            $results = [];

            foreach ($column->getSummarizers() as $summarizer) {
                $results[] = $summarizer->resolve(clone $query, $column->getName());
            }

            $summary[$column->getName()] = $results;
        }

        return $summary;
    }

    public function searchTable(?string $search): void
    {
        $this->tableSearch = $search ?? '';
        $this->resetPage();
    }

    public function searchTableColumn(string $column, ?string $search): void
    {
        if ($search === null || $search === '') {
            unset($this->tableColumnSearches[$column]);
        } else {
            $this->tableColumnSearches[$column] = $search;
        }

        $this->resetPage();
    }

    public function sortTable(string $column): void
    {
        if ($this->tableSortColumn === $column) {
            $this->tableSortDirection = $this->tableSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->tableSortColumn = $column;
            $this->tableSortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function setTablePerPage(int $perPage): void
    {
        $this->tablePerPage = $perPage;
        $this->resetPage();
    }

    public function setTableLayout(string $layout): void
    {
        if (! in_array($layout, ['table', 'grid'], true)) {
            throw new \InvalidArgumentException("Unsupported table layout [{$layout}]. Supported layouts are [table, grid].");
        }

        $this->tableLayout = $layout;

        if ($this->cachedTable !== null) {
            $this->cachedTable->layout($layout);
        }
    }

    public function toggleTableLayout(): void
    {
        if (! $this->getTable()->isLayoutSwitchable()) {
            return;
        }

        $this->setTableLayout($this->tableLayout === 'grid' ? 'table' : 'grid');
    }

    /**
     * Update an editable column's state.
     * Called from Vue when a user edits an inline column value.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateTableColumnState(string $columnName, mixed $recordKey, mixed $value): void
    {
        $table = $this->getTable();

        $column = collect($table->getColumns())
            ->first(fn (Column $col) => $col->getName() === $columnName
                && method_exists($col, 'isEditable')
                && $col->isEditable());

        if (! $column) {
            throw new \InvalidArgumentException("No editable column [{$columnName}] found.");
        }

        $query = clone $table->getQuery();
        $record = $query->where($table->getRecordKeyName(), $recordKey)->firstOrFail();

        $column->updateState($record, $value);
    }

    public function createTableRecord(array $data = []): void
    {
        $table = $this->getTable();

        if (! $table->hasInlineCreate()) {
            return;
        }

        if (empty($data)) {
            $data = $this->tableInlineCreateData;
        }

        // Persist data so inputs re-hydrate their values if validation fails
        $this->tableInlineCreateData = $data;

        $rules = [];
        $validationMessages = [];

        foreach ($table->getColumns() as $column) {
            if (method_exists($column, 'isEditable') && $column->isEditable()
                && method_exists($column, 'getRules') && $column->getRules() !== null) {
                $rules[$column->getName()] = $column->getRules();
                $validationMessages = array_merge($validationMessages, $column->getValidationMessages());
            }
        }

        if (! empty($rules)) {
            Validator::make($data, $rules, $validationMessages)->validate();
        }

        $this->performInlineCreate($table, $data);

        $this->tableInlineCreateData = [];
        $this->resetTableCache();
    }

    protected function performInlineCreate(Table $table, array $data): void
    {
        if ($table->getInlineCreateCallback() !== null) {
            $this->evaluate($table->getInlineCreateCallback(), ['data' => $data]);

            return;
        }

        $query = $table->getQuery();

        if ($query === null) {
            throw new \LogicException(
                'Cannot use inlineCreate() without a query. Specify ->inlineCreateUsing() or override performInlineCreate().'
            );
        }

        $query->getModel()->newInstance()->fill($data)->save();
    }

    protected function resetTableCache(): void
    {
        $this->cachedTable = null;
    }

    protected function initializeTableLayoutState(Table $table): void
    {
        if ($this->tableLayout === '') {
            $this->tableLayout = $table->getLayout();
        }

        $table->layout($this->tableLayout);
    }

    private function getEmbeddedTableRecords(Table $table): LengthAwarePaginator
    {
        $items = collect($table->getEmbeddedRecords());

        // Apply global search
        if ($this->tableSearch) {
            $searchableColumns = $table->getGloballySearchableColumns();

            $items = $items->filter(function ($item) use ($searchableColumns) {
                foreach ($searchableColumns as $col) {
                    if (str_contains(
                        strtolower((string) data_get($item, $col->getName())),
                        strtolower($this->tableSearch)
                    )) {
                        return true;
                    }
                }

                return false;
            });
        }

        // Apply sorting
        if ($this->tableSortColumn) {
            $direction = $this->tableSortDirection === 'desc' ? 'sortByDesc' : 'sortBy';
            $items = $items->{$direction}(fn ($item) => data_get($item, $this->tableSortColumn));
            $items = $items->values();
        }

        // Paginate manually
        $total = $items->count();
        $page = $this->page;
        $perPage = $this->tablePerPage;
        $sliced = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }
}

<?php

namespace Primix\Tables;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View;
use Primix\Support\Components\ComponentContainer;
use Primix\Tables\Concerns\AnalyzesColumnCapabilities;
use Primix\Tables\Concerns\HasEmptyState;
use Primix\Tables\Concerns\HasGridLayout;
use Primix\Tables\Concerns\HasTableActions;
use Primix\Tables\Concerns\HasTreeStructure;
use Primix\Tables\Concerns\ManagesColumnVisibility;
use Primix\Support\SchemaBuilder;
use Primix\Tables\Enums\FiltersLayout;

class Table extends ComponentContainer implements Htmlable
{
    use AnalyzesColumnCapabilities;
    use HasEmptyState;
    use HasTableActions;
    use ManagesColumnVisibility;
    use HasTreeStructure;
    use HasGridLayout;

    public static function configure(Table $table): Table
    {
        return $table;
    }

    protected array $columns = [];

    protected array $filters = [];

    protected ?Builder $query = null;

    protected ?array $embeddedRecords = null;

    protected int $defaultPerPage = 10;

    protected array $perPageOptions = [10, 25, 50, 100];

    protected bool|Closure $isSearchable = true;

    protected ?string $searchPlaceholder = null;

    protected ?string $recordKeyName = null;

    protected bool|Closure $isStriped = false;

    protected bool|Closure $isSelectable = true;

    protected FiltersLayout $filtersLayout = FiltersLayout::Dropdown;

    protected ?TableGroup $group = null;

    protected bool|Closure $isReorderable = false;

    protected string $orderColumn = 'sort_order';

    protected bool|Closure $isRowClickEnabled = true;

    protected ?Closure $recordUrlResolver = null;

    protected bool|Closure|null $inlineCreateEnabled = null;

    protected ?Closure $inlineCreateUsing = null;

    protected bool $inlineInput = false;


    /**
     * Build the table from an array of definitions.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string, \Closure>  $callbacks
     */
    public function fromSchema(array $definition, array $callbacks = []): static
    {
        $builder = app(SchemaBuilder::class);

        if (isset($definition['columns'])) {
            $this->columns($builder->build($definition['columns'], 'column', $callbacks));
        }

        if (isset($definition['filters'])) {
            $this->filters($builder->build($definition['filters'], 'filter', $callbacks));
        }

        if (isset($definition['actions'])) {
            $this->actions($builder->build($definition['actions'], 'action', $callbacks));
        }

        if (isset($definition['bulkActions'])) {
            $this->bulkActions($builder->build($definition['bulkActions'], 'action', $callbacks));
        }

        if (isset($definition['headerActions'])) {
            $this->headerActions($builder->build($definition['headerActions'], 'action', $callbacks));
        }

        if (isset($definition['bulkBarActions'])) {
            $this->bulkBarActions($builder->build($definition['bulkBarActions'], 'action', $callbacks));
        }

        if (isset($definition['columnToggleActions'])) {
            $this->columnToggleActions($builder->build($definition['columnToggleActions'], 'action', $callbacks));
        }

        // Table-level properties
        $tableProps = [
            'defaultPerPage', 'searchable', 'striped', 'selectable',
            'searchPlaceholder', 'recordKey', 'layout', 'grid', 'switchableLayout',
        ];

        foreach ($tableProps as $prop) {
            if (isset($definition[$prop])) {
                $method = lcfirst($prop);
                if (method_exists($this, $method)) {
                    $this->{$method}($definition[$prop]);
                }
            }
        }

        return $this;
    }

    public function columns(array $columns): static
    {
        $this->columns = $columns;
        $this->propagateContextToComponents($this->columns);

        if ($this->inlineInput) {
            foreach ($this->columns as $column) {
                if (method_exists($column, 'inlineInput')) {
                    $column->inlineInput(true);
                }
            }
        }

        return $this;
    }

    public function filters(array $filters): static
    {
        $this->filters = $filters;
        $this->propagateContextToComponents($this->filters);

        return $this;
    }

    public function query(Builder $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function embeddedRecords(array $records): static
    {
        $this->embeddedRecords = $records;

        return $this;
    }

    public function getEmbeddedRecords(): ?array
    {
        return $this->embeddedRecords;
    }

    public function isEmbedded(): bool
    {
        return $this->embeddedRecords !== null;
    }

    public function defaultPerPage(int $perPage): static
    {
        $this->defaultPerPage = $perPage;

        return $this;
    }

    public function perPageOptions(array $options): static
    {
        $this->perPageOptions = $options;

        return $this;
    }

    public function searchable(bool|Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function searchPlaceholder(?string $placeholder): static
    {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    public function recordKey(?string $keyName): static
    {
        $this->recordKeyName = $keyName;

        return $this;
    }

    public function striped(bool|Closure $condition = true): static
    {
        $this->isStriped = $condition;

        return $this;
    }

    public function selectable(bool|Closure $condition = true): static
    {
        $this->isSelectable = $condition;

        return $this;
    }

    public function filtersLayout(FiltersLayout $layout): static
    {
        $this->filtersLayout = $layout;

        return $this;
    }

    public function group(?TableGroup $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): ?TableGroup
    {
        return $this->group;
    }

    public function isGrouped(): bool
    {
        return $this->group !== null;
    }

    public function reorderable(bool|Closure $condition = true, string $column = 'sort_order'): static
    {
        $this->isReorderable = $condition;
        $this->orderColumn = $column;

        return $this;
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }

    public function getOrderColumn(): string
    {
        return $this->orderColumn;
    }

    public function disableRowClick(): static
    {
        $this->isRowClickEnabled = false;

        return $this;
    }

    public function rowClickEnabled(bool|Closure $condition = true): static
    {
        $this->isRowClickEnabled = $condition;

        return $this;
    }

    public function recordUrl(?Closure $resolver): static
    {
        $this->recordUrlResolver = $resolver;

        return $this;
    }

    public function isRowClickEnabled(): bool
    {
        return (bool) $this->evaluate($this->isRowClickEnabled);
    }

    public function getRecordUrlResolver(): ?Closure
    {
        return $this->recordUrlResolver;
    }

    public function inlineCreate(bool|Closure $condition = true): static
    {
        $this->inlineCreateEnabled = $condition;

        return $this;
    }

    public function inlineCreateUsing(?Closure $callback): static
    {
        $this->inlineCreateUsing = $callback;

        return $this;
    }

    public function hasInlineCreate(): bool
    {
        return (bool) $this->evaluate($this->inlineCreateEnabled ?? false);
    }

    public function isInlineCreateExplicitlyConfigured(): bool
    {
        return $this->inlineCreateEnabled !== null;
    }

    public function getInlineCreateCallback(): ?Closure
    {
        return $this->inlineCreateUsing;
    }

    public function getInlineCreateAction(): ?\Primix\Tables\Actions\AddAction
    {
        foreach ($this->actions as $action) {
            if ($action instanceof \Primix\Tables\Actions\AddAction) {
                return $action;
            }
        }

        return null;
    }

    public function inlineInput(bool $condition = true): static
    {
        $this->inlineInput = $condition;

        foreach ($this->columns as $column) {
            if (method_exists($column, 'inlineInput')) {
                $column->inlineInput($condition);
            }
        }

        return $this;
    }

    public function isInlineInput(): bool
    {
        return $this->inlineInput;
    }

    public function getRecordUrl(mixed $record): ?string
    {
        if (! $this->isRowClickEnabled()) {
            return null;
        }

        if ($this->recordUrlResolver === null) {
            return null;
        }

        return $this->evaluate($this->recordUrlResolver, ['record' => $record]);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFiltersLayout(): FiltersLayout
    {
        return $this->filtersLayout;
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }

    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable) && $this->hasGloballySearchableColumns();
    }

    public function getSearchPlaceholder(): string
    {
        return $this->searchPlaceholder ?? __('primix-tables::tables.search_placeholder');
    }

    public function getRecordKeyName(): string
    {
        return $this->recordKeyName ?? 'id';
    }

    public function isStriped(): bool
    {
        return (bool) $this->evaluate($this->isStriped);
    }

    public function isSelectable(): bool
    {
        return (bool) $this->evaluate($this->isSelectable) && $this->hasBulkActions();
    }

    public function toHtml(): string
    {
        $livue = $this->getLiVue();

        return View::make('primix-tables::table', [
            'table' => $this,
            'records' => $livue->getTableRecords(),
            'summary' => $this->hasSummarizableColumns() ? $livue->getTableSummary() : [],
        ])->render();
    }

    public function toArray(): array
    {
        return [
            'columns' => array_map(fn ($c) => $c->toVueProps(), $this->columns),
            'filters' => array_map(fn ($f) => $f->toVueProps(), $this->filters),
            'actions' => array_map(fn ($a) => $a->toVueProps(), $this->actions),
            'bulkActions' => array_map(fn ($a) => $a->toVueProps(), $this->bulkActions),
            'headerActions' => array_map(fn ($a) => $a->toVueProps(), $this->headerActions),
            'bulkBarActions' => array_map(fn ($a) => $a->toVueProps(), $this->getBulkBarActions()),
            'columnToggleActions' => array_map(fn ($a) => $a->toVueProps(), $this->getColumnToggleActions()),
            'layoutToggleActions' => array_map(fn ($a) => $a->toVueProps(), $this->getLayoutToggleActions()),
            'searchable' => $this->isSearchable(),
            'searchPlaceholder' => $this->getSearchPlaceholder(),
            'perPage' => $this->defaultPerPage,
            'perPageOptions' => $this->perPageOptions,
            'striped' => $this->isStriped(),
            'selectable' => $this->isSelectable(),
            'hasEditableColumns' => $this->hasEditableColumns(),
            'hasSummarizableColumns' => $this->hasSummarizableColumns(),
            'maxSummarizersCount' => $this->getMaxSummarizersCount(),
            'filtersLayout' => $this->getFiltersLayout()->value,
            'tree' => $this->isTree(),
            'childrenRelationship' => $this->getChildrenRelationship(),
            'parentKeyColumn' => $this->getParentKeyColumn(),
            'layout' => $this->getLayout(),
            'gridColumns' => $this->getGridColumns(),
            'layoutSwitchable' => $this->isLayoutSwitchable(),
            'virtualScroll' => $this->isVirtualScroll(),
            'virtualScrollItemSize' => $this->getVirtualScrollItemSize(),
            'emptyState' => [
                'heading' => $this->getEmptyStateHeading(),
                'description' => $this->getEmptyStateDescription(),
                'icon' => $this->getEmptyStateIcon(),
            ],
            'inlineCreate' => $this->hasInlineCreate(),
        ];
    }
}

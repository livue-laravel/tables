@php
    /** @var \Primix\Tables\Table $table */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $records */
    /** @var array $summary */
    $livue = $this;
    $visibleColumns = $table->getVisibleColumns();
    $rowActions = $table->getRowActions();
    $hasActions = count($rowActions) > 0;
    $showActionsColumn = $hasActions || $table->hasInlineCreate();
    $isReorderEnabled = $livue->isReorderEnabled();
    $isGrouped = $table->isGrouped();
    $group = $table->getGroup();
    $colCount = count($visibleColumns)
        + ($table->isSelectable() ? 1 : 0)
        + ($showActionsColumn ? 1 : 0)
        + ($isReorderEnabled ? 1 : 0);
    $hasIndividualSearch = $table->hasIndividuallySearchableColumns();
    $headerActions = $table->getHeaderActions();
    $recordIds = $records->pluck($table->getRecordKeyName())->toArray();
    $recordCount = count($recordIds);
    $spaEnabled = (bool) ($spa ?? false);
@endphp

@once
    @livueLoadStyle('primix-tables', 'primix/tables')
    @livueLoadScript('primix-tables', 'primix/tables', ['type' => 'module'])
@endonce

<div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
    {{-- Header Actions --}}
    @if(count($headerActions) > 0)
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
            <div class="flex items-center justify-end gap-2">
                @foreach($headerActions as $action)
                    @if($action->isVisible())
                        {{ $action }}
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Search, Filters, and Column Toggle --}}
    @if($table->isSearchable() || count($table->getFilters()) > 0 || $table->hasToggleableColumns())
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-4 sm:px-6">
            <div class="flex items-center justify-end gap-4">
                @if($table->isSearchable())
                    <div class="w-80">
                        <input
                            type="search"
                            :value="tableSearch"
                            @input="searchTable([$event.target.value], { debounce: 300 })"
                            placeholder="{{ $table->getSearchPlaceholder() }}"
                            class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 bg-transparent"
                        >
                    </div>
                @endif

                @if(count($table->getFilters()) > 0)
                    @php($hasActiveFilters = $livue->hasActiveFilters())
                    @if($table->getFiltersLayout() === \Primix\Tables\Enums\FiltersLayout::AboveContent)
                        @include('primix-tables::filters.filters-above', ['table' => $table])
                    @elseif($table->getFiltersLayout() === \Primix\Tables\Enums\FiltersLayout::Dropdown)
                        @include('primix-tables::filters.filters-dropdown', ['table' => $table, 'hasActiveFilters' => $hasActiveFilters])
                    @elseif($table->getFiltersLayout() === \Primix\Tables\Enums\FiltersLayout::Modal)
                        @include('primix-tables::filters.filters-modal', ['table' => $table, 'hasActiveFilters' => $hasActiveFilters])
                    @endif
                @endif

                @if($table->hasToggleableColumns())
                    @include('primix-tables::columns.column-toggle', ['table' => $table])
                @endif
            </div>
        </div>
    @endif

    {{-- Bulk Actions Bar --}}
    @if($table->hasBulkActions())
        <div v-show="selectedRecords.length > 0" class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    @{{ selectedRecords.length }} selected
                </span>
                <div class="flex items-center gap-2">
                    @foreach($table->getVisibleBulkActions() as $bulkAction)
                        {{ $bulkAction  }}
                    @endforeach
                </div>
                @foreach($table->getBulkBarActions() as $bulkBarAction)
                    {{ $bulkBarAction }}
                @endforeach
            </div>
        </div>
    @endif

    {{-- Table / Grid --}}
    @if($table->getLayout() === 'grid')
        {{-- Grid/Card Layout --}}
        @if($records->isEmpty())
            <div class="text-center py-12 px-4">
                @if($table->getEmptyStateIcon())
                    <div class="mx-auto">
                        {!! app(\Primix\Support\Icons\IconManager::class)->render($table->getEmptyStateIcon(), 'text-gray-400', 'xl') !!}
                    </div>
                @endif
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $table->getEmptyStateHeading() }}</h3>
                @if($table->getEmptyStateDescription())
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $table->getEmptyStateDescription() }}</p>
                @endif
            </div>
        @else
            <div class="grid gap-4 p-4" style="grid-template-columns: repeat({{ $table->getGridColumns() }}, minmax(0, 1fr));">
                @foreach($records as $record)
                    @php($recordUrl = $table->getRecordUrl($record))
                    <div
                        class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm overflow-hidden {{ $recordUrl ? 'cursor-pointer hover:ring-2 hover:ring-primary-500 hover:border-primary-500 transition-shadow' : '' }}"
                        @if($recordUrl)
                            @click="
                                if (!$event.target.closest('input, button, a, [role=button]')) {
                                    const doc = $event.target.ownerDocument;
                                    const link = doc.createElement('a');
                                    link.href = '{{ $recordUrl }}';
                                    @if($spaEnabled)
                                    link.setAttribute('data-livue-navigate', 'true');
                                    @endif
                                    doc.body.appendChild(link);
                                    link.click();
                                    link.remove();
                                }
                            "
                        @endif
                    >
                        <div class="p-4 space-y-2">
                            @foreach($visibleColumns as $column)
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $column->getLabel() }}</span>
                                    <span class="text-sm text-gray-900 dark:text-white text-right">
                                        {!! $column->record($record)->toHtml() !!}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        @if($hasActions || $table->isSelectable())
                            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50">
                                @if($table->isSelectable())
                                    <input
                                        type="checkbox"
                                        :checked="selectedRecords.includes({{ json_encode($record->{$table->getRecordKeyName()}) }})"
                                        @change="toggleSelectRecord({{ json_encode($record->{$table->getRecordKeyName()}) }})"
                                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
                                    >
                                @else
                                    <span></span>
                                @endif
                                @if($hasActions)
                                    <div class="flex items-center gap-1">
                                        @foreach($rowActions as $action)
                                            @php($action->record($record)->recordTitle(data_get($record, $table->getRecordTitleAttribute() ?? 'id')))
                                            @if($action->isVisible())
                                                {{ $action }}
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    @if($isReorderEnabled)
                        <th scope="col" class="w-8 px-2">
                            <span class="sr-only">Reorder</span>
                        </th>
                    @endif

                    @if($table->isSelectable())
                        <th scope="col" class="relative px-7 sm:w-12 sm:px-6">
                            <input
                                type="checkbox"
                                @change="selectedRecords.length === {{ $recordCount }} ? livue.set('selectedRecords', []) : livue.set('selectedRecords', {{ json_encode($recordIds) }})"
                                :checked="selectedRecords.length === {{ $recordCount }} && selectedRecords.length > 0"
                                class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
                            >
                        </th>
                    @endif

                    @foreach($visibleColumns as $column)
                        <th
                            scope="col"
                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white {{ $column->isSortable() ? 'cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800' : '' }}"
                            style="{{ $column->getWidth() ? 'width: ' . $column->getWidth() : '' }}"
                            @if($column->isSortable()) @click="sortTable('{{ $column->getSortColumn() }}')" @endif
                        >
                            <span class="flex items-center gap-x-2">
                                {{ $column->getLabel() }}
                                @if($column->isSortable())
                                    <span v-if="tableSortColumn === '{{ $column->getSortColumn() }}'">
                                        <svg v-if="tableSortDirection === 'asc'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                @endif
                            </span>
                        </th>
                    @endforeach

                    @if($showActionsColumn)
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>

                {{-- Inline Column Search --}}
                @if($hasIndividualSearch)
                    <tr class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                        @if($isReorderEnabled)
                            <th></th>
                        @endif
                        @if($table->isSelectable())
                            <th></th>
                        @endif
                        @foreach($visibleColumns as $column)
                            <th class="px-3 py-2">
                                @if($column->isIndividuallySearchable())
                                    <input
                                        type="search"
                                        :value="tableColumnSearches['{{ $column->getName() }}'] || ''"
                                        @input="searchTableColumn(['{{ $column->getName() }}', $event.target.value], { debounce: 300 })"
                                        placeholder="Search..."
                                        class="block w-full rounded-md border-0 px-2.5 py-1 text-xs text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-600 bg-transparent"
                                    >
                                @endif
                            </th>
                        @endforeach
                        @if($showActionsColumn)
                            <th></th>
                        @endif
                    </tr>
                @endif
            </thead>

            <tbody
                class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800"
                @if($isReorderEnabled) v-sort="'reorderTable'" v-sort.150ms @endif
            >
                @if($records->isEmpty())
                    <tr>
                        <td colspan="{{ $colCount }}" class="text-center py-12">
                            @if($table->getEmptyStateIcon())
                                <div class="mx-auto">
                                    {!! app(\Primix\Support\Icons\IconManager::class)->render($table->getEmptyStateIcon(), 'text-gray-400', 'xl') !!}
                                </div>
                            @endif
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $table->getEmptyStateHeading() }}</h3>
                            @if($table->getEmptyStateDescription())
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $table->getEmptyStateDescription() }}</p>
                            @endif
                        </td>
                    </tr>
                @elseif($isGrouped)
                    @php($grouped = $records->groupBy($group->getColumn()))
                    @foreach($grouped as $groupValue => $groupRecords)
                        <tr
                            class="bg-gray-100 dark:bg-gray-900 {{ $group->isCollapsible() ? 'cursor-pointer' : '' }}"
                            @if($group->isCollapsible())
                                @click="toggleGroupCollapse('{{ $groupValue }}')"
                            @endif
                        >
                            <td colspan="{{ $colCount }}" class="px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    @if($group->isCollapsible())
                                        <i :class="collapsedGroups.includes('{{ $groupValue }}') ? 'pi pi-chevron-right' : 'pi pi-chevron-down'" class="text-xs text-gray-500"></i>
                                    @endif
                                    <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $group->getTitle($groupValue) }}</span>
                                    <span class="text-xs text-gray-500">({{ $groupRecords->count() }})</span>
                                    @if($group->getDescription($groupValue, $groupRecords->count()))
                                        <span class="text-xs text-gray-400">{{ $group->getDescription($groupValue, $groupRecords->count()) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @foreach($groupRecords as $record)
                            @php($recordUrl = $table->getRecordUrl($record))
                            <tr
                                class="{{ $table->isStriped() ? 'even:bg-gray-50 dark:even:bg-gray-900/50' : '' }} {{ $recordUrl ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50' : '' }} {{ $table->isInlineInput() ? 'focus-within:bg-gray-50 dark:focus-within:bg-gray-700/30' : '' }}"
                                @if($group->isCollapsible())
                                    v-show="!collapsedGroups.includes('{{ $groupValue }}')"
                                @endif
                                @if($recordUrl)
                                    @click="
                                        if (!$event.target.closest('input, button, a, [role=button], [v-sort-handle]')) {
                                            const doc = $event.target.ownerDocument;
                                            const link = doc.createElement('a');
                                            link.href = '{{ $recordUrl }}';
                                            @if($spaEnabled)
                                            link.setAttribute('data-livue-navigate', 'true');
                                            @endif
                                            doc.body.appendChild(link);
                                            link.click();
                                            link.remove();
                                        }
                                    "
                                @endif
                            >
                                @include('primix-tables::partials.table-row-cells', [
                                    'record' => $record,
                                    'table' => $table,
                                    'visibleColumns' => $visibleColumns,
                                    'hasActions' => $hasActions,
                                    'showActionsColumn' => $showActionsColumn,
                                    'isReorderEnabled' => $isReorderEnabled,
                                ])
                            </tr>
                        @endforeach
                    @endforeach
                @else
                    @foreach($records as $record)
                        @php($recordUrl = $table->getRecordUrl($record))
                        <tr
                            class="{{ $table->isStriped() ? 'even:bg-gray-50 dark:even:bg-gray-900/50' : '' }} {{ $recordUrl ? 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50' : '' }} {{ $table->isInlineInput() ? 'focus-within:bg-gray-50 dark:focus-within:bg-gray-700/30' : '' }}"
                            @if($isReorderEnabled) v-sort-item="{{ $record->{$table->getRecordKeyName()} }}" @endif
                            @if($recordUrl)
                                @click="
                                    if (!$event.target.closest('input, button, a, [role=button], [v-sort-handle]')) {
                                        const doc = $event.target.ownerDocument;
                                        const link = doc.createElement('a');
                                        link.href = '{{ $recordUrl }}';
                                        @if($spaEnabled)
                                        link.setAttribute('data-livue-navigate', 'true');
                                        @endif
                                        doc.body.appendChild(link);
                                        link.click();
                                        link.remove();
                                    }
                                "
                            @endif
                        >
                            @include('primix-tables::partials.table-row-cells', [
                                'record' => $record,
                                'table' => $table,
                                'visibleColumns' => $visibleColumns,
                                'hasActions' => $hasActions,
                                'showActionsColumn' => $showActionsColumn,
                                'isReorderEnabled' => $isReorderEnabled,
                            ])
                        </tr>
                    @endforeach
                @endif

                @if($table->hasInlineCreate())
                    @include('primix-tables::partials.table-inline-create-row', [
                        'table' => $table,
                        'visibleColumns' => $visibleColumns,
                        'hasActions' => $hasActions,
                        'showActionsColumn' => $showActionsColumn,
                        'isReorderEnabled' => $isReorderEnabled,
                    ])
                @endif
            </tbody>

            {{-- Summary Footer --}}
            @if(!empty($summary))
                @include('primix-tables::columns.summarizers.summary-footer', [
                    'table' => $table,
                    'summary' => $summary,
                    'visibleColumns' => $visibleColumns,
                    'hasActions' => $hasActions,
                ])
            @endif
        </table>
    </div>
    @endif

    {{-- Pagination --}}
    @if($records->hasPages())
        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
            {{ $livue->links() }}
        </div>
    @endif
</div>

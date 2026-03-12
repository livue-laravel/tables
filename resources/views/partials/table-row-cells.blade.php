@if($isReorderEnabled)
    <td class="w-8 px-2">
        <span v-sort-handle class="cursor-grab text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <i class="pi pi-bars"></i>
        </span>
    </td>
@endif

@if($table->isSelectable())
    @php($recordKey = $record->{$table->getRecordKeyName()})
    <td class="relative px-7 sm:w-12 sm:px-6">
        <input
            type="checkbox"
            :checked="selectedRecords.includes({{ $recordKey }})"
            @change="selectedRecords.includes({{ $recordKey }}) ? selectedRecords.splice(selectedRecords.indexOf({{ $recordKey }}), 1) : selectedRecords.push({{ $recordKey }})"
            class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
        >
    </td>
@endif

@php($inlineInput = $table->isInlineInput())

@foreach($visibleColumns as $column)
    <td class="whitespace-nowrap px-3 {{ $inlineInput ? 'py-1' : 'py-4' }} text-sm text-gray-500 dark:text-gray-400">
        {{ $column->record($record) }}
    </td>
@endforeach

@if($showActionsColumn ?? $hasActions)
    <td class="relative whitespace-nowrap {{ $inlineInput ? 'py-1' : 'py-4' }} pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
        @if($hasActions)
            <div class="flex items-center justify-end gap-1">
                @foreach($rowActions ?? $table->getRowActions() as $action)
                    @php($action->record($record))
                    @if($action->isVisible())
                        {{ $action }}
                    @endif
                @endforeach
            </div>
        @endif
    </td>
@endif

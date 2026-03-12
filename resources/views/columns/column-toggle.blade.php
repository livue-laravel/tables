@php
    $toggleableColumns = $table->getToggleableColumns();
@endphp

@if(count($toggleableColumns) > 0)
    <script type="application/livue-setup">
        const columnTogglePopover = ref(null);
        return { columnTogglePopover };
    </script>

    <div class="relative inline-block">
        @foreach($table->getColumnToggleActions() as $columnToggleAction)
            {{ $columnToggleAction }}
        @endforeach

        <p-popover ref="columnTogglePopover">
            <div class="flex flex-col gap-2 p-2 min-w-[200px]">
                <span class="font-semibold text-sm text-gray-900 dark:text-white">Toggle Columns</span>

                @foreach($toggleableColumns as $column)
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input
                            type="checkbox"
                            :checked="!toggledHiddenColumns.includes('{{ $column->getName() }}')"
                            @change="toggleColumn('{{ $column->getName() }}')"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
                        >
                        {{ $column->getLabel() }}
                    </label>
                @endforeach
            </div>
        </p-popover>
    </div>
@endif

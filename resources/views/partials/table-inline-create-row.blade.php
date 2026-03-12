<tr class="bg-primary-50/30 dark:bg-primary-950/20">
    @if($isReorderEnabled)
        <td class="w-8 px-2"></td>
    @endif

    @if($table->isSelectable())
        <td class="relative px-7 sm:w-12 sm:px-6"></td>
    @endif

    @foreach($visibleColumns as $column)
        <td class="whitespace-nowrap px-3 py-2 text-sm">
            @if(method_exists($column, 'isEditable') && $column->isEditable())
                @if($column instanceof \Primix\Tables\Columns\TextInputColumn)
                    <input
                        type="{{ $column->getInputType() }}"
                        data-inline-field="{{ $column->getName() }}"
                        :value="tableInlineCreateData['{{ $column->getName() }}'] ?? ''"
                        @if($column->getStep()) step="{{ $column->getStep() }}" @endif
                        @if($column->getPlaceholder()) placeholder="{{ $column->getPlaceholder() }}" @endif
                        class="w-full rounded-md border border-surface-300 px-2 py-1.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none"
                    />
                    <span v-if="$errors['{{ $column->getName() }}']" class="text-xs text-red-600 mt-0.5 block" v-text="$errors['{{ $column->getName() }}']"></span>
                @elseif($column instanceof \Primix\Tables\Columns\SelectColumn)
                    <select
                        data-inline-field="{{ $column->getName() }}"
                        class="w-full rounded-md border border-surface-300 px-2 py-1.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none"
                    >
                        @if($column->getSelectPlaceholder())
                            <option value="">{{ $column->getSelectPlaceholder() }}</option>
                        @endif
                        @foreach($column->getOptions() as $optValue => $optLabel)
                            <option value="{{ $optValue }}" :selected="(tableInlineCreateData['{{ $column->getName() }}'] ?? '') == '{{ $optValue }}'">{{ $optLabel }}</option>
                        @endforeach
                    </select>
                    <span v-if="$errors['{{ $column->getName() }}']" class="text-xs text-red-600 mt-0.5 block" v-text="$errors['{{ $column->getName() }}']"></span>
                @elseif($column instanceof \Primix\Tables\Columns\ToggleColumn)
                    <input
                        type="checkbox"
                        data-inline-field="{{ $column->getName() }}"
                        data-inline-field-type="checkbox"
                        :checked="tableInlineCreateData['{{ $column->getName() }}'] ?? false"
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
                    />
                    <span v-if="$errors['{{ $column->getName() }}']" class="text-xs text-red-600 mt-0.5 block" v-text="$errors['{{ $column->getName() }}']"></span>
                @elseif($column instanceof \Primix\Tables\Columns\CheckboxColumn)
                    <input
                        type="checkbox"
                        data-inline-field="{{ $column->getName() }}"
                        data-inline-field-type="checkbox"
                        :checked="tableInlineCreateData['{{ $column->getName() }}'] ?? false"
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-600"
                    />
                    <span v-if="$errors['{{ $column->getName() }}']" class="text-xs text-red-600 mt-0.5 block" v-text="$errors['{{ $column->getName() }}']"></span>
                @endif
            @endif
        </td>
    @endforeach

    @if($showActionsColumn)
        <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
            @php($addAction = $table->getInlineCreateAction())
            @if($addAction && $addAction->isVisible())
                {{ $addAction }}
            @endif
        </td>
    @endif
</tr>

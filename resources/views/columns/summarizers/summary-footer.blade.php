@php
    /** @var \Primix\Tables\Table $table */
    /** @var array $summary */
    $maxSummarizers = $table->getMaxSummarizersCount();
    $isSelectable = $table->isSelectable();
@endphp

@if($maxSummarizers > 0)
    <tfoot class="bg-gray-50 dark:bg-gray-900 border-t-2 border-gray-300 dark:border-gray-600">
        @for($i = 0; $i < $maxSummarizers; $i++)
            <tr class="{{ $i > 0 ? 'border-t border-gray-200 dark:border-gray-700' : '' }}">
                @if($isSelectable)
                    <td class="px-7 sm:w-12 sm:px-6"></td>
                @endif

                @foreach($visibleColumns as $column)
                    @php
                        $columnSummary = $summary[$column->getName()] ?? [];
                        $entry = $columnSummary[$i] ?? null;
                    @endphp
                    <td class="whitespace-nowrap px-3 py-3 text-sm">
                        @if($entry)
                            <div class="space-y-0.5">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $entry['label'] }}
                                </span>
                                <span class="block font-semibold text-gray-900 dark:text-white">
                                    {{ $entry['value'] ?? '-' }}
                                </span>
                            </div>
                        @endif
                    </td>
                @endforeach

                @if($hasActions)
                    <td></td>
                @endif
            </tr>
        @endfor
    </tfoot>
@endif

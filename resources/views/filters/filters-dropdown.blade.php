@php
    $filters = $table->getFilters();
@endphp

<script type="application/livue-setup">
    const filterPopover = ref(null);
    return { filterPopover };
</script>

<div class="relative inline-block">
    <p-button
        icon="pi pi-filter"
        severity="secondary"
        outlined
        size="small"
        @click="(e) => filterPopover.toggle(e)"
    >
        @if($hasActiveFilters)
            <template #icon>
                <i class="pi pi-filter-fill text-primary-500"></i>
            </template>
        @endif
    </p-button>

    <p-popover ref="filterPopover">
        <div class="flex flex-col gap-4 p-2 min-w-[250px]">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-sm text-gray-900">{{ __('primix-tables::tables.filters') }}</span>
                @if($hasActiveFilters)
                    <p-button
                        label="{{ __('primix-tables::tables.reset') }}"
                        severity="secondary"
                        text
                        size="small"
                        @click="resetTableFilters()"
                    />
                @endif
            </div>

            @foreach($filters as $filter)
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $filter->getLabel() }}</label>
                    {{ $filter }}
                </div>
            @endforeach
        </div>
    </p-popover>
</div>

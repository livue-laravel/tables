@php
    $filters = $table->getFilters();
@endphp

<div class="inline-block">
    <p-button
        icon="pi pi-filter"
        severity="secondary"
        outlined
        size="small"
        @click="showFilterModal = true"
    >
        @if($hasActiveFilters)
            <template #icon>
                <i class="pi pi-filter-fill text-primary-500"></i>
            </template>
        @endif
    </p-button>

    <p-dialog
        v-model:visible="showFilterModal"
        header="{{ __('primix-tables::tables.filters') }}"
        modal
        :style="{ width: '400px' }"
    >
        <div class="flex flex-col gap-4">
            @foreach($filters as $filter)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getLabel() }}</label>
                    {{ $filter }}
                </div>
            @endforeach
        </div>

        <template #footer>
            <div class="flex justify-between w-full">
                <p-button
                    label="{{ __('primix-tables::tables.reset_all') }}"
                    severity="secondary"
                    text
                    @click="resetTableFilters()"
                    :disabled="{{ $hasActiveFilters ? 'false' : 'true' }}"
                />
                <p-button
                    label="{{ __('primix-tables::tables.done') }}"
                    @click="showFilterModal = false"
                />
            </div>
        </template>
    </p-dialog>
</div>

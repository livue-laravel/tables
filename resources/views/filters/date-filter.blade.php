@php
    /** @var \Primix\Tables\Filters\DateFilter $component */
    $filterName = $component->getName();
    $isRange = $component->isRange();
    $format = $component->getFormat();
    $minDate = $component->getMinDate();
    $maxDate = $component->getMaxDate();

    if ($minDate instanceof \Carbon\CarbonInterface) {
        $minDate = $minDate->toDateString();
    }
    if ($maxDate instanceof \Carbon\CarbonInterface) {
        $maxDate = $maxDate->toDateString();
    }
@endphp

@if($isRange)
    <div class="flex flex-col gap-2">
        <p-date-picker
            :model-value="tableFilters['{{ $filterName }}']?.from || null"
            @update:model-value="(val) => {
                const current = tableFilters['{{ $filterName }}'] || {};
                setTableFilter('{{ $filterName }}', { ...current, from: val });
            }"
            placeholder="From"
            @if($format) date-format="{{ $format }}" @endif
            @if($minDate) :min-date="new Date('{{ $minDate }}')" @endif
            @if($maxDate) :max-date="new Date('{{ $maxDate }}')" @endif
            show-icon
            show-button-bar
            fluid
        />
        <p-date-picker
            :model-value="tableFilters['{{ $filterName }}']?.until || null"
            @update:model-value="(val) => {
                const current = tableFilters['{{ $filterName }}'] || {};
                setTableFilter('{{ $filterName }}', { ...current, until: val });
            }"
            placeholder="Until"
            @if($format) date-format="{{ $format }}" @endif
            @if($minDate) :min-date="new Date('{{ $minDate }}')" @endif
            @if($maxDate) :max-date="new Date('{{ $maxDate }}')" @endif
            show-icon
            show-button-bar
            fluid
        />
    </div>
@else
    <p-date-picker
        :model-value="tableFilters['{{ $filterName }}'] || null"
        @update:model-value="(val) => setTableFilter('{{ $filterName }}', val)"
        placeholder="{{ $component->getLabel() }}"
        @if($format) date-format="{{ $format }}" @endif
        @if($minDate) :min-date="new Date('{{ $minDate }}')" @endif
        @if($maxDate) :max-date="new Date('{{ $maxDate }}')" @endif
        show-icon
        show-button-bar
        fluid
    />
@endif

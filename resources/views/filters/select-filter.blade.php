@php
    /** @var \Primix\Tables\Filters\SelectFilter $component */
    $filterName = $component->getName();
    $options = collect($component->getOptions())
        ->map(fn ($label, $value) => ['label' => $label, 'value' => $value])
        ->values()
        ->toArray();
@endphp

<p-select
    :model-value="tableFilters['{{ $filterName }}'] || null"
    @update:model-value="(val) => setTableFilter('{{ $filterName }}', val)"
    :options="{!! \Illuminate\Support\Js::from($options) !!}"
    option-label="label"
    option-value="value"
    placeholder="{{ $component->getLabel() }}"
    show-clear
    fluid
/>

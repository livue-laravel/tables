@php
    /** @var \Primix\Tables\Filters\TrashedFilter $component */
    $filterName = $component->getName();
    $withoutLabel = $component->getWithoutLabel();
    $withLabel = $component->getWithLabel();
    $onlyLabel = $component->getOnlyLabel();

    $options = [
        ['label' => $withoutLabel, 'value' => 'without'],
        ['label' => $withLabel, 'value' => 'with'],
        ['label' => $onlyLabel, 'value' => 'only'],
    ];
@endphp

<p-select-button
    :model-value="tableFilters['{{ $filterName }}'] ?? 'without'"
    @update:model-value="(val) => setTableFilter('{{ $filterName }}', val)"
    :options="{!! \Illuminate\Support\Js::from($options) !!}"
    option-label="label"
    option-value="value"
    :allow-empty="false"
    fluid
/>

@php
    /** @var \Primix\Tables\Filters\TernaryFilter $component */
    $filterName = $component->getName();
    $trueLabel = $component->getTrueLabel();
    $falseLabel = $component->getFalseLabel();
    $allLabel = $component->getAllLabel();

    $options = [
        ['label' => $allLabel, 'value' => null],
        ['label' => $trueLabel, 'value' => true],
        ['label' => $falseLabel, 'value' => false],
    ];
@endphp

<p-select-button
    :model-value="tableFilters['{{ $filterName }}'] ?? null"
    @update:model-value="(val) => setTableFilter('{{ $filterName }}', val)"
    :options="{!! \Illuminate\Support\Js::from($options) !!}"
    option-label="label"
    option-value="value"
    :allow-empty="false"
    fluid
/>

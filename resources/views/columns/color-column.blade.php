@php
    $state = $component->getState($record);
    $swatchShape = $component->getSwatchShape();
    $swatchSize = $component->getSwatchSize();

    $shapeClass = match ($swatchShape) {
        'circular' => 'rounded-full',
        'square' => 'rounded-none',
        default => 'rounded-md',
    };

    $sizeStyle = match ($swatchSize) {
        'sm' => 'width: 20px; height: 20px;',
        'lg' => 'width: 32px; height: 32px;',
        default => 'width: 24px; height: 24px;',
    };
@endphp

<div class="primix-color-column flex items-center gap-2">
    @if($state)
        <span
            class="{{ $shapeClass }} inline-block border border-surface-300"
            style="{{ $sizeStyle }} background-color: {{ $state }};"
        ></span>
        @if($component->isCopyable())
            <span class="text-sm text-surface-600">{{ $state }}</span>
        @endif
    @else
        <span class="text-surface-400">{{ $component->getPlaceholder() ?? '—' }}</span>
    @endif
</div>

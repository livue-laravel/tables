@php
    $state = $component->getState($record);
    $color = $component->getColorForState($state);
    $icon = $component->getIconForState($state);
    $label = $component->getLabelForState($state);
@endphp

<div class="primix-badge-column">
    <p-tag
        @if($color) severity="{{ app(\Primix\Support\Colors\ColorManager::class)->toPrimeVueSeverity($color) }}" @endif
        :value="'{{ e($label ?? $component->getPlaceholder() ?? '') }}'"
    >
        @if($icon)
            <template #icon>
                {!! app(\Primix\Support\Icons\IconManager::class)->render($icon, 'mr-1') !!}
            </template>
        @endif
    </p-tag>
</div>

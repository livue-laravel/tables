@php
    $state = $component->getState($record);
    $isBoolean = $component->isBoolean();
    $size = $component->getSize() ?? 'md';

    if ($isBoolean) {
        $icon = $state ? $component->getTrueIcon() : $component->getFalseIcon();
        $color = $state ? $component->getTrueColor() : $component->getFalseColor();
    } else {
        $icon = $component->getIconForState($state);
        $color = $component->getColorForState($state);
    }

    $colorHex = $color
        ? app(\Primix\Support\Colors\ColorManager::class)->toHex($color)
        : null;
@endphp

<div class="primix-icon-column">
    @if($icon)
        {!! app(\Primix\Support\Icons\IconManager::class)->render($icon, null, $size, $colorHex ? 'color: ' . $colorHex : null) !!}
    @elseif($component->getPlaceholder())
        <span class="text-surface-400">{{ $component->getPlaceholder() }}</span>
    @endif
</div>

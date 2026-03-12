@php
    $state = $component->getState($record);
    $isBadge = $component->isBadge();
    $description = $component->getDescription();
    $descriptionPosition = $component->getDescriptionPosition();
    $weight = $component->getWeight();
    $color = $component->getColor();
    $characterLimit = $component->getCharacterLimit();
    $wordLimit = $component->getWordLimit();
    $url = $component->getUrl();
    $openUrlInNewTab = $component->shouldOpenUrlInNewTab();
    $spaEnabled = (bool) ($spa ?? false);

    if ($characterLimit && is_string($state)) {
        $state = str($state)->limit($characterLimit)->toString();
    }

    if ($wordLimit && is_string($state)) {
        $state = str($state)->words($wordLimit)->toString();
    }

    $weightClass = match ($weight) {
        'bold' => 'font-bold',
        'semibold' => 'font-semibold',
        'medium' => 'font-medium',
        default => '',
    };
@endphp

<div class="primix-text-column">
    @if($description && $descriptionPosition === 'above')
        <span class="text-xs text-surface-500">{{ $description }}</span>
    @endif

    @if($isBadge)
        @if($url)
            <a
                href="{{ $url }}"
                @if($spaEnabled && ! $openUrlInNewTab) data-livue-navigate="true" @endif
                @if($openUrlInNewTab) target="_blank" rel="noopener noreferrer" @endif
                class="inline-block"
            >
        @endif
        <p-tag
            @if($color) severity="{{ app(\Primix\Support\Colors\ColorManager::class)->toPrimeVueSeverity($color) }}" @endif
            :value="'{{ e($state ?? $component->getPlaceholder() ?? '') }}'"
        ></p-tag>
        @if($url)
            </a>
        @endif
    @else
        @if($url)
            <a
                href="{{ $url }}"
                @if($spaEnabled && ! $openUrlInNewTab) data-livue-navigate="true" @endif
                @if($openUrlInNewTab) target="_blank" rel="noopener noreferrer" @endif
                class="inline-flex items-center hover:underline"
            >
        @endif
        <span class="{{ $weightClass }} {{ $color ? app(\Primix\Support\Colors\ColorManager::class)->toTailwindClass($color, 'text', 600) : '' }}">
            {!! $state ?? '<span class="text-surface-400">' . e($component->getPlaceholder() ?? '—') . '</span>' !!}
        </span>
        @if($url)
            </a>
        @endif
    @endif

    @if($description && $descriptionPosition === 'below')
        <span class="text-xs text-surface-500">{{ $description }}</span>
    @endif
</div>

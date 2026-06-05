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

    $fullState = is_string($state) ? $state : null;
    $isTruncated = false;

    if ($characterLimit && is_string($state)) {
        $truncated = str($state)->limit($characterLimit)->toString();
        if ($truncated !== $state) {
            $state = $truncated;
            $isTruncated = true;
        }
    }

    if ($wordLimit && is_string($state)) {
        $truncated = str($state)->words($wordLimit)->toString();
        if ($truncated !== $state) {
            $state = $truncated;
            $isTruncated = true;
        }
    }

    $isExpandable = $isTruncated && ! $isBadge && ! $url;

    $weightClass = match ($weight) {
        'bold' => 'font-bold',
        'semibold' => 'font-semibold',
        'medium' => 'font-medium',
        default => '',
    };

    $colorClass = $color
        ? app(\Primix\Support\Colors\ColorManager::class)->toTailwindClass($color, 'text', 600)
        : '';
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
    @elseif($isExpandable)
        <details class="primix-text-expandable group inline-block">
            <summary class="cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden">
                <span class="{{ $weightClass }} {{ $colorClass }}">
                    <span class="group-open:hidden">{{ $state }}</span>
                    <span class="hidden group-open:inline whitespace-pre-wrap">{{ $fullState }}</span>
                </span>
                <span class="ml-1 text-xs text-primary-600 hover:underline select-none">
                    <span class="group-open:hidden">{{ __('Show more') }}</span>
                    <span class="hidden group-open:inline">{{ __('Show less') }}</span>
                </span>
            </summary>
        </details>
    @else
        @if($url)
            <a
                href="{{ $url }}"
                @if($spaEnabled && ! $openUrlInNewTab) data-livue-navigate="true" @endif
                @if($openUrlInNewTab) target="_blank" rel="noopener noreferrer" @endif
                class="inline-flex items-center hover:underline"
            >
        @endif
        <span class="{{ $weightClass }} {{ $colorClass }}">
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

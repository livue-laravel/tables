@php
    $state = $component->getState($record);
    $editable = $component->isEditable();
    $columnName = $component->getName();
    $recordKey = $record->getKey();
    $onLabel = $component->getOnLabel();
    $offLabel = $component->getOffLabel();
    $onIcon = $component->getOnIcon();
    $offIcon = $component->getOffIcon();

    $colors = app(\Primix\Support\Colors\ColorManager::class);
    $onColorRaw = $component->getOnColor();
    $offColorRaw = $component->getOffColor();

    // Toggle switch: on = vivid, off = muted only for defaults (user-specified colors stay vivid)
    $toggleOnColor = $colors->toHex($onColorRaw ?? 'success');
    $toggleOffColor = $offColorRaw
        ? $colors->toHex($offColorRaw)
        : $colors->toMutedHex('secondary');

    // Text display (non-editable): on = green, off = red (both vivid for readability)
    $textOnColor = $colors->toHex($onColorRaw ?? 'success');
    $textOffColor = $colors->toHex($offColorRaw ?? 'danger');
@endphp

<div class="primix-toggle-column">
    @if($editable)
        <label class="relative inline-flex items-center cursor-pointer" style="--toggle-on: {{ $toggleOnColor }}; --toggle-off: {{ $toggleOffColor }}">
            <input
                type="checkbox"
                {{ $state ? 'checked' : '' }}
                @change="updateTableColumnState('{{ $columnName }}', {!! \Illuminate\Support\Js::from($recordKey) !!}, $event.target.checked)"
                class="sr-only peer"
            />
            <div class="w-9 h-5 bg-[var(--toggle-off)] rounded-full peer peer-checked:bg-[var(--toggle-on)] relative after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white"></div>
        </label>
    @else
        @if($state)
            @if($onIcon)
                {!! app(\Primix\Support\Icons\IconManager::class)->render($onIcon, null, null, 'color: ' . $textOnColor) !!}
            @else
                <span style="color: {{ $textOnColor }}">
                    {{ $onLabel ?? __('Yes') }}
                </span>
            @endif
        @else
            @if($offIcon)
                {!! app(\Primix\Support\Icons\IconManager::class)->render($offIcon, null, null, 'color: ' . $textOffColor) !!}
            @else
                <span style="color: {{ $textOffColor }}">
                    {{ $offLabel ?? __('No') }}
                </span>
            @endif
        @endif
    @endif
</div>

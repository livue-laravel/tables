@php
    $state = $component->getState($record);
    $editable = $component->isEditable();
    $options = $component->getOptions();
    $columnName = $component->getName();
    $selectPlaceholder = $component->getSelectPlaceholder();
    $recordKey = $record->getKey();
    $inline = $component->isInlineInput();
    $primaryColor = $inline ? app(\Primix\Support\Colors\ColorManager::class)->toHex('primary') : null;
@endphp

<div class="primix-select-column">
    @if($editable)
        <select
            @change="updateTableColumnState('{{ $columnName }}', {!! \Illuminate\Support\Js::from($recordKey) !!}, $event.target.value)"
            @if($inline)
                style="border: none; border-bottom: 2px solid transparent; transition: border-bottom-color 0.15s ease;"
                @focus="$event.target.style.borderBottomColor = '{{ $primaryColor }}'"
                @blur="$event.target.style.borderBottomColor = 'transparent'"
            @endif
            class="{{ $inline
                ? 'w-full bg-transparent px-1 py-0.5 text-sm outline-none'
                : 'w-full rounded-md border border-surface-300 px-2 py-1.5 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none'
            }}"
        >
            @if($selectPlaceholder)
                <option value="" disabled {{ $state === null ? 'selected' : '' }}>{{ $selectPlaceholder }}</option>
            @endif
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" {{ (string) $state === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
            @endforeach
        </select>
    @else
        <span>{{ $options[$state] ?? $state ?? $component->getPlaceholder() ?? '—' }}</span>
    @endif
</div>

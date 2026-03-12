@php
    $state = $component->getState($record);
    $editable = $component->isEditable();
    $inputType = $component->getInputType();
    $step = $component->getStep();
    $recordKey = $record->getKey();
    $columnName = $component->getName();
    $inline = $component->isInlineInput();
    $primaryColor = $inline ? app(\Primix\Support\Colors\ColorManager::class)->toHex('primary') : null;
@endphp

<div class="primix-text-input-column">
    @if($editable)
        <input
            type="{{ $inputType }}"
            value="{{ e($state ?? '') }}"
            @if($step) step="{{ $step }}" @endif
            @if($component->getPlaceholder()) placeholder="{{ $component->getPlaceholder() }}" @endif
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
        />
    @else
        <span>{{ $state ?? $component->getPlaceholder() ?? '—' }}</span>
    @endif
</div>

@php
    $state = $component->getState($record);
    $editable = $component->isEditable();
    $columnName = $component->getName();
    $recordKey = $record->getKey();
    $inline = $component->isInlineInput();
@endphp

<div class="primix-checkbox-column">
    @if($editable)
        <div class="{{ $inline ? 'flex items-center justify-center' : '' }}">
            <input
                type="checkbox"
                {{ $state ? 'checked' : '' }}
                @change="updateTableColumnState('{{ $columnName }}', {!! \Illuminate\Support\Js::from($recordKey) !!}, $event.target.checked)"
                class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
            />
        </div>
    @else
        <i class="{{ $state ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500' }}"></i>
    @endif
</div>

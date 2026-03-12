@php
    $filters = $table->getFilters();
@endphp

<div class="flex flex-wrap gap-3 items-end">
    @foreach($filters as $filter)
        <div class="w-48">
            {{ $filter }}
        </div>
    @endforeach
</div>

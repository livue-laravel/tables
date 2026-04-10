@php
    $state = $component->getState($record);
    $shape = $component->getShape();
    $height = $component->getHeight() ?? '40px';
    $size = $component->getSize();
    $disk = $component->getDisk();
    $defaultImageUrl = $component->getDefaultImageUrl();
    $isStacked = $component->isStacked();
    $stackLimit = $component->getStackLimit();

    $shapeClass = match ($shape) {
        'circular' => 'rounded-full',
        'square' => 'rounded-none',
        default => 'rounded-md',
    };

    $sizeStyle = $size ? match ($size) {
        'xs' => 'width: 24px; height: 24px;',
        'sm' => 'width: 32px; height: 32px;',
        'md' => 'width: 40px; height: 40px;',
        'lg' => 'width: 48px; height: 48px;',
        'xl' => 'width: 64px; height: 64px;',
        default => "height: {$height};",
    } : "height: {$height};";

    // Resolve image URL
    $images = is_array($state) ? $state : ($state ? [$state] : []);

    if ($disk) {
        $diskInstance = \Illuminate\Support\Facades\Storage::disk($disk);
        $images = array_map(function ($img) use ($diskInstance) {
            // Use only the path portion of the URL to avoid APP_URL mismatches
            // in dev environments (e.g. APP_URL=http://localhost but server on :8000).
            $url = $diskInstance->url($img);
            $path = parse_url($url, PHP_URL_PATH);
            return $path ?? $url;
        }, $images);
    }

    if (empty($images) && $defaultImageUrl) {
        $images = [$defaultImageUrl];
    }

    if ($isStacked) {
        $images = array_slice($images, 0, $stackLimit);
    }

    $remaining = is_array($state) ? max(0, count($state) - $stackLimit) : 0;
@endphp

<div class="primix-image-column {{ $isStacked ? 'flex -space-x-2' : '' }}">
    @forelse($images as $imageUrl)
        <img
            src="{{ $imageUrl }}"
            alt=""
            class="{{ $shapeClass }} object-cover {{ $isStacked ? 'ring-2 ring-white' : '' }}"
            style="{{ $sizeStyle }}"
        />
    @empty
        @if($component->getPlaceholder())
            <span class="text-surface-400">{{ $component->getPlaceholder() }}</span>
        @endif
    @endforelse

    @if($isStacked && $remaining > 0)
        <span class="flex items-center justify-center {{ $shapeClass }} bg-surface-200 text-surface-600 text-xs font-medium ring-2 ring-white" style="{{ $sizeStyle }}">
            +{{ $remaining }}
        </span>
    @endif
</div>

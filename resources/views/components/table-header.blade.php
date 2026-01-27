@props(['title', 'subtitle', 'icon', 'actions'])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            @if(isset($icon))
                {{ $icon }}
            @endif
            {{ $title }}
        </h1>
        @if(isset($subtitle))
            <p class="text-gray-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="flex items-center gap-3">
        @if(isset($actions))
            {{ $actions }}
        @endif
    </div>
</div>

@props(['hasActiveFilters' => false])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" {{ $attributes }}>
        <!-- Ligne principale de filtres -->
        <div class="flex flex-wrap items-center gap-4">
            {{ $filters ?? $slot }}
        </div>

        <!-- Filtres actifs (chips) -->
        @if($hasActiveFilters && isset($activeFilters))
        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100 mt-4">
            <span class="text-sm text-gray-500">Filtres actifs:</span>
            {{ $activeFilters }}
        </div>
        @endif
    </form>
</div>

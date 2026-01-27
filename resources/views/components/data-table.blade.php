@props(['hasHeader' => true, 'hasFooter' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden']) }}>
    <!-- Barre d'actions groupées -->
    <div x-show="selectedCount > 0"
         x-transition
         style="display: none;"
         class="bg-blue-50 border-b border-blue-100 px-6 py-3 flex items-center justify-between">
        <span class="text-sm font-medium text-blue-700">
            <span x-text="selectedCount"></span> élément(s) sélectionné(s)
        </span>
        <div class="flex items-center gap-2">
            {{ $bulkActions ?? '' }}
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full">
            @if($hasHeader && isset($header))
            <thead class="bg-gradient-to-r from-gray-50 to-slate-100 border-b border-gray-200">
                {{ $header }}
            </thead>
            @endif
            <tbody class="divide-y divide-gray-100">
                {{ $body ?? $slot }}
            </tbody>
        </table>
    </div>

    <!-- Footer avec pagination -->
    @if($hasFooter && isset($pagination))
    <div class="bg-gray-50 border-t border-gray-100 px-6 py-4">
        {{ $pagination }}
    </div>
    @endif
</div>

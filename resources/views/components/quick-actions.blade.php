@props([
    'actions' => []
])

@php
$defaultActions = [
    [
        'id' => 'leave',
        'label' => 'Demander un congé',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
        'color' => 'purple',
        'route' => 'employee.leaves.create'
    ],
    [
        'id' => 'task',
        'label' => 'Mes tâches',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
        'color' => 'blue',
        'route' => 'employee.tasks.index'
    ],
    [
        'id' => 'payroll',
        'label' => 'Mes fiches de paie',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        'color' => 'green',
        'route' => 'employee.payrolls.index'
    ],
    [
        'id' => 'survey',
        'label' => 'Sondages',
        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        'color' => 'indigo',
        'route' => 'employee.surveys.index'
    ]
];

$actionsList = !empty($actions) ? $actions : $defaultActions;

$colorClasses = [
    'blue' => ['bg' => 'bg-[#31708E]/15', 'icon' => 'text-[#31708E]', 'hover' => 'hover:border-[#31708E]/30 hover:bg-[#31708E]/10'],
    'green' => ['bg' => 'bg-[#5085A5]/15', 'icon' => 'text-[#5085A5]', 'hover' => 'hover:border-[#5085A5]/30 hover:bg-[#5085A5]/10'],
    'yellow' => ['bg' => 'bg-[#8FC1E3]/20', 'icon' => 'text-[#5085A5]', 'hover' => 'hover:border-[#8FC1E3]/30 hover:bg-[#8FC1E3]/15'],
    'red' => ['bg' => 'bg-[#687864]/15', 'icon' => 'text-[#687864]', 'hover' => 'hover:border-[#687864]/30 hover:bg-[#687864]/10'],
    'purple' => ['bg' => 'bg-[#8FC1E3]/20', 'icon' => 'text-[#31708E]', 'hover' => 'hover:border-[#8FC1E3]/30 hover:bg-[#8FC1E3]/15'],
    'indigo' => ['bg' => 'bg-[#31708E]/10', 'icon' => 'text-[#31708E]', 'hover' => 'hover:border-[#31708E]/30 hover:bg-[#31708E]/15'],
];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-100 p-6 animate-fade-in-up']) }}>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Actions rapides</h3>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
    </div>

    {{-- Actions Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach($actionsList as $index => $action)
            @php
                $color = $action['color'] ?? 'blue';
                $colorConfig = $colorClasses[$color] ?? $colorClasses['blue'];
                $url = isset($action['route']) ? route($action['route']) : ($action['url'] ?? '#');
            @endphp

            <a href="{{ $url }}"
               class="quick-action-btn group {{ $colorConfig['hover'] }}"
               style="animation-delay: {{ $index * 50 }}ms"
               @if(isset($action['external']) && $action['external'])
               target="_blank"
               @endif>
                {{-- Icon --}}
                <div class="w-12 h-12 rounded-full {{ $colorConfig['bg'] }} flex items-center justify-center mb-2 transition-transform group-hover:scale-110">
                    <span class="{{ $colorConfig['icon'] }}">
                        {!! $action['icon'] !!}
                    </span>
                </div>

                {{-- Label --}}
                <span class="text-xs font-medium text-gray-700 text-center leading-tight">
                    {{ $action['label'] }}
                </span>

                {{-- Badge (optional) --}}
                @if(isset($action['badge']) && $action['badge'] > 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                        {{ $action['badge'] > 9 ? '9+' : $action['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Optional: Custom slot for additional actions --}}
    @if(isset($slot) && !$slot->isEmpty())
        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $slot }}
        </div>
    @endif
</div>

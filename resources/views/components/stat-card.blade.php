@props([
    'title',
    'value',
    'icon' => null,
    'color' => 'blue',
    'trend' => null,
    'trendLabel' => 'vs mois dernier',
    'gradient' => false,
    'link' => null,
    'subtitle' => null
])

@php
$colorClasses = [
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'yellow' => 'bg-yellow-500',
    'red' => 'bg-red-500',
    'purple' => 'bg-purple-500',
    'indigo' => 'bg-indigo-500',
    'amber' => 'bg-amber-500',
];

$gradientClasses = [
    'blue' => 'bg-gradient-to-br from-blue-500 to-blue-600',
    'green' => 'bg-gradient-to-br from-green-500 to-emerald-600',
    'yellow' => 'bg-gradient-to-br from-yellow-500 to-amber-600',
    'red' => 'bg-gradient-to-br from-red-500 to-rose-600',
    'purple' => 'bg-gradient-to-br from-purple-500 to-violet-600',
    'indigo' => 'bg-gradient-to-br from-indigo-500 to-blue-600',
    'amber' => 'bg-gradient-to-br from-amber-500 to-orange-600',
];

$lightBgClasses = [
    'blue' => 'bg-blue-100',
    'green' => 'bg-green-100',
    'yellow' => 'bg-yellow-100',
    'red' => 'bg-red-100',
    'purple' => 'bg-purple-100',
    'indigo' => 'bg-indigo-100',
    'amber' => 'bg-amber-100',
];

$textColorClasses = [
    'blue' => 'text-blue-600',
    'green' => 'text-green-600',
    'yellow' => 'text-yellow-600',
    'red' => 'text-red-600',
    'purple' => 'text-purple-600',
    'indigo' => 'text-indigo-600',
    'amber' => 'text-amber-600',
];

$bgColor = $colorClasses[$color] ?? 'bg-blue-500';
$gradientClass = $gradientClasses[$color] ?? 'bg-gradient-to-br from-blue-500 to-blue-600';
$lightBg = $lightBgClasses[$color] ?? 'bg-blue-100';
$textColor = $textColorClasses[$color] ?? 'text-blue-600';

// TailwindAdmin trend badge classes
$trendBadgeClass = $trend > 0 
    ? 'bg-green-50 text-green-600' 
    : ($trend < 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-600');
@endphp

@if($gradient)
    {{-- Gradient Style Card (TailwindAdmin) --}}
    <div {{ $attributes->merge(['class' => "$gradientClass rounded-2xl shadow-sm p-5 md:p-6 text-white hover:shadow-md transition-shadow"]) }}>
        @if($icon)
            <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm mb-4">
                {!! $icon !!}
            </div>
        @endif
        <div class="flex items-end justify-between">
            <div>
                <span class="text-sm text-white/80">{{ $title }}</span>
                <h4 class="mt-2 text-2xl font-bold" x-data="{ count: 0, target: {{ is_numeric($value) ? $value : 0 }} }"
                   x-init="if(target > 0) { let interval = setInterval(() => { if(count < target) { count += Math.ceil(target/20); if(count > target) count = target; } else { clearInterval(interval); } }, 50); }">
                    @if(is_numeric($value))
                        <span x-text="count">0</span>
                    @else
                        {{ $value }}
                    @endif
                </h4>
                @if($subtitle)
                    <p class="text-sm mt-1 text-white/70">{{ $subtitle }}</p>
                @endif
            </div>
            @if($trend !== null)
                <span class="flex items-center gap-1 rounded-full bg-white/20 py-0.5 pl-2 pr-2.5 text-sm font-medium">
                    @if($trend > 0)
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 12 12">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432L6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 12 12">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257L5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"/>
                        </svg>
                    @endif
                    {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
                </span>
            @endif
        </div>
        @if($link)
            <a href="{{ $link }}" class="mt-4 inline-flex items-center text-sm text-white/90 hover:text-white transition-colors">
                Voir plus →
            </a>
        @endif
    </div>
@else
    {{-- Standard Card (TailwindAdmin Style) --}}
    <div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white p-5 md:p-6 hover:shadow-md transition-shadow']) }}>
        @if($icon)
            <div class="flex items-center justify-center w-12 h-12 {{ $lightBg }} rounded-xl">
                {!! $icon !!}
            </div>
        @endif
        <div class="flex items-end justify-between mt-5">
            <div>
                <span class="text-sm text-gray-500">{{ $title }}</span>
                <h4 class="mt-2 text-2xl font-bold text-gray-800" x-data="{ count: 0, target: {{ is_numeric($value) ? $value : 0 }} }"
                   x-init="if(target > 0) { let interval = setInterval(() => { if(count < target) { count += Math.ceil(target/20); if(count > target) count = target; } else { clearInterval(interval); } }, 50); }">
                    @if(is_numeric($value))
                        <span x-text="count">0</span>
                    @else
                        {{ $value }}
                    @endif
                </h4>
                @if($subtitle)
                    <p class="text-sm mt-1 text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
            @if($trend !== null)
                <span class="flex items-center gap-1 rounded-full {{ $trendBadgeClass }} py-0.5 pl-2 pr-2.5 text-sm font-medium">
                    @if($trend > 0)
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 12 12">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432L6.12422 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z"/>
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 12 12">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.31462 10.3761C5.45194 10.5293 5.65136 10.6257 5.87329 10.6257L5.87421 10.6257C6.0663 10.6259 6.25845 10.5527 6.40505 10.4062L9.40514 7.4082C9.69814 7.11541 9.69831 6.64054 9.40552 6.34754C9.11273 6.05454 8.63785 6.05438 8.34486 6.34717L6.62329 8.06753L6.62329 1.875C6.62329 1.46079 6.28751 1.125 5.87329 1.125C5.45908 1.125 5.12329 1.46079 5.12329 1.875L5.12329 8.06422L3.40516 6.34719C3.11218 6.05439 2.6373 6.05454 2.3445 6.34752C2.0517 6.64051 2.05185 7.11538 2.34484 7.40818L5.31462 10.3761Z"/>
                        </svg>
                    @endif
                    {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
                </span>
            @endif
        </div>
        @if($link)
            <a href="{{ $link }}" class="mt-4 inline-flex items-center text-sm {{ $textColor }} hover:underline transition-colors">
                Voir plus →
            </a>
        @endif
    </div>
@endif


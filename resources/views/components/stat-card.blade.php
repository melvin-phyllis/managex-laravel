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
];

$gradientClasses = [
    'blue' => 'stat-gradient-blue',
    'green' => 'stat-gradient-green',
    'yellow' => 'stat-gradient-yellow',
    'red' => 'stat-gradient-red',
    'purple' => 'stat-gradient-purple',
    'indigo' => 'stat-gradient-indigo',
];

$lightBgClasses = [
    'blue' => 'bg-blue-50',
    'green' => 'bg-green-50',
    'yellow' => 'bg-yellow-50',
    'red' => 'bg-red-50',
    'purple' => 'bg-purple-50',
    'indigo' => 'bg-indigo-50',
];

$textColorClasses = [
    'blue' => 'text-blue-600',
    'green' => 'text-green-600',
    'yellow' => 'text-yellow-600',
    'red' => 'text-red-600',
    'purple' => 'text-purple-600',
    'indigo' => 'text-indigo-600',
];

$bgColor = $colorClasses[$color] ?? 'bg-blue-500';
$gradientClass = $gradientClasses[$color] ?? 'stat-gradient-blue';
$lightBg = $lightBgClasses[$color] ?? 'bg-blue-50';
$textColor = $textColorClasses[$color] ?? 'text-blue-600';
@endphp

@if($gradient)
    {{-- Gradient Style Card --}}
    <div {{ $attributes->merge(['class' => "$gradientClass rounded-xl shadow-sm p-6 card-hover animate-fade-in-up text-white"]) }}>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-white/80">{{ $title }}</p>
                <p class="text-3xl font-bold mt-2" x-data="{ count: 0, target: {{ is_numeric($value) ? $value : 0 }} }"
                   x-init="if(target > 0) { let interval = setInterval(() => { if(count < target) { count += Math.ceil(target/20); if(count > target) count = target; } else { clearInterval(interval); } }, 50); }">
                    @if(is_numeric($value))
                        <span x-text="count">0</span>
                    @else
                        {{ $value }}
                    @endif
                </p>
                @if($subtitle)
                    <p class="text-sm mt-1 text-white/70">{{ $subtitle }}</p>
                @endif
                @if($trend !== null)
                    <div class="flex items-center mt-2 text-sm">
                        @if($trend > 0)
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        @elseif($trend < 0)
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        @endif
                        <span class="text-white/90">{{ $trend > 0 ? '+' : '' }}{{ $trend }}% {{ $trendLabel }}</span>
                    </div>
                @endif
            </div>
            @if($icon)
                <div class="bg-white/20 p-3 rounded-full backdrop-blur-sm">
                    {!! $icon !!}
                </div>
            @endif
        </div>
        @if($link)
            <a href="{{ $link }}" class="mt-4 inline-flex items-center text-sm text-white/90 hover:text-white transition-colors">
                Voir plus
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>
@else
    {{-- Standard Card --}}
    <div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm p-6 border border-gray-100 card-hover animate-fade-in-up']) }}>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2" x-data="{ count: 0, target: {{ is_numeric($value) ? $value : 0 }} }"
                   x-init="if(target > 0) { let interval = setInterval(() => { if(count < target) { count += Math.ceil(target/20); if(count > target) count = target; } else { clearInterval(interval); } }, 50); }">
                    @if(is_numeric($value))
                        <span x-text="count">0</span>
                    @else
                        {{ $value }}
                    @endif
                </p>
                @if($subtitle)
                    <p class="text-sm mt-1 text-gray-400">{{ $subtitle }}</p>
                @endif
                @if($trend !== null)
                    <div class="flex items-center mt-2 text-sm {{ $trend > 0 ? 'text-green-600' : ($trend < 0 ? 'text-red-600' : 'text-gray-500') }}">
                        @if($trend > 0)
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                        @elseif($trend < 0)
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/>
                            </svg>
                        @endif
                        <span>{{ $trend > 0 ? '+' : '' }}{{ $trend }}% {{ $trendLabel }}</span>
                    </div>
                @endif
            </div>
            @if($icon)
                <div class="{{ $lightBg }} {{ $textColor }} p-3 rounded-full">
                    {!! $icon !!}
                </div>
            @endif
        </div>
        @if($link)
            <a href="{{ $link }}" class="mt-4 inline-flex items-center text-sm {{ $textColor }} hover:underline transition-colors">
                Voir plus
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>
@endif

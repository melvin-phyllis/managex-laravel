@props([
    'value' => 0,
    'max' => 100,
    'size' => 'md',
    'color' => 'blue',
    'label' => null,
    'sublabel' => null,
    'showPercentage' => true,
    'animated' => true
])

@php
$percentage = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;

$sizes = [
    'sm' => ['container' => 'w-16 h-16', 'stroke' => 4, 'text' => 'text-sm', 'subtext' => 'text-xs'],
    'md' => ['container' => 'w-24 h-24', 'stroke' => 6, 'text' => 'text-xl', 'subtext' => 'text-xs'],
    'lg' => ['container' => 'w-32 h-32', 'stroke' => 8, 'text' => 'text-2xl', 'subtext' => 'text-sm'],
    'xl' => ['container' => 'w-40 h-40', 'stroke' => 10, 'text' => 'text-3xl', 'subtext' => 'text-base'],
];

$colorClasses = [
    'blue' => ['stroke' => 'stroke-blue-500', 'text' => 'text-blue-600'],
    'green' => ['stroke' => 'stroke-green-500', 'text' => 'text-green-600'],
    'yellow' => ['stroke' => 'stroke-yellow-500', 'text' => 'text-yellow-600'],
    'red' => ['stroke' => 'stroke-red-500', 'text' => 'text-red-600'],
    'purple' => ['stroke' => 'stroke-purple-500', 'text' => 'text-purple-600'],
    'indigo' => ['stroke' => 'stroke-indigo-500', 'text' => 'text-indigo-600'],
];

// Auto color based on percentage - using new teal/blue palette
if ($color === 'auto') {
    if ($percentage >= 80) {
        $colorClasses['auto'] = ['stroke' => 'stroke-[#31708E]', 'text' => 'text-[#31708E]'];
    } elseif ($percentage >= 50) {
        $colorClasses['auto'] = ['stroke' => 'stroke-[#5085A5]', 'text' => 'text-[#5085A5]'];
    } elseif ($percentage >= 25) {
        $colorClasses['auto'] = ['stroke' => 'stroke-[#8FC1E3]', 'text' => 'text-[#8FC1E3]'];
    } else {
        $colorClasses['auto'] = ['stroke' => 'stroke-[#687864]', 'text' => 'text-[#687864]'];
    }
}

$sizeConfig = $sizes[$size] ?? $sizes['md'];
$colorConfig = $colorClasses[$color] ?? $colorClasses['blue'];

// SVG calculations
$radius = 45;
$circumference = 2 * pi() * $radius;
$strokeDashoffset = $circumference - ($percentage / 100) * $circumference;
@endphp

<div {{ $attributes->merge(['class' => 'circular-progress ' . $sizeConfig['container']]) }}
     @if($animated)
     x-data="{ shown: false, animatedPercentage: 0 }"
     x-init="setTimeout(() => { shown = true; animatedPercentage = {{ $percentage }}; }, 100)"
     @endif>

    <svg class="w-full h-full" viewBox="0 0 100 100">
        {{-- Background circle --}}
        <circle
            class="circular-progress-bg stroke-gray-200 fill-none"
            cx="50"
            cy="50"
            r="{{ $radius }}"
            stroke-width="{{ $sizeConfig['stroke'] }}"
        />

        {{-- Progress circle --}}
        <circle
            class="circular-progress-bar {{ $colorConfig['stroke'] }} fill-none"
            cx="50"
            cy="50"
            r="{{ $radius }}"
            stroke-width="{{ $sizeConfig['stroke'] }}"
            stroke-linecap="round"
            stroke-dasharray="{{ $circumference }}"
            @if($animated)
            :stroke-dashoffset="shown ? {{ $strokeDashoffset }} : {{ $circumference }}"
            @else
            stroke-dashoffset="{{ $strokeDashoffset }}"
            @endif
            style="transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset 1s ease-out;"
        />

        {{-- Center text --}}
        @if($showPercentage)
            <text
                x="50"
                y="50"
                class="fill-current {{ $colorConfig['text'] }} {{ $sizeConfig['text'] }} font-bold"
                text-anchor="middle"
                dominant-baseline="middle"
                @if($animated)
                x-text="Math.round(animatedPercentage) + '%'"
                @endif
            >
                @if(!$animated)
                    {{ $percentage }}%
                @endif
            </text>
        @endif
    </svg>

    {{-- Labels below the circle --}}
    @if($label || $sublabel)
        <div class="text-center mt-2">
            @if($label)
                <p class="{{ $sizeConfig['text'] }} font-semibold text-gray-900">{{ $label }}</p>
            @endif
            @if($sublabel)
                <p class="{{ $sizeConfig['subtext'] }} text-gray-500">{{ $sublabel }}</p>
            @endif
        </div>
    @endif
</div>

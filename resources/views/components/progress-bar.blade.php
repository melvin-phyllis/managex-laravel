@props(['value' => 0, 'max' => 100, 'showLabel' => true, 'size' => 'md'])

@php
$percentage = min(100, max(0, ($value / $max) * 100));
$colorClass = match(true) {
    $percentage >= 100 => 'bg-green-500',
    $percentage >= 75 => 'bg-blue-500',
    $percentage >= 50 => 'bg-yellow-500',
    $percentage >= 25 => 'bg-orange-500',
    default => 'bg-red-500',
};
$heightClass = match($size) {
    'sm' => 'h-2',
    'lg' => 'h-6',
    default => 'h-4',
};
@endphp

<div class="w-full">
    <div class="flex items-center justify-between mb-1">
        @if($showLabel)
            <span class="text-sm font-medium text-gray-700">Progression</span>
            <span class="text-sm font-medium text-gray-700">{{ number_format($percentage, 0) }}%</span>
        @endif
    </div>
    <div class="w-full bg-gray-200 rounded-full {{ $heightClass }} overflow-hidden">
        <div class="{{ $colorClass }} {{ $heightClass }} rounded-full transition-all duration-500 ease-out"
             style="width: {{ $percentage }}%"></div>
    </div>
</div>

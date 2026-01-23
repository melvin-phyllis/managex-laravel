@props([
    'type' => 'card',
    'count' => 1,
    'columns' => 1
])

@php
$gridClass = match($columns) {
    2 => 'grid-cols-1 sm:grid-cols-2',
    3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    5 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
    default => 'grid-cols-1',
};
@endphp

<div class="grid {{ $gridClass }} gap-6">
    @for($i = 0; $i < $count; $i++)
        @switch($type)
            @case('stat-card')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="h-4 bg-gray-200 rounded w-24 mb-3"></div>
                            <div class="h-8 bg-gray-200 rounded w-16"></div>
                        </div>
                        <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                    </div>
                </div>
                @break

            @case('chart')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="h-48 bg-gray-200 rounded"></div>
                </div>
                @break

            @case('activity')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-40 mb-4"></div>
                    <div class="space-y-4">
                        @for($j = 0; $j < 5; $j++)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('alert')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-3">
                        @for($j = 0; $j < 3; $j++)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                                    <div class="h-4 bg-gray-200 rounded w-40"></div>
                                </div>
                                <div class="w-16 h-8 bg-gray-200 rounded"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('calendar')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="h-5 bg-gray-200 rounded w-24"></div>
                        <div class="flex space-x-2">
                            <div class="w-8 h-8 bg-gray-200 rounded"></div>
                            <div class="w-8 h-8 bg-gray-200 rounded"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        @for($j = 0; $j < 35; $j++)
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        @endfor
                    </div>
                </div>
                @break

            @case('list')
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-5 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-3">
                        @for($j = 0; $j < 4; $j++)
                            <div class="flex items-center justify-between p-3 border-b border-gray-100">
                                <div class="flex-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                                <div class="w-20 h-6 bg-gray-200 rounded-full"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('text-line')
                <div class="h-4 bg-gray-200 rounded animate-pulse" style="width: {{ rand(60, 100) }}%"></div>
                @break

            @default
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                </div>
        @endswitch
    @endfor
</div>

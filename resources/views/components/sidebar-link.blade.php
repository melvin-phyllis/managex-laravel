@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center px-4 py-3 rounded-xl font-medium border-l-4' . ' ' . 'text-[#3B8BEB] bg-[#3B8BEB]/10 border-[#3B8BEB]'
    : 'flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-all duration-200 hover:translate-x-1';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

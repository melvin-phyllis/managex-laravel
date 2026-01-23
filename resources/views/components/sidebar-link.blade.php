@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center px-4 py-3 text-blue-600 bg-blue-50 rounded-lg font-medium'
    : 'flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition-colors duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

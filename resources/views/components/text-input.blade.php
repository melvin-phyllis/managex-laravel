@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#3D7A6A] focus:border-[#3D7A6A] transition-all duration-200 bg-white hover:border-gray-400']) !!}>

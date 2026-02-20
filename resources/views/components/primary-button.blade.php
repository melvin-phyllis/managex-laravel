<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1B3C35] to-[#2D5A4E] text-white font-semibold rounded-xl hover:from-[#2D5A4E] hover:to-[#3D7A6A] focus:outline-none focus:ring-2 focus:ring-[#3D7A6A] focus:ring-offset-2 transition-all duration-200 shadow-lg shadow-[#1B3C35]/25 transform hover:scale-[1.02] active:scale-[0.98]']) }}>
    {{ $slot }}
</button>

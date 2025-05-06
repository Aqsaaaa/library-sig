<button {{ $attributes->merge(['class' => 'w-full bg-[#f53003] hover:bg-[#d42a02] text-white font-semibold py-2 rounded-md transition-colors']) }}>
    {{ $slot }}
</button>

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-[#fc5648] border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-[#e04840] focus:outline-none focus:ring-2 focus:ring-[#fc5648] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

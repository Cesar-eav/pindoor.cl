<div class="relative flex w-full font-mono items-center justify-between bg-white border border-gray-300 text-[#fc5648] md:rounded-md px-6 py-3 md:mb-8 mb-0 sticky top-0 z-50 text-lg font-semibold min-h-[60px]">

    <div class="flex items-center gap-6">
        <a href="{{ url('labrujula') }}" class="hover:text-green-600 text-[#fc5648] transition-colors py-1 block lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a2 2 0 002 2h10a2 2 0 002-2V10" />
            </svg>
        </a>

    <form id="searchFilterNavBar" action="{{ route('atractivos.index') }}" method="GET">
        <div class="flex">
            <input type="text" id="searchFilterNavbar" name="search" value="{{ request('search') }}" 
                    placeholder="¿Qué quieres conocer?" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-[#fc5648] outline-none">
            <button type="submit" class="bg-[#fc5648] text-white px-5 py-2 rounded-r-lg hover:bg-[#d94439] transition">
                🔍
            </button>
        </div>
    </form>
    </div>


    <nav class="hidden lg:flex gap-4 items-center">
        <a href="{{ url('labrujula') }}" class="hover:text-green-600 transition-colors">Inicio</a>
        <a href="{{ url('panoramas') }}" class="hover:text-green-600 transition-colors">Panoramas</a>
        <a href="{{ route('publicita.index') }}" class="hover:text-green-600 transition-colors">Publicita</a>
        
    </nav>

    <button id="mobile-menu-button" class="lg:hidden text-[#fc5648] focus:outline-none pl-2 relative z-[60]">
        <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-white text-[#fc5648] border-t border-gray-300 border-b border-x shadow-2xl flex-col p-4 space-y-3 z-[55]">
        <div class="pb-3 border-b border-gray-100">
            <form action="{{ route('atractivos.index') }}" method="GET">
                <div class="relative flex">
                    <input type="text" name="q" placeholder="Buscar..." value="{{ request('q') }}"
                           class="flex-1 px-4 py-2 rounded-l-md bg-gray-100 border border-gray-300 text-black outline-none" required>
                    <button type="submit" class="px-3 bg-[#fc5648] text-white rounded-r-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        <a href="{{ url('/labrujula') }}" class="hover:text-green-600 transition-colors block py-1">Inicio</a>
        <a href="{{ url('panoramas') }}" class="hover:text-green-600 transition-colors block py-1">Panoramas</a>
        <a href="{{ url('#') }}" class="hover:text-green-600 transition-colors block py-1">Publicita</a>
        
    </div>
</div>
<script>


    // document.getElementById('searchBtn').addEventListener('click', function() {
    //     const params = {
    //         category: document.getElementById('categoryFilter').value,
    //         search: document.getElementById('searchFilter').value
    //     };
    //     loadAtractivos(params);
    // });


    // JavaScript puro para el menú móvil y dropdown desktop
    document.addEventListener('DOMContentLoaded', function() {
        // Menú móvil
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        if (menuButton && mobileMenu && menuIcon && closeIcon) {
            menuButton.addEventListener('click', function() {
                // Toggle del menú
                const isOpen = !mobileMenu.classList.contains('hidden');

                if (isOpen) {
                    // Cerrar menú
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    // Abrir menú
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.classList.add('flex');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });

            // Cerrar el menú al hacer clic fuera de él
            document.addEventListener('click', function(event) {
                const isClickInside = menuButton.contains(event.target) || mobileMenu.contains(event.target);

                if (!isClickInside && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            });
        }

        // Menú dropdown desktop
        const dropdownButton = document.getElementById('desktop-dropdown-button');
        const dropdownMenu = document.getElementById('desktop-dropdown-menu');
        const dropdown = document.getElementById('desktop-dropdown');

        if (dropdownButton && dropdownMenu && dropdown) {
            dropdownButton.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle('hidden');
            });

            // Cerrar el dropdown al hacer clic fuera de él
            document.addEventListener('click', function(event) {
                const isClickInside = dropdown.contains(event.target);

                if (!isClickInside && !dropdownMenu.classList.contains('hidden')) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>

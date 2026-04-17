<footer class="bg-black text-white py-10 px-6 mt-12 font-sans">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
        <!-- Título -->
        <div class="text-4xl font-extrabold tracking-wide font-serif text-center md:text-left">
            <div class="md:hidden block text-center text-white text-3xl mb-4">
                <span class="text-[#fc5648]">RE</span><span class="text-[#eba81d]">VIS</span><span
                    class="text-white">TAS</span>
            </div>

            <div class="hidden md:block">
                <span class="text-[#fc5648]">RE</span><br />
                <span class="text-[#eba81d]">VIS</span><br />
                <span class="text-white">TAS</span>
            </div>
        </div>

        <!-- Descargas -->
        <div class="flex gap-6 flex-wrap justify-center">
            <!-- Revista de Mayo -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Mayo</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_MAYO_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED1.jpg" alt="Revista Mayo"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>
            <!-- Revista de Junio -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Junio</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_JUNIO_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED2.jpeg" alt="Revista Junio"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>
            <!-- Revista de Julio -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Julio</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_JULIO_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED3.jpeg" alt="Revista Julio"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>

            <!-- Revista de Septiembre -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Septiembre</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_SEPTIEMBRE_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED5.jpeg" alt="Revista Septiembre"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>

            <!-- Revista de Octubre -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Octubre</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_OCTUBRE_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED5.jpeg" alt="Revista Octubre"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>

            <!-- Revista de Noviembre -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Noviembre</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_NOVIEMBRE_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_ED5.jpeg" alt="Revista Noviembre"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>

                        <!-- Revista de Diciembre -->
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Diciembre</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'EPDV_DICIEMBRE_2025.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Portada_12_2025.jpg" alt="Revista Diciembre"
                        class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                </a>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="text-sm mt-6 md:mt-0 font-light text-center md:text-right">
            <p class="text-gray-300">&copy; {{ date('Y') }} El Pionero de Valparaíso</p>
            <p class="text-gray-400">Todos los derechos reservados</p>
        </div>
    </div>

    <!-- Redes sociales - Solo visible en móvil -->
    <div class="md:hidden flex justify-center gap-4 mt-6 border-t border-gray-700 pt-6">
        <a href="https://www.instagram.com/elpionerodevalparaiso/" target="_blank" rel="noopener"
           class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 rounded-full shadow-lg hover:shadow-xl transition-all hover:scale-110">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
        </a>
        <a href="https://www.facebook.com/elpionerodv/" target="_blank" rel="noopener"
           class="w-12 h-12 flex items-center justify-center bg-blue-600 rounded-full shadow-lg hover:shadow-xl transition-all hover:scale-110">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>
        <a href="https://x.com/elpionerovalpo" target="_blank" rel="noopener"
           class="w-12 h-12 flex items-center justify-center bg-black rounded-full shadow-lg hover:shadow-xl transition-all hover:scale-110">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
        </a>
    </div>
</footer>

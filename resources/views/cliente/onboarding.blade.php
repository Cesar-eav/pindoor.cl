<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Activa tu perfil en Pindoor</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>· {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <p class="text-sm text-gray-500 mb-8">
                    Con el nombre, dirección y categoría tu espacio queda registrado — y visible en el mapa de Pindoor apenas agregues una foto.
                    Podrás completar el resto del perfil (carta, ofertas, galería) desde tu panel.
                </p>

                <form id="form-onboarding" method="POST" action="{{ route('cliente.crear') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Nombre + Categoría --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                        <div>
                            <x-input-label for="title" value="Nombre del espacio *" />
                            <x-text-input id="title" name="title" class="block mt-1 w-full"
                                          :value="old('title')" required autofocus />
                        </div>
                        <div>
                            <x-input-label for="categoria_id" value="Categoría *" />
                            <select id="categoria_id" name="categoria_id" required
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-[#fc5648] focus:border-[#fc5648] text-sm">
                                <option value="">Selecciona una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('categoria_id') == $cat->id)>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($requiereAprobacion)
                    {{-- WhatsApp de contacto (solo si la aprobación de admin está activa) --}}
                    <div class="mb-6">
                        <x-input-label for="contacto_whatsapp" value="WhatsApp de contacto *" />
                        <p class="text-xs text-gray-400 mb-2">El equipo de Pindoor te va a contactar acá para confirmar los datos antes de publicar tu ficha.</p>
                        <x-text-input id="contacto_whatsapp" name="contacto_whatsapp" type="text" class="block mt-1 w-full"
                                      placeholder="+56 9 1234 5678" :value="old('contacto_whatsapp')" required />
                    </div>
                    @endif

                    {{-- Mapa --}}
                    <div class="mb-6">
                        <x-input-label value="Ubicación en el mapa *" />
                        <p class="text-xs text-gray-400 mb-2">Escribe tu dirección y el pin se ubica solo. Si no queda exacto, arrástralo en el mapa.</p>
                        <div id="app">
                            <selector-mapa
                                :initial-lat="-33.0472"
                                :initial-lng="-71.6297"
                                geocode-url="{{ route('cliente.geocodificar') }}"
                                reverse-geocode-url="{{ route('cliente.geocodificar-inverso') }}"
                            ></selector-mapa>
                        </div>
                    </div>

                    {{-- Imagen principal --}}
                    <div class="mb-8">
                        <x-input-label for="imagen" value="Foto principal (opcional)" />
                        <p class="text-xs text-gray-400 mb-2">
                            La imagen de portada de tu ficha. Formato JPG o PNG, máximo 5 MB.
                            Puedes agregarla más tarde desde tu panel — pero tu espacio no será visible en Pindoor hasta que subas al menos una foto.
                        </p>
                        <input id="imagen" name="imagen" type="file" accept="image/*"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-[#fc5648] file:text-white
                                      hover:file:bg-[#e83a2c] cursor-pointer" />

                        {{-- Preview --}}
                        <div id="preview-wrap" class="hidden mt-3">
                            <img id="preview-img" src="" alt="Preview"
                                 class="w-full max-h-48 object-cover rounded-xl border border-gray-200">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('cliente.perfil') }}"
                           class="px-5 py-2.5 text-sm text-gray-500 hover:text-gray-800 transition">
                            Cancelar
                        </a>
                        <x-primary-button id="btn-publicar">
                            Publicar mi espacio →
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Overlay de carga --}}
    <div id="loading-overlay"
         class="fixed inset-0 z-50 flex-col items-center justify-center bg-gray-900/80 backdrop-blur-sm"
         style="display:none">
        <svg class="animate-spin h-16 w-16 text-white mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-white text-xl font-bold tracking-tight">Publicando tu negocio…</p>
        <p class="text-gray-300 text-sm mt-2">Esto puede tardar unos segundos</p>
    </div>

    {{-- Popup: publicar sin foto --}}
    <div id="modal-sin-foto"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4"
         style="display:none">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            
            <div class="flex items-start gap-3 mb-5">
                {{-- Imagen de ejemplo de foto principal (369×475px reales) --}}
                <img id="img-ejemplo-thumb" src="{{ asset('onboarding.png') }}" alt="Ejemplo de foto principal"
                     class="w-24 h-28 shrink-0 object-contain bg-gray-50 rounded-lg border border-gray-200 cursor-zoom-in">
                <div>
                    <h3 class="font-extrabold text-gray-900 mb-1">Visibilidad de tu espacio</h3>
                    <p class="text-sm text-gray-500">
                        Tu espacio no será visible hasta que selecciones la imagen principal para tu ficha en Pindoor.
                        No es obligatorio hacerlo ahora, puedes hacerlo más tarde desde tu panel.
                    </p>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <button type="button" id="btn-elegir-foto"
                        class="w-full py-2.5 rounded-xl text-sm font-bold text-white bg-[#fc5648] hover:bg-[#e83a2c] transition">
                    Elegir foto ahora
                </button>
                <button type="button" id="btn-publicar-sin-foto"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-gray-800 transition">
                    Elegir después →
                </button>
            </div>
        </div>
    </div>

    {{-- Lightbox: zoom de la imagen de ejemplo --}}
    <div id="lightbox-ejemplo"
         class="fixed inset-0 z-60 flex items-center justify-center bg-gray-900/90 p-6 cursor-zoom-out"
         style="display:none">
        <img src="{{ asset('onboarding.png') }}" alt="Ejemplo de foto principal (tamaño real)"
             class="max-w-full max-h-full rounded-xl">
    </div>

    <script>
    document.getElementById('imagen').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const wrap = document.getElementById('preview-wrap');
        const img  = document.getElementById('preview-img');
        img.src = URL.createObjectURL(file);
        wrap.classList.remove('hidden');
    });

    const form       = document.getElementById('form-onboarding');
    const modalFoto  = document.getElementById('modal-sin-foto');
    let confirmadoSinFoto = false;
    let modalDesdeSubmit  = false;

    function publicar() {
        const btn = document.getElementById('btn-publicar');
        btn.disabled = true;
        btn.textContent = 'Publicando…';
        document.getElementById('loading-overlay').style.display = 'flex';
        form.submit();
    }

    // Aviso informativo al entrar a la página (con un pequeño delay para que no se sienta instantáneo)
    modalDesdeSubmit = false;
    setTimeout(() => { modalFoto.style.display = 'flex'; }, 500);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const tieneFoto = document.getElementById('imagen').files.length > 0;
        if (!tieneFoto && !confirmadoSinFoto) {
            modalDesdeSubmit = true;
            modalFoto.style.display = 'flex';
            return;
        }
        publicar();
    });

    document.getElementById('btn-elegir-foto').addEventListener('click', function () {
        modalFoto.style.display = 'none';
        document.getElementById('imagen').click();
    });

    document.getElementById('btn-publicar-sin-foto').addEventListener('click', function () {
        confirmadoSinFoto = true;
        modalFoto.style.display = 'none';
        // Si el popup se disparó al intentar enviar el formulario, continúa el envío;
        // si fue el aviso inicial al cargar la página, solo lo cierra y deja seguir completando.
        if (modalDesdeSubmit) {
            publicar();
        }
    });

    const lightbox = document.getElementById('lightbox-ejemplo');
    document.getElementById('img-ejemplo-thumb').addEventListener('click', function () {
        lightbox.style.display = 'flex';
    });
    lightbox.addEventListener('click', function () {
        lightbox.style.display = 'none';
    });
    </script>
</x-app-layout>

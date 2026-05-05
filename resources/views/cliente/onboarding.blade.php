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
                    Con estos datos básicos tu negocio queda publicado en el mapa de Pindoor de inmediato.
                    Podrás completar el resto del perfil (carta, ofertas, galería) desde tu panel.
                </p>

                <form method="POST" action="{{ route('cliente.crear') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Nombre + Categoría --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                        <div>
                            <x-input-label for="title" value="Nombre del negocio *" />
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

                    {{-- Mapa --}}
                    <div class="mb-6">
                        <x-input-label value="Ubicación en el mapa *" />
                        <p class="text-xs text-gray-400 mb-2">Haz clic en el mapa o arrastra el marcador para ajustar la posición exacta de tu negocio.</p>
                        <div id="app">
                            <selector-mapa
                                :initial-lat="-33.0472"
                                :initial-lng="-71.6297"
                            ></selector-mapa>
                        </div>
                    </div>

                    {{-- Imagen principal --}}
                    <div class="mb-8">
                        <x-input-label for="imagen" value="Foto principal *" />
                        <p class="text-xs text-gray-400 mb-2">La imagen de portada de tu ficha. Formato JPG o PNG, máximo 5 MB.</p>
                        <input id="imagen" name="imagen" type="file" accept="image/*" required
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
                        <x-primary-button>
                            Publicar mi negocio →
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
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
    </script>
</x-app-layout>

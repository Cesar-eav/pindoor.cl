@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>El Pionero de Valparaíso</title>

    <script>
        // Forzar redirección si estamos en www
        if (window.location.hostname.startsWith('www.')) {
            window.location.replace(window.location.href.replace('://www.', '://'));
        }
    </script>
        
    <!-- SEO Meta Tags -->
    <meta name="description" content="El Pionero de Valparaíso - Revista digital con columnas y miradas diversas sobre la ciudad puerto" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ config('app.url') }}" />
    <meta property="og:title" content="El Pionero de Valparaíso" />
    <meta property="og:description" content="Revista digital con columnas y miradas diversas sobre la ciudad puerto" />
    <meta property="og:image" content="{{ asset('storage/Portada_Octubre.jpg') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ config('app.url') }}" />
    <meta property="twitter:title" content="El Pionero de Valparaíso" />
    <meta property="twitter:description" content="Revista digital con columnas y miradas diversas sobre la ciudad puerto" />
    <meta property="twitter:image" content="{{ asset('storage/Portada_Octubre.jpg') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "rsqwi6wyvd");
        </script>
    @endif
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full  mx-auto p-4">
        <!-- Encabezado -->
        <header class="text-center mb-1">
            <!-- Visible en pantallas medianas y grandes -->
            <img src="{{ asset('storage/portada.png') }}" class="w-full mx-auto mb-4 rounded shadow  md:block hidden" />

            <!-- Visible solo en móviles -->
            <img src="{{ asset('storage/logo_m.png') }}" class="w-full mx-auto mb-4 rounded shadow  block md:hidden" />
        </header>



        <div>

            <x-navbar />

 <!-- DESTACADO: Último número descargable -->
<section 
    id="ultimo-numero"
    x-data="{ visible: false }" 
    x-init="setTimeout(() => visible = true, 100)" 
    x-show="visible" -
    x-transition
    class="relative overflow-hidden rounded-2xl my-8 max-w-7xl mx-auto border border-gray-200 shadow-lg"
>
  <!-- Fondo en gradiente con tus colores -->
  <div class="absolute inset-0 bg-gradient-to-r from-[#fc5648] via-[#eba81d] to-[#fc5648] opacity-20"></div>

  <div class="relative grid grid-cols-1 md:grid-cols-5 gap-0 bg-white">
    <!-- Columna portada -->
    <div class="md:col-span-2 p-4 md:p-6 flex items-center justify-center">
      <div class="relative w-full max-w-sm">
        <!-- Cinta "Nuevo" -->
        <span class="absolute -left-2 -top-2 z-10 bg-[#fc5648] text-white text-xs font-bold uppercase tracking-wide px-2 py-1 rounded">
          Nuevo: Noviembre 2025
        </span>

        <!-- Portada -->
        <a href="{{ asset('storage/Ediciones/EPDV_NOVIEMBRE_2025.pdf') }}" target="_blank" rel="noopener" class="block">
          <img
            src="{{ asset('storage/Portada_Octubre.jpg') }}"
            alt="Portada El Pionero - Octubre 2025"
            class="w-full h-auto rounded-lg shadow-lg ring-1 ring-black/5 hover:scale-[1.02] transition-transform"
            loading="lazy"
          />
        </a>

        <!-- Etiquetas inferiores -->
        <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] text-gray-600">
          <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7l-4 3v-3H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
            PDF
          </span>
          <span class="inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5a2 2 0 0 0-2 2v14l4-4h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2Z"/></svg>
            Lectura rápida
          </span>
        </div>
      </div>
    </div>

    <!-- Columna texto + CTA + newsletter compacto -->
    <div class="md:col-span-3 p-6 md:p-8 flex flex-col justify-center">
      <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
        Descarga el <span class="text-[#fc5648]">Último Número</span> de <span class="italic">El Pionero de Valparaíso</span>
      </h2>

      <p class="mt-3 text-gray-700">
        “Propuestas para una reactivación económica en serio” — Columnas y miradas diversas sobre una pregunta urgente para la ciudad. 
        Llévatelo en PDF o léelo en línea.
      </p>

      <!-- Botones CTA -->
      <div class="mt-5 flex flex-col sm:flex-row gap-3">
        <a 
          href="{{ asset('storage/Ediciones/EPDV_NOVIEMBRE_2025.pdf') }}" 
          target="_blank" 
          rel="noopener" 
          download 
          class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-[#fc5648] text-white font-semibold hover:bg-[#d94439] shadow transition"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10m0 0 4-4m-4 4-4-4M4 17h16v2H4v-2Z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Descargar PDF
        </a>

        <a 
          href="{{ asset('storage/Ediciones/EPDV_NOVIEMBRE_2025.pdf') }}" 
          target="_blank" 
          rel="noopener" 
          class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-white text-gray-900 border border-gray-300 font-semibold hover:bg-gray-50 shadow-sm transition"
        >
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5h13M8 12h13M8 19h13M3 5h.01M3 12h.01M3 19h.01"/></svg>
          Ver en línea
        </a>
      </div>

      <!-- separador fino -->
      <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>

      <!-- Mini newsletter integrado -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
        <p class="sm:col-span-1 text-sm text-gray-700">
          📬 Suscríbete y recibe los próximos números y columnas en tu correo.
        </p>

        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="sm:col-span-2 flex flex-col sm:flex-row gap-2">
          @csrf
          <input 
            type="email" 
            name="email" 
            required 
            placeholder="Tu correo electrónico" 
            class="w-full sm:flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring focus:border-[#fc5648]" 
          />
          <button 
            type="submit" 
            class="px-4 py-2 rounded-lg bg-[#eba81d] text-black font-semibold hover:brightness-95 border border-amber-300 shadow-sm transition"
          >
            Suscribirse
          </button>
        </form>
      </div>

      @if (session('success'))
        <div class="mt-3 text-green-600 text-sm font-semibold">
          {{ session('success') }}
        </div>
      @endif
    </div>
  </div>
</section>



            <!-- Layout principal -->
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar izquierda -->
                <aside
                    class="w-full md:w-2/6 hidden md:block space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>
                 <!-- MAIN: Noticias dinámicas -->
                <main class="md:col-span-2 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Últimas noticias</h2>

                {{-- {{$noticias }} --}}

                    @if ($noticias->count())
                        {{-- Opcional: destacar la primera noticia --}}
                        @php
                            $destacada = $noticias->first();
                            $resto = $noticias;
                        @endphp

                        {{-- Resto en grilla --}}
                        @if($resto->count())
                            <section class="grid grid-cols-1 sm:grid-cols-1 gap-6 mt-6">
                                @foreach ($resto as $n)
                                    <article class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                        <div class="flex flex-col h-full">
                                            <div class="p-4 flex-1 flex flex-col">
                                                <div class="text-xs text-gray-500 mb-1">
                                                    {{ $n->fecha_publicacion?->format('d/m/Y') ?? '' }}
                                                </div>
                                                <h4 class="text-lg font-bold text-black mb-1 line-clamp-2">
                                                    {{ $n->titulo }}
                                                </h4>
                                                <p class="text-sm text-gray-700 text-justify mt-2 flex-1">
                                                    {{ $n->cuerpo}}
                                                </p>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </section>
                        @endif
{{-- 
                        <div class="mt-6">
                            {{ $noticias->links() }}
                        </div> --}}
                    @else
                        <p class="mt-4 text-gray-600">No hay noticias publicadas aún.</p>
                    @endif
                </main>


                        <div class="mt-2">
                            <a href="https://www.instagram.com/manos_.alarte/" target="_blank">
                                <img src="{{ asset('storage/manosalarte.jpeg') }}" alt="Anuncio Mediano"
                                    class="w-full rounded border shadow" /></a>
                        </div>
                </aside>

                <!-- Contenido principal -->

                <main class="w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">

                    @if ($columnas->isNotEmpty())
                        @php
                            $destacada = $columnas->last();
                            $resto = $columnas->take(6);
                        @endphp
                        <div>
         
                            <img src="{{ asset('storage/publicidad_movil_2.png') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow  block md:hidden" />
                        </div>
                        {{-- DESTACADA --}}
                        <section class="mt-4">
                            <div
                                class="border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                <a href="{{ url('articulo/' . $destacada->slug) }}" class="flex flex-col md:flex-row">

                                                                        {{-- Imagen a la derecha, grande --}}
                                    @if ($destacada->columnista && $destacada->columnista->foto)
                                        <div class="w-full md:w-1/3 bg-gray-100">
                                            <img src="{{ asset('storage/' . $destacada->columnista->foto) }}"
                                                alt="{{ $destacada->columnista->nombre }}"
                                                class="w-full h-64 md:h-full object-cover" />
                                        </div>
                                    @endif
                                    {{-- Texto --}}
                                    <div class="w-full md:w-2/3 p-5 flex flex-col justify-center">
                                        <div class="text-sm md:text-base text-gray-700 mb-2">
                                            {{ $destacada->revista->titulo ?? '' }}
                                        </div>
                                        <h3 class="text-2xl md:text-3xl font-bold text-black leading-tight mb-3">
                                            {{ $destacada->titulo }}
                                        </h3>
                                        @if ($destacada->autor)
                                            <p class="text-sm md:text-base italic text-gray-600">Por
                                                {{ $destacada->autor }}</p>
                                        @endif
                                        <p class="text-gray-800 text-base leading-relaxed">
                                            {!! nl2br(e(Str::limit($destacada->contenido, 300))) !!}
                                        </p>
                                    </div>

                                </a>
                            </div>
                        </section>
                        <div>
                            <img src="{{ asset('storage/cafe.png') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow  md:block hidden" />
                            <img src="{{ asset('storage/publicidad_movil_1.png') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow  block md:hidden" />
                        </div>
                        {{-- RESTO (tarjetas más pequeñas) --}}
                        @if ($resto->isNotEmpty())
                            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                @foreach ($resto as $articulo)
                                    <div
                                        class="flex border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                        <a href="{{ url('articulo/' . $articulo->slug) }}" class="flex w-full">
                                            {{-- Texto --}}
                                            <div class="w-2/3 p-4 flex flex-col justify-center">
                                                <div class="text-xs md:text-sm text-gray-700 mb-1">
                                                    {{ $articulo->revista->titulo ?? '' }}
                                                </div>
                                                 {{-- line-clamp-4 --}}
                                                <h4 class="text-lg font-bold text-black mb-1">
                                                    {{ $articulo->titulo }}
                                                </h4>
                                                @if ($articulo->columnista)
                                                    <div class="text-sm italic text-gray-600">{{ $articulo->columnista->nombre }}
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Imagen a la derecha --}}
                                            @if ($articulo->columnista && $articulo->columnista->foto)
                                                <div class="w-1/3 bg-gray-100">
                                                    <img src="{{ asset('storage/' . $articulo->columnista->foto) }}"
                                                        alt="{{ $articulo->columnista->nombre }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                @endforeach
                            </section>
                        @endif
                    @else
                        <p>No hay artículos en esta revista.</p>
                    @endif
                        <div>
                            <img src="{{ asset('storage/publicidad_desk_2.png') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow  md:block hidden" />
                            <img src="{{ asset('storage/publicidad_movil_2.png') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow  block md:hidden" />
                        </div>
                </main>
            </div>



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
                            <a href="{{ asset('storage/Ediciones/EPDV_MAYO_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED1.jpg" alt="Revista Mayo"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>
                        <!-- Revista de Junio -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Junio</p>
                            <a href="{{ asset('storage/Ediciones/EPDV_JUNIO_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED2.jpeg" alt="Revista Junio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>
                        <!-- Revista de Julio -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Julio</p>
                            <a href="{{ asset('storage/Ediciones/EPDV_JULIO_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED3.jpeg" alt="Revista Julio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>

                        <!-- Revista de Julio -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Septiembre</p>
                            <a href="{{ asset('storage/Ediciones/EPDV_SEPTIEMBRE_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED5.jpeg" alt="Revista Julio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>

                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Octubre</p>
                            <a href="{{ asset('storage/Ediciones/EPDV_OCTUBRE_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED5.jpeg" alt="Revista Julio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>

                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Noviembre</p>
                            <a href="{{ asset('storage/Ediciones/EPDV_NOVIEMBRE_2025.pdf') }}"
                                target="_blank">
                                <img src="/storage/Portada_ED5.jpeg" alt="Revista Julio"
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
            </footer>

            <!-- Pie de página -->
            <footer class="text-center text-sm text-gray-600 mt-10 pt-4 border-t border-gray-300">
            </footer>

            <div class="fixed bottom-2 right-2 text-xs text-gray-400">1</div>
        </div>
</body>

</html>

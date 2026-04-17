@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>El Pionero de Valparaíso</title>

<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>

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
    <meta property="og:image" content="{{ asset('storage/especiales/01_paseo_wWheelwright.jpg') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:url" content="{{ config('app.url') }}" />
    <meta property="twitter:title" content="El Pionero de Valparaíso" />
    <meta property="twitter:description" content="Revista digital con columnas y miradas diversas sobre la ciudad puerto" />
    <meta property="twitter:image" content="{{ asset('storage/Portada_Diciembre.jpg') }}" />

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
    <div class="w-full  mx-auto md:p-4">
        <x-header />

        <div>

            <x-navbar />

 <!-- DESTACADO: Último número descargable -->
<section
    id="ultimo-numero"
    x-data="{ visible: false }"
    x-init="setTimeout(() => visible = true, 100)"
    x-show="visible"
    x-transition
    class="relative overflow-hidden rounded-2xl my-8 max-w-7xl mx-auto border border-gray-200 shadow-lg"
>
  <!-- Fondo en gradiente con tus colores -->
  <div class="absolute inset-0 bg-gradient-to-r from-[#fc5648] via-[#eba81d] to-[#fc5648] opacity-20"></div>

  <div class="relative grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 md:p-8">
    <!-- Columna 1: Mini especial (solo portada) -->
    <div class="flex flex-col">
      <div class="relative w-full">
        <!-- Cinta "Nuevo" -->
        <span class="absolute -left-2 -top-2 z-10 bg-[#fc5648] text-white text-xs font-bold uppercase tracking-wide px-2 py-1 rounded">
          Nuevo: Especial Paseo Wheelwright
        </span>

        <!-- Portada -->
        <a href="{{ route('pdf.track', ['pdfName' => '01_paseo_wWheelwright.pdf', 'action' => 'view']) }}" target="_blank" rel="noopener" class="block">
          <img
            src="{{ asset('storage/especiales/01_paseo_wWheelwright.jpg') }}"
            alt="Portada El Pionero - Octubre 2025"
            class="w-full h-auto rounded-lg shadow-lg ring-1 ring-black/5 hover:scale-[1.02] transition-transform"
            loading="lazy"
          />
        </a>
      </div>
    </div>

    <!-- Columna 2: Contenido - Descargar hasta Newsletter + La Brújula y Juegos -->
    <div class="flex flex-col">
      <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight mb-3">
        Descarga el <span class="text-[#fc5648]">primer especial</span> de <span class="italic">El Pionero de Valparaíso</span>
      </h2>

      <p class="text-gray-700 mb-5">
        "Paseo Wheelwright, entre el potencial y el olvido." 
      </p>

      <!-- Botones CTA -->
      <div class="flex flex-col sm:flex-row gap-3 md:mb-3 mb-6" >
<a href="{{ route('pdf.track', ['pdfName' => '01_paseo_wWheelwright.pdf', 'action' => 'download']) }}"
   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-[#fc5648] text-white font-semibold hover:bg-[#d94439] shadow transition"
>
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
    </svg>
    Descargar PDF
</a>
  @push('scripts')
  <script>
  function trackAndDownloadPDF(event, url) {
    event.preventDefault();
    fetch(url, { method: 'GET', credentials: 'same-origin' })
      .then(response => {
        if (response.status === 200 || response.status === 302) {
          // Si es una descarga, forzar la descarga del PDF
          window.location.href = url;
        } else {
          alert('No se pudo descargar el PDF.');
        }
      })
      .catch(() => alert('Error al registrar la descarga.'));
  }
  </script>
  @endpush

      </div>

      <!-- Recuadro Apóyanos sobrio -->
            <x-apoyanos />


      <!-- Mini newsletter integrado -->
      <div class="space-y-3 md:space-y-1 mb-2">
        <p class="text-sm text-gray-700">
          📬 Suscríbete y recibe los próximos números y columnas en tu correo.
        </p>

        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex flex-col sm:flex-row gap-2">
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


      <!-- Nueva sección: La Brújula y Juegos -->
      <div class="grid grid-cols-2 gap-4">
        <!-- Tarjeta La Brújula -->
        <a href="{{ route('atractivos.index') }}" class="group block rounded-2xl overflow-hidden border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
          <div class="relative bg-gray-200 h-32 md:h-40 flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-50"></div>
            <div class="relative text-center">
              <div class="text-5xl mb-2">🧭</div>
            </div>
          </div>
          <div class="p-4 bg-white">
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#fc5648] transition">La Brújula</h3>
            <p class="text-gray-700 mt-1 text-sm">Encuentra todos los tesoros de Valparaíso</p>
          </div>
        </a>

        <!-- Tarjeta Juegos -->
        <a href="{{ route('juegos.index') }}" class="group block rounded-2xl overflow-hidden border border-gray-200 shadow-lg hover:shadow-xl transition-all duration-300">
          <div class="relative bg-gray-200 h-32 md:h-40 flex items-center justify-center">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-100 to-purple-50"></div>
            <div class="relative text-center">
              <div class="text-5xl mb-2">🕹️</div>
            </div>
          </div>
          <div class="p-4 bg-white">
            <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#fc5648] transition">Juegos</h3>
            <p class="text-gray-700 mt-1 text-sm">Diversión y entretenimiento</p>
          </div>
        </a>
      </div>
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
                                    <a href="{{ route('noticia.show', $n->slug) }}" class="block">
                                        <article class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                            <div class="flex flex-col h-full">
                                                @if($n->imagen)
                                                    <img src="{{ asset('storage/' . $n->imagen) }}"
                                                         alt="{{ $n->titulo }}"
                                                         class="w-full h-48 object-cover">
                                                @endif
                                                <div class="p-4 flex-1 flex flex-col">
                                                    <div class="text-xs text-gray-500 mb-1">
                                                        {{ $n->fecha_publicacion?->format('d/m/Y') ?? '' }}
                                                    </div>
                                                    <h4 class="text-lg font-bold text-black mb-1 line-clamp-2">
                                                        {{ $n->titulo }}
                                                    </h4>
                                                    <p class="text-sm text-gray-700 text-justify mt-2 flex-1">
                                                        {{ Str::limit($n->resumen ?? strip_tags($n->cuerpo), 250) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </article>
                                    </a>
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

                <main class="w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg md:p-4 p-2 shadow-sm">

                    @if ($columnas->isNotEmpty())
                        @php
                            $ordenados = $columnas->sortByDesc('created_at');
                            $destacada = $ordenados->first();
                            $resto = $ordenados->slice(1,6);
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
                                                class="w-full h-48 md:h-full object-cover" />
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
                                            {{ Str::limit(strip_tags($destacada->contenido), 300) }}
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
                                        class="flex rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                        <a href="{{ url('articulo/' . $articulo->slug) }}" class="flex w-full">
                                            {{-- Texto --}}
                                            <div class="w-4/5 p-4 flex flex-col justify-center">
                                                 <div class="text-xs md:text-sm text-gray-700 mb-1">
                                                    {{ $articulo->revista->titulo ?? '' }}
                                                </div>
                                                 {{-- line-clamp-4 --}}
                                                <h4 class="md:text-lg text-xs font-bold text-black mb-1">
                                                    {{ $articulo->titulo }}
                                                </h4>
                                                @if ($articulo->columnista)
                                                    <div class="md:text-sm text-xs italic text-gray-600">{{ $articulo->columnista->nombre }}
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Imagen a la derecha --}}
                                            @if ($articulo->columnista && $articulo->columnista->foto)
                                                <div class="w-1/5 bg-gray-100">
                                                    <img src="{{ asset('storage/' . $articulo->columnista->foto) }}"
                                                        alt="{{ $articulo->columnista->nombre }}"
                                                        class="w-32 h-full object-cover">
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



            <!-- Footer -->
            <x-footer />

            <div class="fixed bottom-2 right-2 text-xs text-gray-400">1</div>
        </div>
</body>

</html>

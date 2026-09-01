<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Reel de panoramas · Pindoor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-950 text-white overflow-hidden" style="height: 100dvh">

    <div class="relative flex flex-col h-full"
         x-data="{
             orientation: localStorage.getItem('pindoor_reel_orientation') || 'horizontal',
             speed: parseInt(localStorage.getItem('pindoor_reel_speed') ?? '35'),
             themeColor: localStorage.getItem('pindoor_reel_theme_color') || '#fc5648',
             themeColorBorde: localStorage.getItem('pindoor_reel_theme_color_borde') || '#0a0a0a',
             repetir: JSON.parse(localStorage.getItem('pindoor_reel_repetir') ?? 'true'),
             paused: false,
             panelOpen: true,
             last: null,
             get backgroundStyle() {
                 return `background: radial-gradient(circle at 50% 0%, ${this.themeColor} 0%, ${this.themeColorBorde} 65%)`;
             },
             init() {
                 this.$watch('orientation', v => localStorage.setItem('pindoor_reel_orientation', v));
                 this.$watch('speed', v => localStorage.setItem('pindoor_reel_speed', v));
                 this.$watch('themeColor', v => localStorage.setItem('pindoor_reel_theme_color', v));
                 this.$watch('themeColorBorde', v => localStorage.setItem('pindoor_reel_theme_color_borde', v));
                 this.$watch('repetir', v => localStorage.setItem('pindoor_reel_repetir', JSON.stringify(v)));
                 const step = (ts) => {
                     const el = this.$refs.scroller;
                     if (el && this.last !== null && !this.paused) {
                         // El slider va de 0 a 100, pero un valor bajo lineal (ej. speed=10 → 10px/s)
                         // es casi imperceptible. Se mapea a un rango con piso visible (40px/s en 0)
                         // hasta 400px/s en 100, así cualquier valor del slider ya se nota en pantalla.
                         const pxPorSegundo = 40 + (this.speed / 100) * 360;
                         const delta = pxPorSegundo * (ts - this.last) / 1000;
                         if (this.orientation === 'horizontal') {
                             el.scrollLeft += delta;
                             if (this.repetir && el.scrollLeft >= el.scrollWidth / 2) el.scrollLeft -= el.scrollWidth / 2;
                         } else {
                             el.scrollTop += delta;
                             if (this.repetir && el.scrollTop >= el.scrollHeight / 2) el.scrollTop -= el.scrollHeight / 2;
                         }
                     }
                     this.last = ts;
                     requestAnimationFrame(step);
                 };
                 requestAnimationFrame(step);
             },
             toggleFullscreen() {
                 if (!document.fullscreenElement) document.documentElement.requestFullscreen?.();
                 else document.exitFullscreen?.();
             }
         }"
         :style="backgroundStyle">

        {{-- Cerrar --}}
        <a href="{{ route('admin.panoramas.index') }}"
           class="absolute top-4 right-4 z-30 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 text-lg transition">
            ✕
        </a>

        {{-- Botón ajustes --}}
        <button type="button" @click="panelOpen = !panelOpen"
                class="absolute top-4 left-4 z-30 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 text-base transition">
            <span x-show="!panelOpen" x-cloak>⚙️</span>
            <span x-show="panelOpen" x-cloak>✕</span>
        </button>

        {{-- Panel de ajustes --}}
        <div x-show="panelOpen" x-transition x-cloak
             class="absolute top-16 left-4 z-30 w-64 bg-black/85 backdrop-blur border border-white/10 rounded-2xl p-4 space-y-4 text-sm">

            <div>
                <p class="text-[11px] font-black uppercase tracking-widest text-white/40 mb-2">Orientación</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="orientation = 'horizontal'"
                            :class="orientation === 'horizontal' ? 'bg-[#fc5648] text-white' : 'bg-white/10 text-white/60 hover:bg-white/15'"
                            class="rounded-xl py-2 text-xs font-bold transition">↔ Horizontal</button>
                    <button type="button" @click="orientation = 'vertical'"
                            :class="orientation === 'vertical' ? 'bg-[#fc5648] text-white' : 'bg-white/10 text-white/60 hover:bg-white/15'"
                            class="rounded-xl py-2 text-xs font-bold transition">↕ Vertical</button>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-black uppercase tracking-widest text-white/40">Velocidad</p>
                    <span class="text-xs font-bold text-[#ff8a80]" x-text="speed"></span>
                </div>
                <input type="range" min="0" max="100" step="1" x-model.number="speed"
                       class="w-full accent-[#fc5648]">
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" x-model="repetir"
                       class="w-4 h-4 rounded border-white/30 bg-white/10 text-[#fc5648] focus:ring-[#fc5648] focus:ring-offset-0">
                <span class="text-xs font-semibold text-white/70">Repetir al llegar al final</span>
            </label>

            <div x-data="{ presets: ['#fc5648', '#1a1a1a', '#8b5cf6', '#3b82f6', '#10b981', '#f97316', '#ec4899', '#f59e0b', '#06b6d4', '#4f46e5'] }">
                <p class="text-[11px] font-black uppercase tracking-widest text-white/40 mb-2">Fondo</p>
                <div class="grid grid-cols-5 gap-2 mb-3">
                    <template x-for="color in presets" :key="color">
                        <button type="button" @click="themeColor = color"
                                :style="`background: radial-gradient(circle at 30% 30%, ${color}, ${themeColorBorde})`"
                                :class="themeColor === color ? 'ring-2 ring-white' : 'ring-1 ring-white/20'"
                                class="w-8 h-8 rounded-full"></button>
                    </template>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="color" x-model="themeColor"
                               class="w-8 h-8 rounded-full border-0 bg-transparent cursor-pointer">
                        <span class="text-xs font-semibold text-white/60">Centro</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="color" x-model="themeColorBorde"
                               class="w-8 h-8 rounded-full border-0 bg-transparent cursor-pointer">
                        <span class="text-xs font-semibold text-white/60">Borde</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-2 pt-1">
                <button type="button" @click="paused = !paused"
                        class="flex-1 rounded-xl py-2 text-xs font-bold bg-white/10 hover:bg-white/15 transition">
                    <span x-text="paused ? '▶ Reanudar' : '⏸ Pausar'"></span>
                </button>
                <button type="button" @click="toggleFullscreen()"
                        class="flex-1 rounded-xl py-2 text-xs font-bold bg-white/10 hover:bg-white/15 transition">⛶ Pantalla completa</button>
            </div>
        </div>

        {{-- Encabezado --}}
        <div class="pt-8 pb-4 px-6 text-center shrink-0">
            <p class="text-sm font-bold text-white/60 tracking-wide">Pindoor</p>
            <h1 class="text-2xl font-extrabold tracking-tight mt-1">Próximos panoramas</h1>
        </div>

        {{-- Carrusel --}}
        <div class="flex-1 min-h-0 flex">
            @if($panoramas->isEmpty())
            <div class="w-full flex items-center justify-center text-center px-8">
                <p class="text-white/40 text-sm">No hay panoramas próximos programados todavía.</p>
            </div>
            @else
            <div x-ref="scroller"
                 class="w-full [&::-webkit-scrollbar]:hidden"
                 :class="orientation === 'horizontal' ? 'overflow-x-auto overflow-y-hidden' : 'overflow-y-auto overflow-x-hidden'"
                 style="scrollbar-width:none"
                 @click="paused = !paused">
                <div :class="orientation === 'horizontal' ? 'flex gap-4 px-4 h-full items-center' : 'flex flex-col gap-4 py-4 items-center object-cover'"
                     :style="orientation === 'horizontal' ? 'width: max-content' : 'height: max-content'">
                    @for($vuelta = 0; $vuelta < 2; $vuelta++)
                        @foreach($panoramas as $panorama)
                        @php $catInfo = \App\Models\CategoriaEvento::catalogo()[$panorama->categoria] ?? ['emoji' => '📌', 'label' => 'Panorama']; @endphp
                        {{-- La 2ª vuelta solo existe para el loop sin cortes; si "repetir" está
                             desactivado se oculta y el scroll se detiene tras el único pase real. --}}
                        <div x-show="{{ $vuelta === 0 ? 'true' : 'repetir' }}"
                             :class="orientation === 'horizontal' ? 'w-56 h-80' : 'aspect-4/5 h-[60dvh] max-h-130 w-auto'"
                             class="relative shrink-0 rounded-3xl overflow-hidden shadow-2xl border border-white/10">
                            @if($panorama->imagen)
                                <img src="{{ asset('storage/' . $panorama->imagen) }}" alt="{{ $panorama->titulo }}"
                                     class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-[#fc5648]/40 to-gray-900 flex items-center justify-center text-6xl">
                                    {{ $catInfo['emoji'] }}
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-black/10"></div>

                            @if($panorama->es_gratuito)
                            <span class="absolute top-3 left-3 text-[10px] font-black uppercase bg-[#fc5648] text-white px-2.5 py-1 rounded-full">🎟️ Gratis</span>
                            @endif

                            <div class="absolute inset-x-0 bottom-0 p-4">
                                <span class="text-[10px] font-black uppercase bg-white/15 px-2 py-0.5 rounded-full">{{ $catInfo['emoji'] }} {{ $catInfo['label'] }}</span>
                                <h3 class="text-lg font-extrabold leading-snug mt-2 line-clamp-3">{{ $panorama->titulo }}</h3>
                                <p class="text-sm font-bold text-[#ff8a80] mt-1.5">
                                    {{ $panorama->fecha->locale('es')->isoFormat('dddd D MMM') }}
                                    @if($panorama->hora) · {{ $panorama->hora }}@endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    @endfor
                </div>
            </div>
            @endif
        </div>

        {{-- Marca --}}
        <div class="pb-6 pt-2 text-center shrink-0">
            <p class="text-[11px] font-bold red-white/30 tracking-widest uppercase">pindoor.cl</p>
        </div>

    </div>

    @livewireScripts
</body>
</html>

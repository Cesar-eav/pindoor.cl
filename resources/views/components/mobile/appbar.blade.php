{{-- App Bar mobile —————————————————————————————————————————— --}}
<header x-data="{
            buscando: false,
            q: @js(request('search', '')),
            sugerencias: [],
            cargando: false,
            async buscar() {
                if (this.q.trim().length < 2) { this.sugerencias = []; return; }
                this.cargando = true;
                try {
                    const res = await fetch('{{ route('puntos.sugerencias') }}?q=' + encodeURIComponent(this.q));
                    this.sugerencias = await res.json();
                } catch (e) {
                    this.sugerencias = [];
                }
                this.cargando = false;
            },
        }"
        class="md:hidden sticky top-0 z-40 bg-white border-b border-gray-100 px-4 pb-3 flex items-center justify-between shadow-sm" style="padding-top: calc(12px + var(--inset-top, 0px))">

    <div class="flex items-center gap-1 min-w-0" x-show="!buscando">
        <a href="{{ route('puntos.index') }}" class="text-lg font-bold tracking-tight shrink-0">
            <span class="text-[#fc5648]">Pin</span>door
        </a>
        <button type="button"
                @click="buscando = true; $nextTick(() => $refs.buscarInput.focus())"
                class="p-1.5 text-gray-400 hover:text-[#fc5648] transition shrink-0" title="{{ __('ui.nav.buscar') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
    </div>

    <nav class="flex items-center gap-1" x-show="!buscando">
        <a href="{{ route('puntos.explorar') }}"
           class="px-3 py-1.5 text-xs font-bold transition-colors
                  {{ request()->routeIs('puntos.explorar') || request()->routeIs('puntos.buscar') ? 'bg-[#fc5648] text-white' : 'text-gray-500 hover:text-gray-800' }}">
            {{ __('ui.nav.explorar') }}
        </a>
        <a href="{{ route('atractivos.panoramas') }}"
           class="px-3 py-1.5 text-xs font-bold transition-colors
                  {{ request()->routeIs('atractivos.panoramas') ? 'bg-[#fc5648] text-white' : 'text-gray-500 hover:text-gray-800' }}">
            {{ __('ui.nav.panoramas') }}
        </a>
        <div class="flex items-center gap-0.5 ml-1 bg-gray-100 rounded-full px-2 py-1">
            <a href="{{ route('lang.switch', 'es') }}"
               class="text-[10px] font-black transition px-1 py-0.5 rounded-full {{ app()->getLocale() === 'es' ? 'bg-[#fabeb9] text-[#fc5648] shadow-sm' : 'text-gray-400' }}">🇪🇸</a>
            <span class="text-gray-300 text-[10px]">|</span>
            <a href="{{ route('lang.switch', 'en') }}"
               class="text-[10px] font-black transition px-1 py-0.5 rounded-full {{ app()->getLocale() === 'en' ? 'bg-[#fabeb9] text-[#fc5648] shadow-sm' : 'text-gray-400' }}">🇬🇧</a>
            <span class="text-gray-300 text-[10px]">|</span>
            <a href="{{ route('lang.switch', 'fr') }}"
               class="text-[10px] font-black transition px-1 py-0.5 rounded-full {{ app()->getLocale() === 'fr' ? 'bg-[#fabeb9] text-[#fc5648] shadow-sm' : 'text-gray-400' }}">🇫🇷</a>
        </div>
    </nav>

    {{-- Búsqueda: se despliega hacia el lado en lugar del logo/menú --}}
    <form x-show="buscando" x-cloak
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-x-3"
          x-transition:enter-end="opacity-100 translate-x-0"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 translate-x-0"
          x-transition:leave-end="opacity-0 -translate-x-3"
          @click.outside="buscando = false"
          action="{{ route('puntos.index') }}" method="GET"
          class="flex-1 flex items-center gap-2">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" x-ref="buscarInput" x-model="q"
                   @input.debounce.300ms="buscar()"
                   autocomplete="off"
                   placeholder="{{ __('ui.home.buscar_placeholder') }}"
                   class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">

            {{-- Sugerencias instantáneas --}}
            <div x-show="sugerencias.length > 0" x-cloak
                 class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-lg border border-gray-100 max-h-72 overflow-y-auto z-50">
                <template x-for="s in sugerencias" :key="s.url">
                    <a :href="s.url" @click="buscando = false"
                       class="flex items-start gap-2 px-4 py-2.5 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition">
                        <span class="shrink-0 mt-0.5" x-text="s.tipo === 'panorama' ? '🗓' : '📍'"></span>
                        <span class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate" x-text="s.title"></p>
                            <p class="text-xs text-gray-400 truncate" x-text="s.subtitle"></p>
                        </span>
                    </a>
                </template>
            </div>
        </div>
        <button type="button" @click="buscando = false"
                class="p-1.5 text-gray-400 hover:text-gray-700 transition shrink-0" title="Cerrar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </form>
</header>

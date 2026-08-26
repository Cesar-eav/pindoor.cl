{{-- Negocios destacados --}}
@if(isset($negociosDestacados) && $negociosDestacados->isNotEmpty() && !request()->filled('lat') && !request()->filled('category') && !request()->filled('search'))
<div id="destacados-mobile-section" class="px-3 pt-3">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <span class="text-md font-bold text-gray-900 tracking-tight">Destacados</span>
        <span class="flex-1 h-px bg-gray-200"></span>
    </div>
    <div class="overflow-x-auto pb-1 scrollbar-hide" style="scrollbar-width:none">
        <div class="flex gap-2">
        @foreach($negociosDestacados as $negocio)
        <div class="flex-none w-36">
            @php($atractivo = $negocio)
            @include('puntos.partials._card_mobile')
        </div>
        @endforeach
        </div>
    </div>
</div>
@endif

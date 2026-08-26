{{-- Entradas del blog --}}
@if(isset($ultimosPosts) && $ultimosPosts->isNotEmpty() && !request()->filled('category') && !request()->filled('search') && !request()->filled('lat'))
<div id="blog-mobile-section" class="mt-5">
    <div class="flex items-center gap-2 mb-3 px-3">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <h2 class="text-sm font-bold text-gray-900 tracking-tight">{{ __('ui.home.blog_titulo') }}</h2>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('blog.index') }}" class="text-[11px] font-semibold text-[#fc5648] shrink-0">{{ __('ui.home.ver_todos') }}</a>
    </div>

    <div class="flex gap-3 overflow-x-auto pb-2 px-3" style="-ms-overflow-style:none;scrollbar-width:none;">
        @foreach($ultimosPosts as $post)
        <a href="{{ route('blog.show', $post->slug) }}"
           class="relative shrink-0 rounded-2xl overflow-hidden shadow-sm"
           style="width:72vw;height:11rem;">
            @if($post->imagen_portada)
                <img src="{{ asset('storage/' . $post->imagen_portada) }}"
                     alt="{{ $post->titulo }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gray-800"></div>
            @endif
            <div class="absolute inset-0 bg-linear-to-t from-black/95 via-black/20 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <h3 class="text-sm font-bold text-white leading-snug line-clamp-3">{{ $post->titulo }}</h3>

            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

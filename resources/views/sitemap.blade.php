<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ route('puntos.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ route('atractivos.panoramas') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>{{ route('publicita.index') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    @foreach($categorias as $categoria)
    <url>
        <loc>{{ route('puntos.index', ['category' => $categoria->slug]) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach

    @foreach($puntos as $punto)
    <url>
        <loc>{{ route('puntos.show', $punto->slug) }}</loc>
        <lastmod>{{ $punto->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post->slug) }}</loc>
        @if($post->publicado_en)
        <lastmod>{{ $post->publicado_en->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    @foreach($rutas as $ruta)
    <url>
        <loc>{{ route('rutas.show', $ruta->slug) }}</loc>
        @if($ruta->publicado_en)
        <lastmod>{{ $ruta->publicado_en->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    @foreach($recomendaciones as $recomendacion)
    <url>
        <loc>{{ route('recomienda.show', $recomendacion->slug) }}</loc>
        @if($recomendacion->publicado_en)
        <lastmod>{{ $recomendacion->publicado_en->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>

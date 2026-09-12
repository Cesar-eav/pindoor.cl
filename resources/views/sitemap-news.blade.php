<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

    @foreach($posts as $post)
    <url>
        <loc>{{ route('blog.show', $post->slug) }}</loc>
        <news:news>
            <news:publication>
                <news:name>Pindoor</news:name>
                <news:language>es</news:language>
            </news:publication>
            <news:publication_date>{{ $post->publicado_en->toAtomString() }}</news:publication_date>
            <news:title>{{ $post->titulo }}</news:title>
        </news:news>
    </url>
    @endforeach

</urlset>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $page)
    <url>
        <loc>{{ rtrim(config('app.url'), '/').($page['path'] === '/' ? '' : $page['path']) }}</loc>
        <changefreq>{{ $page['frequency'] }}</changefreq>
        <priority>{{ $page['priority'] }}</priority>
    </url>
@endforeach
</urlset>

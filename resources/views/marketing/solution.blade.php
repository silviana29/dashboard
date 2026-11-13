@extends('marketing.layout')

@section('seo')
@include('marketing.partials.seo', [
    'title' => $page['seoTitle'],
    'description' => $page['description'],
    'path' => '/'.$page['slug'],
    'structuredData' => [[
        '@context' => 'https://schema.org', '@type' => 'WebPage',
        'name' => $page['title'], 'description' => $page['description'],
        'url' => rtrim(config('app.url'), '/').'/'.$page['slug'],
        'isPartOf' => ['@type' => 'WebSite', 'name' => 'MWork', 'url' => rtrim(config('app.url'), '/')],
    ], [
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'MWork', 'item' => rtrim(config('app.url'), '/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $page['title'], 'item' => rtrim(config('app.url'), '/').'/'.$page['slug']],
        ],
    ]],
])
@endsection

@section('content')
<main>
    <section class="hero"><div class="container" style="max-width:920px;text-align:center"><span class="eyebrow">{{ $page['eyebrow'] }}</span><h1 style="margin-inline:auto">{{ $page['title'] }}</h1><p class="lead" style="margin-inline:auto">{{ $page['intro'] }}</p><div class="hero-actions" style="justify-content:center"><a class="button primary" href="{{ route('organizations.create') }}">Buat workspace gratis</a><a class="button" href="{{ route('home') }}#fitur">Lihat semua fitur</a></div></div></section>
    <section class="section"><div class="container"><div class="section-head"><span class="eyebrow">Manfaat utama</span><h2>Dibuat agar pengelolaan kehadiran lebih sederhana.</h2><p>{{ $page['description'] }}</p></div><div class="grid-3">@foreach($page['benefits'] as $index => $benefit)<article class="feature"><span class="feature-number">0{{ $index + 1 }}</span><h3>{{ $benefit['title'] }}</h3><p>{{ $benefit['text'] }}</p></article>@endforeach</div></div></section>
    <section class="section soft"><div class="container"><div class="section-head"><span class="eyebrow">Bagian dari MWork</span><h2>Tetap terhubung dengan pengelolaan tim.</h2><p>Selain kebutuhan khusus di halaman ini, MWork menyediakan tim, atasan, operator, izin, laporan, audit aktivitas, invoice, dan pengelolaan paket dalam satu sistem.</p></div><div class="use-links"><a class="use-link" href="{{ route('seo.free-attendance') }}"><span>PAKET AWAL</span><h3>Absensi gratis</h3><p>Mulai menggunakan workspace tanpa biaya di awal.</p></a><a class="use-link" href="{{ route('seo.location-attendance') }}"><span>LOKASI GPS</span><h3>Absensi berbasis lokasi</h3><p>Rekam posisi dan radius area kehadiran.</p></a><a class="use-link" href="{{ route('seo.organization-attendance') }}"><span>BERBAGAI ORGANISASI</span><h3>Solusi multi-workspace</h3><p>Untuk perusahaan, UMKM, kantor, dan sekolah.</p></a></div></div></section>
    <section class="section"><div class="container"><div class="section-head"><span class="eyebrow">Pertanyaan umum</span><h2>Informasi yang sering ditanyakan.</h2></div><div class="faq">@foreach($page['faq'] as $item)<details><summary>{{ $item['question'] }}</summary><p>{{ $item['answer'] }}</p></details>@endforeach</div></div></section>
    <section class="cta"><div class="container"><div class="cta-box"><div><h2>Kelola absensi bersama MWork.</h2><p>Mulai gratis, undang anggota, dan sesuaikan aturan untuk organisasimu.</p></div><a class="button primary" href="{{ route('organizations.create') }}">Mulai sekarang</a></div></div></section>
</main>
@endsection

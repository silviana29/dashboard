@php
    $seoBaseUrl = rtrim(config('app.url'), '/');
    $seoCanonical = $seoBaseUrl.($path === '/' ? '' : '/'.ltrim($path, '/'));
    $seoImage = $seoBaseUrl.'/mwork.png';
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<link rel="canonical" href="{{ $seoCanonical }}">
<link rel="alternate" hreflang="id-ID" href="{{ $seoCanonical }}">
<link rel="alternate" hreflang="x-default" href="{{ $seoCanonical }}">
@if(config('seo.google_site_verification'))<meta name="google-site-verification" content="{{ config('seo.google_site_verification') }}">@endif
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="website">
<meta property="og:site_name" content="MWork">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:width" content="2000">
<meta property="og:image:height" content="2000">
<meta property="og:image:alt" content="Logo MWork by Masimasa">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $seoImage }}">
@foreach($structuredData as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach

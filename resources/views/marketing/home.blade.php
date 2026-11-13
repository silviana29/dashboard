@extends('marketing.layout')

@section('seo')
@include('marketing.partials.seo', [
    'title' => 'MWork by Masimasa — Sistem Absensi Online Berbasis Lokasi',
    'description' => 'MWork adalah sistem absensi online gratis berbasis lokasi GPS untuk perusahaan, UMKM, kantor, sekolah, instansi, dan komunitas.',
    'path' => '/',
    'structuredData' => [[
        '@context' => 'https://schema.org', '@type' => 'WebSite',
        'name' => 'MWork', 'alternateName' => ['MWork by Masimasa', 'Masimasa MWork'],
        'url' => rtrim(config('app.url'), '/'),
    ], [
        '@context' => 'https://schema.org', '@type' => 'Organization',
        'name' => 'Masimasa', 'url' => rtrim(config('app.url'), '/'),
        'logo' => rtrim(config('app.url'), '/').'/mwork.png',
        'brand' => ['@type' => 'Brand', 'name' => 'MWork'],
    ], [
        '@context' => 'https://schema.org', '@type' => 'SoftwareApplication',
        'name' => 'MWork', 'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'Web, Android, iOS, Windows, macOS',
        'description' => 'Sistem absensi online berbasis lokasi GPS untuk perusahaan, UMKM, kantor, sekolah, instansi, dan komunitas.',
        'url' => rtrim(config('app.url'), '/'),
        'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
    ]],
])
@endsection

@section('content')
<main>
    <section class="hero"><div class="container hero-grid"><div><span class="eyebrow">Sistem absensi Indonesia</span><h1>Absensi online berbasis lokasi untuk tim yang lebih tertata.</h1><p class="lead">MWork by Masimasa menyatukan absensi masuk dan pulang, lokasi GPS, izin, laporan harian, tim, atasan, serta audit aktivitas dalam satu workspace.</p><div class="hero-actions"><a class="button primary" href="{{ route('organizations.create') }}">Mulai gratis</a><a class="button" href="{{ route('login') }}">Masuk ke MWork</a></div><p class="hero-note">Cocok untuk perusahaan, UMKM, kantor, sekolah, instansi, dan komunitas.</p></div><div class="product-card" aria-label="Ringkasan dashboard MWork"><div class="product-top"><div><small style="color:#7b8798">WORKSPACE</small><br><strong>Tim Masimasa</strong></div><span class="status">Sistem aktif</span></div><div class="summary"><div><span>Hadir</span><strong>24</strong></div><div><span>Izin</span><strong>2</strong></div><div><span>Belum masuk</span><strong>3</strong></div></div><div class="activity"><div><span><i class="dot" style="display:inline-block;margin-right:8px"></i>Anggota masuk</span><strong>08:02</strong></div><div><span><i class="dot" style="display:inline-block;margin-right:8px;background:#6172f3"></i>Laporan WFH diterima</span><strong>09:10</strong></div><div><span><i class="dot" style="display:inline-block;margin-right:8px;background:#f79009"></i>Pengajuan izin</span><strong>Hari ini</strong></div></div></div></div></section>
    <div class="proof"><div class="container proof-inner"><span>Lokasi GPS tercatat</span><span>Aturan WFO & WFH</span><span>Multi-workspace</span><span>Bisa dipasang sebagai aplikasi</span></div></div>
    <section class="section" id="fitur"><div class="container"><div class="section-head"><span class="eyebrow">Fitur utama</span><h2>Satu sistem untuk seluruh alur kehadiran.</h2><p>Admin dapat menyesuaikan aturan tanpa mencampur data antarperusahaan. Anggota mendapatkan halaman sederhana untuk menjalankan absensi harian.</p></div><div class="grid-3">
        @foreach([['01','Absensi berdasarkan lokasi','Setiap absensi menyimpan koordinat GPS. Admin juga dapat menentukan titik dan radius lokasi kerja.'],['02','Terjadwal, fleksibel, dan laporan','Gunakan aturan masuk-pulang terjadwal, jam fleksibel, WFO, WFH, atau cukup laporan harian.'],['03','Tim dan atasan','Kelompokkan anggota ke dalam tim dan berikan akses kepada atasan untuk memantau kehadiran timnya.'],['04','Izin di tengah hari','Anggota dapat mengajukan izin beserta waktu mulai, keterangan, lokasi, dan berkas pendukung.'],['05','Audit aktivitas','Perubahan akun, lokasi, aturan, absensi, paket, dan pembayaran tercatat di audit organisasi.'],['06','Aplikasi web praktis','MWork dapat digunakan dari browser serta dipasang sebagai PWA pada perangkat yang mendukung.']] as [$number,$heading,$text])
        <article class="feature"><span class="feature-number">{{ $number }}</span><h3>{{ $heading }}</h3><p>{{ $text }}</p></article>
        @endforeach
    </div></div></section>
    <section class="section soft" id="cara-kerja"><div class="container"><div class="section-head"><span class="eyebrow">Cara kerja</span><h2>Mulai absensi tim dalam tiga langkah.</h2></div><div class="steps"><article class="step"><b>LANGKAH 1</b><h3>Buat workspace</h3><p>Daftarkan organisasi dan dapatkan kode perusahaan acak untuk proses pendaftaran anggota.</p></article><article class="step"><b>LANGKAH 2</b><h3>Atur tim dan kebijakan</h3><p>Buat lokasi, tim, atasan, serta aturan absensi terjadwal, fleksibel, atau laporan harian.</p></article><article class="step"><b>LANGKAH 3</b><h3>Undang dan pantau anggota</h3><p>Anggota mendaftar memakai kode perusahaan, lalu admin menyetujui akun sebelum digunakan.</p></article></div></div></section>
    <section class="section" id="solusi"><div class="container"><div class="section-head"><span class="eyebrow">Temukan solusi</span><h2>Pelajari MWork sesuai kebutuhanmu.</h2><p>Setiap halaman berikut menjelaskan fungsi MWork secara lebih spesifik.</p></div><div class="use-links"><a class="use-link" href="{{ route('seo.free-attendance') }}"><span>MULAI GRATIS</span><h3>Sistem absensi gratis</h3><p>Untuk UMKM dan tim yang ingin memulai pencatatan kehadiran tanpa proses rumit.</p></a><a class="use-link" href="{{ route('seo.location-attendance') }}"><span>GPS & GEOFENCE</span><h3>Absensi berbasis lokasi</h3><p>Catat koordinat dan periksa radius lokasi pada setiap proses absensi.</p></a><a class="use-link" href="{{ route('seo.organization-attendance') }}"><span>MULTI-WORKSPACE</span><h3>Perusahaan, UMKM, dan sekolah</h3><p>Kelola organisasi dengan anggota, aturan, dan data yang terpisah.</p></a></div></div></section>
    <section class="section soft" id="faq"><div class="container"><div class="section-head"><span class="eyebrow">Pertanyaan umum</span><h2>Tentang sistem absensi MWork.</h2></div><div class="faq"><details><summary>Apa itu MWork by Masimasa?</summary><p>MWork adalah sistem absensi online berbasis lokasi yang dibuat oleh Masimasa untuk membantu berbagai jenis organisasi mengelola kehadiran, izin, laporan harian, tim, dan pemantauan atasan.</p></details><details><summary>Apakah MWork mempunyai paket gratis?</summary><p>Ya. Organisasi dapat memulai dengan paket gratis dan memilih paket berbayar apabila membutuhkan kapasitas atau fitur tambahan.</p></details><details><summary>Apakah lokasi anggota tercatat saat absensi?</summary><p>Ya. Semua jenis absensi dirancang untuk mencatat posisi GPS aktual saat data dikirim.</p></details><details><summary>Apakah data setiap perusahaan terpisah?</summary><p>Ya. Pengguna, lokasi, tim, aturan, pembayaran, dan audit aktivitas dikelola berdasarkan workspace masing-masing.</p></details></div></div></section>
    <section class="cta"><div class="container"><div class="cta-box"><div><h2>Siap mencoba MWork?</h2><p>Buat workspace organisasi dan mulai mengatur absensi timmu.</p></div><a class="button primary" href="{{ route('organizations.create') }}">Buat workspace gratis</a></div></div></section>
</main>
@endsection

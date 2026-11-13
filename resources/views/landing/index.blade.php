<!DOCTYPE html>
<html lang="en" id="htmlRoot">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MWork</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="{{ asset('landingcss/bootstrap.min.css') }}">

    <!-- AOS Animate on Scroll -->
    <link rel="stylesheet" href="{{ asset('landingcss/aos.css') }}">

    <!-- Swiper -->
    <link rel="stylesheet" href="{{ asset('landingcss/swiper-bundle.min.css') }}">

    <!-- Font Awesome (all.min.css) -->
    <link rel="stylesheet" href="{{ asset('landingcss/all.min.css') }}">

    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('landingcss/magnific-popup.css') }}">

    <!-- Style CSS (global) -->
    <link rel="stylesheet" href="{{ asset('landingcss/style.css') }}">
</head>


<body>
    <!-- header -->
    @include('landing.header')

    <!-- ======================== LANDING PAGE ======================== -->
    <main>
        <!-- HERO -->
        <section id="hero">
            <div class="aur aur-a" style="top:-80px;left:-120px"></div>
            <div class="aur aur-b" style="top:180px;right:-180px"></div>
            <div class="aur aur-a" style="bottom:-80px;left:45%;transform:translateX(-50%);opacity:.4"></div>
            <div class="container position-relative" style="z-index:2">
                <div class="text-center">
                    <div class="afu" style="animation-delay:.05s"><span class="hbadge"><span
                                class="bdot"></span>Platform
                            Administrasi Terintegrasi</span></div>
                    <h1 class="h1 afu" style="animation-delay:.12s">Pusat Administrasi MWork<br><span
                            class="gt">dalam Satu
                            Platform</span></h1>
                    <p class="mx-auto afu"
                        style="max-width:580px;font-size:clamp(.95rem,1.8vw,1.2rem);color:var(--tx2);margin-bottom:36px;animation-delay:.2s">
                        Kelola workspace perusahaan, pengguna, absensi, langganan, layanan pelanggan, dan keamanan
                        sistem
                        secara terpusat dengan MWork.</p>
                </div>
            </div>
        </section>

        <!-- Fitur -->
        <section id="fitur" class="sp" style="background:var(--bg2)">
            <div class="container">
                <div class="text-center mb-5 rv">
                    <span class="slbl">Fitur Utama</span>
                    <p class="ssub mx-auto">
                        Kelola seluruh kebutuhan administrasi MWork dalam satu platform yang terintegrasi.
                    </p>
                </div>

                <div class="row g-3 justify-content-center">

                    <!-- Manajemen Perusahaan -->
                    <div class="col-md-4 rv">
                        <div class="gc p-4 h-100">
                            <div class="ftico">
                                <i class="fa-solid fa-building"></i>
                            </div>

                            <h3 class="fs-5 fw-semibold mb-2">
                                Manajemen Perusahaan
                            </h3>

                            <p style="font-size:.875rem;color:var(--tx2)">
                                Kelola workspace perusahaan, status akses, dan langganan dari satu tempat. Pantau
                                informasi
                                pembayaran serta atur akses workspace dengan mudah.
                            </p>
                        </div>
                    </div>

                    <!-- Manajemen Pengguna & Absensi -->
                    <div class="col-md-4 rv" style="transition-delay:.05s">
                        <div class="gc p-4 h-100">
                            <div class="ftico">
                                <i class="fa-solid fa-user-check"></i>
                            </div>

                            <h3 class="fs-5 fw-semibold mb-2">
                                Manajemen Pengguna & Absensi
                            </h3>

                            <p style="font-size:.875rem;color:var(--tx2)">
                                Kelola data pengguna, perusahaan, peran, dan pantau riwayat absensi terbaru dalam satu
                                tempat.
                            </p>
                        </div>
                    </div>

                    <!-- Paket & Pembayaran -->
                    <div class="col-md-4 rv" style="transition-delay:.1s">
                        <div class="gc p-4 h-100">
                            <div class="ftico">
                                <i class="fa-solid fa-wallet"></i>
                            </div>

                            <h3 class="fs-5 fw-semibold mb-2">
                                Paket & Pembayaran
                            </h3>

                            <p style="font-size:.875rem;color:var(--tx2)">
                                Kelola transaksi keuangan dan paket langganan perusahaan, termasuk membuat, mengubah,
                                dan
                                mengatur paket di MWork.
                            </p>
                        </div>
                    </div>

                    <!-- Layanan Pelanggan -->
                    <div class="col-md-4 rv" style="transition-delay:.15s">
                        <div class="gc p-4 h-100">
                            <div class="ftico">
                                <i class="fa-solid fa-headset"></i>
                            </div>

                            <h3 class="fs-5 fw-semibold mb-2">
                                Layanan Pelanggan
                            </h3>

                            <p style="font-size:.875rem;color:var(--tx2)">
                                Kelola percakapan bantuan, pantau informasi workspace, dan berikan penawaran paket
                                khusus kepada
                                perusahaan secara terpusat.
                            </p>
                        </div>
                    </div>

                    <!-- Audit Aktivitas & Keamanan -->
                    <div class="col-md-4 rv" style="transition-delay:.2s">
                        <div class="gc p-4 h-100">
                            <div class="ftico">
                                <i class="fa-solid fa-shield"></i>
                            </div>

                            <h3 class="fs-5 fw-semibold mb-2">
                                Audit Aktivitas & Keamanan
                            </h3>

                            <p style="font-size:.875rem;color:var(--tx2)">
                                Pantau aktivitas perusahaan dan keamanan sistem melalui riwayat aksi, pelaku, waktu,
                                alamat IP,
                                serta detail aktivitas.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Paket & Langganan -->
        <section id="harga" class="sp position-relative" style="background:var(--bg2)">
            <div class="aur aur-a" style="top:50%;left:50%;transform:translate(-50%,-50%)"></div>
            <div class="container position-relative" style="z-index:1">
                <div class="text-center mb-5 rv">
                    <span class="slbl">Paket & Langganan</span>
                    <h2 class="stitle">Pilih Paket <span class="gt">Sesuai Kebutuhan </span></h2>
                    <p class="ssub mx-auto">Kelola paket langganan MWork dengan mudah dan transparan. Pilih paket sesuai
                        kebutuhan perusahaan tanpa biaya tersembunyi.</p>
                </div>

                <!-- ======================== PRICING SLIDER ======================== -->
                <div class="pricing-slider rv">

                    <!-- Tombol kiri -->
                    <button class="pricing-arrow pricing-prev" id="pricingPrev" type="button">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <!-- Area slider -->
                    <div class="pricing-window">
                        <div class="pricing-track" id="pricingTrack">

                            <!-- ================= Free ================= -->
                            <div class="pricing-item">
                                <div class="pcard h-100">
                                    <div class="pricing-head">
                                        <div class="pricing-name">
                                            Free
                                        </div>
                                        <div class="pricing-price-badge">
                                            Rp 0 / bulan
                                        </div>
                                    </div>
                                    <p class="pricing-desc">
                                    </p>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Maks. pengguna: <strong>10</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Penyimpanan: <strong>3 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-clock"></i>
                                        </span>
                                        Masa aktif: <strong>1 bulan</strong>
                                    </div>
                                    <div class="pfl unavailable">
                                        <span class="pno">
                                            <i class="fa-solid fa-xmark"></i> </span>
                                        Audit Log Aktivitas
                                    </div>
                                    <div class="pfl unavailable">
                                        <span class="pno">
                                            <i class="fa-solid fa-xmark"></i> </span>
                                        Login tanpa .mm Suffix
                                    </div>
                                </div>
                            </div>

                            <!-- ================= TRY ME ================= -->
                            <div class="pricing-item">
                                <div class="pcard h-100">
                                    <div class="pricing-head">
                                        <div class="pricing-name">
                                            Try Me
                                        </div>
                                        <div class="pricing-price-badge">
                                            Rp 2.000 / 7 hari
                                        </div>
                                    </div>
                                    <p class="pricing-desc">
                                    </p>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Maks. pengguna: <strong>30</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Penyimpanan: <strong>3 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-clock"></i>
                                        </span>
                                        Masa aktif: <strong>7 hari</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Audit Log Aktivitas
                                    </div>
                                    <div class="pfl unavailable">
                                        <span class="pno">
                                            <i class="fa-solid fa-xmark"></i>
                                        </span>
                                        Login Tanpa .mm Suffix
                                    </div>
                                </div>
                            </div>

                            <!-- ================= PRO ================= -->
                            <div class="pricing-item">
                                <div class="pcard pop h-100">
                                    <span class="pbadge">
                                        <i class="fa-solid fa-star me-1"></i>
                                        Paling Populer
                                    </span>
                                    <div class="pricing-head">
                                        <div class="pricing-name">
                                            Pro
                                        </div>
                                        <div class="pricing-price-badge">
                                            Rp 5.000 / bulan
                                        </div>
                                    </div>
                                    <p class="pricing-desc">
                                    </p>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Maks. pengguna: <strong>30</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Penyimpanan: <strong>12 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-clock"></i>
                                        </span>
                                        Masa aktif: <strong>1 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Audit Log Aktivitas
                                    </div>
                                    <div class="pfl unavailable">
                                        <span class="pno">
                                            <i class="fa-solid fa-xmark"></i>
                                        </span>
                                        Login Tanpa .mm Suffix
                                    </div>
                                </div>
                            </div>

                            <!-- ================= BISNIS ================= -->
                            <div class="pricing-item">
                                <div class="pcard h-100">
                                    <div class="pricing-head">
                                        <div class="pricing-name">
                                            Bisnis
                                        </div>
                                        <div class="pricing-price-badge">
                                            Rp 15.000 / bulan
                                        </div>
                                    </div>
                                    <p class="pricing-desc">
                                    </p>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Maks. pengguna: <strong>100</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Penyimpanan: <strong>24 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-clock"></i>
                                        </span>
                                        Masa aktif: <strong>1 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Audit Log Aktivitas
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Login Tanpa .mm Suffix
                                    </div>
                                </div>
                            </div>

                            <!-- ================= PAKET KHUSUS ================= -->
                            <div class="pricing-item">
                                <div class="pcard h-100">
                                    <div class="pricing-head">
                                        <div class="pricing-name">
                                            Paket Khusus
                                        </div>
                                        <div class="pricing-price-badge">
                                            Rp 240.000 / 24 bulan
                                        </div>
                                    </div>
                                    <div class="special-label">
                                        PAKET KHUSUS MAKSIMASA
                                    </div>
                                    <p class="pricing-desc">
                                    </p>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Maks. pengguna: <strong>30</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Penyimpanan: <strong>24 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-clock"></i>
                                        </span>
                                        Masa aktif: <strong>24 bulan</strong>
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Audit Log Aktivitas
                                    </div>
                                    <div class="pfl">
                                        <span class="pchk">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        Login Tanpa .mm Suffix
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol kanan -->
                    <button class="pricing-arrow pricing-next" id="pricingNext" type="button">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <!-- ======================== INDICATOR ======================== -->
                <div class="pricing-dots" id="pricingDots"></div>
            </div>
        </section>

        <!-- Cara Berlangganan/Pembayaran -->
        <section id="tutorial" class="sp" style="background:var(--bg3)">
            <div class="container">
                <div class="text-center mb-5 rv">
                    <span class="slbl">Cara Berlangganan / Pembayaran</span>
                    <p class="ssub mx-auto">Proses berlangganan yang mudah dan aman dengan berbagai metode pembayaran
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 rv">
                        <div class="gc p-4 h-100 text-center">
                            <div class="hnum">1</div>
                            <h3 class="fs-5 fw-semibold mb-2">Daftar</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Buat akun untuk menggunakan sistem.</p>
                        </div>
                    </div>
                    <div class="col-md-6 rv" style="transition-delay:.1s">
                        <div class="gc p-4 h-100 text-center">
                            <div class="hnum">2</div>
                            <h3 class="fs-5 fw-semibold mb-2">Pilih paket</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Pilih paket layanan sesuai kebutuhan.</p>
                        </div>
                    </div>
                    <div class="col-md-6 rv" style="transition-delay:.2s">
                        <div class="gc p-4 h-100 text-center">
                            <div class="hnum">3</div>
                            <h3 class="fs-5 fw-semibold mb-2">Lakukan Pembayaran</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Selesaikan pembayaran melalui metod
                                epembayaran yang
                                tersedia.</p>
                        </div>
                    </div>
                    <div class="col-md-6 rv" style="transition-delay:.2s">
                        <div class="gc p-4 h-100 text-center">
                            <div class="hnum">4</div>
                            <h3 class="fs-5 fw-semibold mb-2">Gunakan Sistem</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Mulai kelola data dan aktivitas melalui
                                sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alasan -->
        <section id="alasan" class="sp position-relative">
            <div class="aur aur-b" style="top:50%;right:-200px;transform:translateY(-50%)"></div>
            <div class="container position-relative" style="z-index:1">
                <div class="text-center mb-5 rv">
                    <span class="slbl">Alasan Penggunaan MWork</span>
                    <h2 class="stitle">Mengapa Menggunakan <span class="gt">MWork?</span></h2>
                    <p class="ssub mx-auto">Menjelaskan masalah absensi yang diselesaikan MWork dan manfaat yang
                        didapat pengguna.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4 rv">
                        <div class="gc p-4 h-100">
                            <div
                                style="width:52px;height:52px;border-radius:14px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);display:flex;align-items:center;justify-content:center;margin-bottom:18px">
                                <i class="fa-solid fa-calendar-days fa-lg" style="color:#f87171"></i>
                            </div>
                            <h3 class="fw-semibold fs-5 mb-2">Absensi yang fleksibel</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Aturan terjadwal, fleksibel, WFO, WFH, dan
                                laporan harian.</p>
                        </div>
                    </div>
                    <div class="col-md-4 rv" style="transition-delay:.1s">
                        <div class="gc p-4 h-100">
                            <div
                                style="width:52px;height:52px;border-radius:14px;background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.2);display:flex;align-items:center;justify-content:center;margin-bottom:18px">
                                <i class="fa-solid fa-location-dot fa-lg" style="color:#fbbf24"></i>
                            </div>
                            <h3 class="fw-semibold fs-5 mb-2">Lokasi tercatat</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Setiap absensi menyimpan posisi GPS sebagai
                                bagian
                                dari riwayat.</p>
                        </div>
                    </div>
                    <div class="col-md-4 rv" style="transition-delay:.2s">
                        <div class="gc p-4 h-100">
                            <div
                                style="width:52px;height:52px;border-radius:14px;background:rgba(99,102,241,.12);border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;margin-bottom:18px">
                                <i class="fa-solid fa-users-gear fa-lg" style="color:#a78bfa"></i>
                            </div>
                            <h3 class="fw-semibold fs-5 mb-2">Akses sesuai peran</h3>
                            <p style="font-size:.875rem;color:var(--tx2)">Admin, operator, atasan, dan anggota
                                memperoleh halaman yang sesuai.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pertanyaan Umum -->
        <section id="tentang" class="sp" style="background:var(--bg2)">
            <div class="container">
                <div class="text-center mb-5 rv">
                    <span class="slbl">FAQ</span>
                    <h2 class="stitle">Pertanyaan <span class="gt">Umum</span></h2>
                </div>
                <div class="row justify-content-center rv">
                    <div class="col-lg-8">
                        <div class="accordion acco" id="faqAcc">
                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#f1">Apa itu MWork?</button></h2>
                                <div id="f1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAcc">
                                    <div class="accordion-body">MWork merupakan sistem manajemen absensi yang membantu
                                        perusahaan
                                        dalam mengelola dan memantau kehadiran karyawan secara lebih terstruktur.</div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#f2">Apakah MWork
                                        mendukung sistem kerja WFO dan
                                        WFH?</button>
                                </h2>
                                <div id="f2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                    <div class="accordion-body">Ya. MWork mendukung pengaturan absensi untuk berbagai
                                        kondisi
                                        kerja, termasuk Work From Office (WFO) dan Work From Home (WFH), sesuai dengan
                                        aturan yang
                                        ditetapkan perusahaan.</div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#f3">Apakah lokasi
                                        pengguna dapat tercatat saat
                                        melakukan absensi?</button>
                                </h2>
                                <div id="f3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                    <div class="accordion-body">Ya. MWork dapat mencatat lokasi pengguna melalui GPS
                                        ketika
                                        melakukan absensi sehingga informasi lokasi dapat menjadi bagian dari riwayat
                                        kehadiran.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#f4">Siapa saja yang
                                        dapat menggunakan
                                        MWork?</button></h2>
                                <div id="f4" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                    <div class="accordion-body">MWork dapat digunakan oleh beberapa peran pengguna,
                                        seperti
                                        admin, operator, atasan, dan anggota, dengan hak akses yang disesuaikan
                                        berdasarkan
                                        perannya masing-masing.</div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#f5">Apakah data
                                        absensi dapat dilihat
                                        kembali?</button></h2>
                                <div id="f5" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                    <div class="accordion-body">Ya. Data absensi yang telah tercatat dapat dilihat
                                        kembali
                                        sebagai riwayat kehadiran untuk membantu proses pemantauan dan pengelolaan
                                        absensi.</div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header"><button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#f6">Bagaimana jika
                                        mengalami kendala saat
                                        menggunakan MWork?</button></h2>
                                <div id="f6" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                                    <div class="accordion-body">Pengguna dapat menghubungi tim support melalui
                                        informasi kontak
                                        yang tersedia pada bagian Butuh Bantuan? untuk mendapatkan bantuan terkait
                                        penggunaan
                                        sistem maupun kendala lainnya.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bantuan -->
        <section id="kontak" class="sp" style="background:var(--bg2)">
            <div class="container">

                <div class="help-box rv">

                    <!-- KIRI -->
                    <div class="help-content">

                        <h2 class="help-title">
                            Butuh Bantuan?
                        </h2>

                        <p class="help-desc">
                            Tim support kami siap membantu Anda terkait penggunaan sistem,
                            paket layanan, maupun kendala pembayaran.
                        </p>

                        <div class="help-info">

                            <!-- Email -->
                            <div class="help-item">
                                <div class="help-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>

                                <div>
                                    <span>Email</span>
                                    <p>support@mwork.id</p>
                                </div>
                            </div>

                            <!-- Telepon -->
                            <div class="help-item">
                                <div class="help-icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>

                                <div>
                                    <span>Telepon</span>
                                    <p>+62 812 3456 7890</p>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="help-item">
                                <div class="help-icon">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>
                                    <span>Alamat</span>
                                    <p>
                                        Jl. Melati No. 10, Kec. Lowokwaru,<br>
                                        Kota Malang, Jawa Timur, Indonesia
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- KANAN / ILUSTRASI -->
                    <div class="help-visual">

                        <!-- Lingkaran belakang -->
                        <div class="help-circle">

                            <!-- Ilustrasi orang -->
                            <svg class="support-person" viewBox="0 0 220 220" xmlns="http://www.w3.org/2000/svg">

                                <!-- badan -->
                                <path d="M65 210c4-43 29-65 45-65s41 22 45 65" fill="#dbeafe" />

                                <!-- leher -->
                                <path d="M96 126v28c0 7 8 12 14 12s14-5 14-12v-28z" fill="#f3c7a5" />

                                <!-- rambut belakang -->
                                <path d="M70 76c0-29 18-48 42-48
                        29 0 44 21 44 49v43
                        c0 18-13 32-43 32
                        -27 0-43-14-43-32z" fill="#1e3a8a" />

                                <!-- wajah -->
                                <path d="M82 76c0-22 11-36 31-36
                        22 0 32 15 32 37v31
                        c0 18-12 29-32 29
                        -19 0-31-11-31-29z" fill="#f3c7a5" />

                                <!-- rambut depan -->
                                <path d="M78 76c2-31 17-47 38-47
                        25 0 39 17 39 43
                        -9-7-18-13-26-22
                        -9 13-29 23-51 26z" fill="#172554" />

                                <!-- mata -->
                                <circle cx="99" cy="88" r="2.5" fill="#1f2937" />
                                <circle cx="128" cy="88" r="2.5" fill="#1f2937" />

                                <!-- headset -->
                                <path d="M75 91c0-32 17-51 42-51
                        27 0 44 21 44 51" fill="none" stroke="#2563eb" stroke-width="8"
                                    stroke-linecap="round" />

                                <!-- ear kiri -->
                                <rect x="69" y="88" width="15" height="30" rx="7" fill="#2563eb" />

                                <!-- ear kanan -->
                                <rect x="151" y="88" width="15" height="30" rx="7" fill="#2563eb" />

                                <!-- microphone -->
                                <path d="M157 111c12 0 19 5 23 12" fill="none" stroke="#2563eb" stroke-width="5"
                                    stroke-linecap="round" />

                                <circle cx="181" cy="125" r="4" fill="#2563eb" />

                                <!-- pakaian -->
                                <path d="M78 151c8 9 19 14 32 14
                        14 0 26-5 34-14
                        14 9 23 25 28 59H51
                        c4-34 13-50 27-59z" fill="#bfdbfe" />

                                <!-- bagian tengah baju -->
                                <path d="M110 165l-15 45h31z" fill="#eff6ff" />

                            </svg>

                        </div>

                        <!-- Bubble Chat -->
                        <div class="help-chat">
                            <i class="fa-solid fa-message"></i>
                        </div>

                    </div>

                </div>

            </div>
        </section>

    </main>

    <!-- footer -->
    @include('landing.footer')

    <!-- JS -->
    <script src="{{ asset('landingjs/aos.js') }}"></script>
    <script src="{{ asset('landingjs/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landingjs/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landingjs/chart.umd.min.js') }}"></script>
    <script src="{{ asset('landingjs/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('landingjs/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('landingjs/landing.js') }}"></script>
    <script src="{{ asset('landingjs/main.js') }}"></script>

</body>

</html>

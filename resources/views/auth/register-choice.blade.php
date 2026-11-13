<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun · MWork</title>
    <meta name="robots" content="noindex,follow">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
    @include('auth.partials.registration-classic-styles')
</head>
<body class="registration-page min-h-screen antialiased">
    <main class="mx-auto flex min-h-screen max-w-5xl items-center px-4 py-8 sm:px-6">
        <section class="registration-window w-full shadow-md">
            <header class="registration-titlebar">
                <div class="flex items-center gap-3"><img src="{{ asset('mworkputih.png') }}" alt="MWork" class="h-7 w-7 object-contain"><strong class="text-sm">MWork · Pendaftaran Akun</strong></div>
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-300 hover:text-white">Kembali ke halaman masuk</a>
            </header>
            <div class="border-b border-slate-300 bg-slate-50 px-6 py-7 text-center sm:px-10">
                <p class="text-xs font-bold uppercase tracking-[.16em] text-blue-700">Pilih jenis pendaftaran</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Apa yang ingin Anda daftarkan?</h1>
                <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-600">Pilih pendaftaran anggota jika organisasi Anda sudah memakai MWork. Pilih workspace baru jika Anda akan menjadi admin utama organisasi.</p>
            </div>
            <div class="grid gap-5 p-5 sm:p-7 md:grid-cols-2">
                <a href="{{ route('members.register') }}" class="group rounded-xl border border-slate-200 bg-white p-6 transition hover:border-indigo-300 hover:bg-indigo-50/40">
                    <div class="flex items-start gap-4"><span class="registration-number">01</span><div><p class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Untuk pengguna</p><h2 class="mt-1 text-xl font-bold text-slate-900">Daftar sebagai anggota</h2></div></div>
                    <p class="mt-5 text-sm leading-6 text-slate-600">Untuk karyawan, siswa, operator, atasan, atau anggota yang sudah menerima kode perusahaan dari admin.</p>
                    <div class="registration-help mt-5 px-4 py-3 text-xs leading-5 text-slate-600"><strong>Siapkan:</strong> kode perusahaan, username, pilihan tim, dan password.</div>
                    <span class="registration-link mt-5 inline-flex text-sm font-bold">Lanjutkan pendaftaran anggota →</span>
                </a>
                <a href="{{ route('organizations.create') }}" class="group rounded-xl border border-slate-200 bg-white p-6 transition hover:border-indigo-300 hover:bg-indigo-50/40">
                    <div class="flex items-start gap-4"><span class="registration-number">02</span><div><p class="text-[11px] font-bold uppercase tracking-wider text-indigo-600">Untuk pengelola</p><h2 class="mt-1 text-xl font-bold text-slate-900">Buat workspace baru</h2></div></div>
                    <p class="mt-5 text-sm leading-6 text-slate-600">Untuk perusahaan, UMKM, sekolah, kantor, instansi, atau komunitas yang belum memiliki workspace.</p>
                    <div class="registration-help mt-5 px-4 py-3 text-xs leading-5 text-slate-600"><strong class="text-slate-800">Hasilnya:</strong> Anda menjadi admin utama dan menerima kode perusahaan untuk dibagikan kepada anggota.</div>
                    <span class="registration-link mt-5 inline-flex text-sm font-bold">Lanjutkan pembuatan workspace →</span>
                </a>
            </div>
            <footer class="border-t border-slate-300 bg-slate-100 px-6 py-3 text-center text-xs text-slate-500">MWork by Masimasa · Sistem kehadiran untuk berbagai jenis organisasi</footer>
        </section>
    </main>
</body>
</html>

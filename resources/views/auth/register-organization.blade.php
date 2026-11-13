<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Workspace · MWork</title>
    <meta name="robots" content="noindex,follow">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
    @include('auth.partials.registration-classic-styles')
</head>
<body class="registration-page min-h-screen antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-8 sm:px-6">
        <section class="registration-window w-full shadow-md">
            <header class="registration-titlebar">
                <div class="flex items-center gap-3"><img src="{{ asset('mworkputih.png') }}" alt="MWork" class="h-7 w-7 object-contain"><strong class="text-sm">MWork · Pembuatan Workspace</strong></div>
                <div class="flex gap-4 text-xs font-semibold"><a href="{{ route('registrations.choice') }}" class="text-slate-300 hover:text-white">Ganti jenis pendaftaran</a><a href="{{ route('login') }}" class="text-slate-300 hover:text-white">Halaman masuk</a></div>
            </header>

            <div class="grid lg:grid-cols-[380px_minmax(0,1fr)]">
                <aside class="border-b border-slate-400 bg-slate-100 p-6 lg:border-b-0 lg:border-r lg:p-8">
                    <p class="text-xs font-bold uppercase tracking-[.14em] text-blue-700">Untuk admin organisasi</p>
                    <h1 class="mt-3 text-2xl font-bold leading-tight text-slate-900">Buat workspace baru untuk organisasi Anda</h1>
                    <p class="mt-4 text-sm leading-6 text-slate-600">Akun yang dibuat di halaman ini menjadi admin utama. Admin dapat mengatur anggota, tim, lokasi, aturan absensi, dan paket layanan.</p>
                    <div class="registration-help mt-7">
                        <div class="registration-section-title">Setelah workspace dibuat</div>
                        <ol class="divide-y divide-slate-300 px-4 text-sm">
                            <li class="flex gap-3 py-4"><span class="registration-number">01</span><span><strong class="block text-slate-800">Kode perusahaan diterbitkan</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Kode acak dapat dibagikan kepada calon anggota.</span></span></li>
                            <li class="flex gap-3 py-4"><span class="registration-number">02</span><span><strong class="block text-slate-800">Atur kebutuhan organisasi</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Tambahkan tim, lokasi, dan aturan absensi sesuai kebutuhan.</span></span></li>
                            <li class="flex gap-3 py-4"><span class="registration-number">03</span><span><strong class="block text-slate-800">Setujui pendaftaran anggota</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Anggota baru tidak langsung aktif sebelum diperiksa admin.</span></span></li>
                        </ol>
                    </div>
                    <div class="mt-5 border border-blue-300 bg-blue-50 p-4 text-xs leading-5 text-blue-900"><strong>Catatan:</strong> nama dan slug perusahaan harus unik. Sistem tidak menambahkan akhiran angka secara otomatis.</div>
                </aside>

                <div class="bg-white">
                    <div class="registration-section-title">Formulir workspace dan admin utama</div>
                    <div class="p-6 sm:p-8 lg:p-10">
                        <h2 class="text-2xl font-bold text-slate-900">Data organisasi</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Isi identitas organisasi terlebih dahulu, kemudian buat akun admin yang akan mengelolanya.</p>

                        @if($errors->any())
                            <div class="mt-5 border border-rose-300 bg-rose-50 p-4 text-sm text-rose-800"><strong>Workspace belum dapat dibuat. Periksa data berikut:</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif

                        <form id="workspaceForm" method="POST" action="{{ route('organizations.store') }}" class="mt-7 grid gap-5 sm:grid-cols-2">
                            @csrf
                            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold text-slate-700">Nama organisasi</span><input id="organizationName" name="name" value="{{ old('name') }}" required maxlength="120" class="h-12 w-full border border-slate-400 px-4 outline-none" placeholder="Contoh: PT Acme Indonesia"><span class="mt-1.5 block text-xs text-slate-500">Gunakan nama resmi atau nama yang mudah dikenali anggota.</span></label>

                            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold text-slate-700">Slug perusahaan</span><div class="flex border border-slate-400 bg-white"><span class="flex items-center border-r border-slate-300 bg-slate-100 px-3 text-sm text-slate-600">@</span><input id="organizationSlug" name="slug" value="{{ old('slug') }}" required minlength="3" maxlength="80" autocomplete="off" spellcheck="false" class="h-12 min-w-0 flex-1 px-4 lowercase outline-none" placeholder="pt-acme-indonesia"><span class="flex items-center border-l border-slate-300 bg-slate-100 px-3 text-sm text-slate-600">.mm</span></div><span id="slugStatus" aria-live="polite" class="mt-1.5 block min-h-4 text-xs text-slate-500">Contoh ID login: admin@pt-acme-indonesia.mm. Slug tidak akan diberi akhiran angka otomatis.</span></label>

                            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold text-slate-700">Jenis organisasi</span><select name="type" class="h-12 w-full border border-slate-400 bg-white px-4 outline-none"><option value="company" @selected(old('type') === 'company')>Perusahaan</option><option value="small_business" @selected(old('type') === 'small_business')>UMKM</option><option value="school" @selected(old('type') === 'school')>Sekolah / Kampus</option><option value="government" @selected(old('type') === 'government')>Instansi</option><option value="community" @selected(old('type') === 'community')>Komunitas</option></select><span class="mt-1.5 block text-xs text-slate-500">Pilihan ini membantu MWork menyesuaikan istilah yang digunakan di dalam workspace.</span></label>

                            <div class="sm:col-span-2 mt-2"><div class="registration-section-title border border-slate-400">Akun admin utama</div><p class="mt-2 text-xs leading-5 text-slate-500">Akun ini langsung aktif dan memiliki akses untuk mengatur seluruh workspace.</p></div>
                            <label class="sm:col-span-2"><span class="mb-2 block text-sm font-bold text-slate-700">Username admin</span><input name="username" value="{{ old('username') }}" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9._-]+" autocomplete="username" class="h-12 w-full border border-slate-400 px-4 lowercase outline-none" placeholder="Contoh: admin"><span class="mt-1.5 block text-xs text-slate-500">Username boleh sama dengan perusahaan lain karena slug workspace membedakan ID login.</span></label>
                            <label><span class="mb-2 block text-sm font-bold text-slate-700">Password</span><input type="password" name="password" required minlength="8" autocomplete="new-password" class="h-12 w-full border border-slate-400 px-4 outline-none" placeholder="Minimal 8 karakter"></label>
                            <label><span class="mb-2 block text-sm font-bold text-slate-700">Ulangi password</span><input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="h-12 w-full border border-slate-400 px-4 outline-none" placeholder="Ketik kembali password"></label>
                            <button id="submitWorkspace" class="registration-primary sm:col-span-2 mt-1 h-12 font-bold text-white disabled:cursor-not-allowed disabled:border-slate-400 disabled:bg-slate-300">Buat workspace dan akun admin</button>
                        </form>
                        <p class="mt-5 border-t border-slate-200 pt-4 text-center text-xs leading-5 text-slate-500">Setelah berhasil, Anda langsung diarahkan ke dashboard admin dan menerima kode perusahaan.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        const nameInput = document.getElementById('organizationName');
        const slugInput = document.getElementById('organizationSlug');
        const slugStatus = document.getElementById('slugStatus');
        const submitButton = document.getElementById('submitWorkspace');
        const availabilityUrl = @json(route('organizations.slug-availability'));
        let slugEdited = slugInput.value.trim() !== '';
        let availabilityTimer;
        let availabilityRequest;

        const normalizeSlug = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 80);
        const setSlugState = (state, message) => {
            slugStatus.textContent = message;
            slugStatus.className = `mt-1.5 block min-h-4 text-xs font-medium ${state === 'available' ? 'text-emerald-700' : state === 'unavailable' || state === 'invalid' ? 'text-rose-700' : 'text-slate-500'}`;
            slugInput.parentElement.style.borderColor = state === 'available' ? '#34d399' : state === 'unavailable' || state === 'invalid' ? '#fb7185' : '';
            submitButton.disabled = ['checking', 'unavailable', 'invalid'].includes(state);
        };
        const checkSlug = () => {
            const slug = normalizeSlug(slugInput.value);
            slugInput.value = slug;
            clearTimeout(availabilityTimer);
            availabilityRequest?.abort();
            if (slug.length < 3) { setSlugState('invalid', 'Slug minimal 3 karakter.'); return; }
            setSlugState('checking', 'Memeriksa ketersediaan slug…');
            availabilityTimer = setTimeout(async () => {
                availabilityRequest = new AbortController();
                try {
                    const response = await fetch(`${availabilityUrl}?slug=${encodeURIComponent(slug)}`, { headers: { 'Accept': 'application/json' }, signal: availabilityRequest.signal });
                    const result = await response.json();
                    if (slugInput.value !== result.slug) return;
                    setSlugState(result.available ? 'available' : 'unavailable', result.available ? `${result.message} ID login admin akan menggunakan @${result.slug}.mm.` : result.message);
                } catch (error) {
                    if (error.name !== 'AbortError') setSlugState('error', 'Pemeriksaan otomatis gagal. Form tetap akan diperiksa saat dikirim.');
                }
            }, 350);
        };
        nameInput.addEventListener('input', () => { if (!slugEdited) { slugInput.value = normalizeSlug(nameInput.value); checkSlug(); } });
        slugInput.addEventListener('input', () => { slugEdited = true; checkSlug(); });
        slugInput.addEventListener('blur', checkSlug);
        if (slugInput.value || nameInput.value) { if (!slugInput.value) slugInput.value = normalizeSlug(nameInput.value); checkSlug(); } else { submitButton.disabled = true; }
    </script>
</body>
</html>

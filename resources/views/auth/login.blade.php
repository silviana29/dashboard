<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · {{ $organization?->name ?? 'MWork' }}</title>
    <meta name="robots" content="noindex,follow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{ink:'#101828',brand:'#4f46e5'},boxShadow:{soft:'0 28px 90px rgba(15,23,42,.16)'}}}}</script>
    @include('partials.pwa')
</head>
<body class="min-h-screen overflow-x-hidden bg-[#eef1f8] text-ink antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-28 -top-28 h-96 w-96 rounded-full bg-indigo-300/35 blur-3xl"></div>
        <div class="absolute -bottom-28 -right-20 h-[28rem] w-[28rem] rounded-full bg-cyan-200/40 blur-3xl"></div>
        <div class="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/80 blur-3xl"></div>
    </div>

    <main class="relative grid min-h-screen items-center gap-10 px-4 py-10 sm:px-8 lg:grid-cols-[minmax(0,1fr)_440px_minmax(0,1fr)] lg:px-10 xl:gap-16 xl:px-20">
        <section class="hidden justify-self-end lg:block lg:max-w-sm">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-indigo-600">Platform pengelolaan tim</p>
            <h2 class="mt-4 text-3xl font-semibold leading-tight tracking-[-.04em] text-slate-900 xl:text-4xl">Kelola kehadiran tim dalam satu workspace.</h2>
            <p class="mt-5 text-sm leading-7 text-slate-600">MWork membantu perusahaan, UMKM, kantor, dan sekolah mengatur absensi, izin, laporan harian, tim, serta pemantauan atasan.</p>
            <div class="mt-7 flex items-center gap-3 text-sm text-slate-600"><span class="h-px w-10 bg-indigo-400"></span><span>Dibuat untuk berbagai jenis organisasi</span></div>
        </section>

        <section class="w-full max-w-[440px] justify-self-center">
            <div class="overflow-hidden rounded-[2rem] border border-white/80 bg-white/95 shadow-soft backdrop-blur-xl">
                <div class="px-6 pb-7 pt-8 sm:px-9 sm:pb-9">
                    <div class="flex flex-col items-center text-center">
                        <img src="{{ asset('mworkputih.png') }}" alt="Logo MWork" class="h-20 w-20 object-contain">
                        <strong class="mt-1 text-lg tracking-tight text-slate-900">MWork</strong>
                        @if($organization)<p class="mt-1 text-sm text-slate-500">{{ $organization->name }}</p>@endif
                    </div>

                    @if($organization)
                        <div class="mt-6 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl text-base font-bold text-white" style="background:{{ $organization->primary_color }}">{{ strtoupper(substr($organization->name,0,1)) }}</span>
                            <div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $organization->name }}</p><p class="text-xs text-slate-500">{{ $organization->type_label }} · by MWork</p></div>
                        </div>
                    @endif

                    <div class="mt-6 text-center"><h1 class="text-2xl font-semibold tracking-[-.03em]">Masuk ke workspace</h1><p class="mt-1 text-sm leading-6 text-slate-500">Gunakan ID yang diberikan admin organisasi Anda.</p></div>

                    @if(session('success'))<div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                    @if($errors->any())<div class="mt-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

                    <form method="POST" action="{{ route('login.submit') }}" class="mt-6 space-y-4">
                        @csrf
                        <label class="block"><span class="mb-1.5 block text-sm font-medium text-slate-700">ID masuk</span><input id="identity" name="identity" value="{{ old('identity') }}" required autofocus autocomplete="username" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="{{ 'nama@'.($organization?->slug ?? 'perusahaan').(($organization && $organization->plan_details->has_custom_suffix) ? '' : '.mm') }}"></label>
                        <label class="block"><span class="mb-1.5 block text-sm font-medium text-slate-700">Password</span><div class="relative"><input type="password" id="password" name="password" required autocomplete="current-password" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4 pr-16 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100" placeholder="Masukkan password"><button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-slate-500 hover:text-indigo-600">Lihat</button></div></label>
                        <label class="flex items-center gap-2.5 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ old('remember') ? 'checked' : '' }}>Tetap masuk di perangkat ini</label>
                        <button class="h-11 w-full rounded-xl bg-indigo-600 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200">Masuk</button>
                    </form>

                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <a id="pwaInstallEntry" href="{{ route('pwa.install') }}" class="grid h-10 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50">Pasang aplikasi</a>
                        <a href="{{ route('registrations.choice') }}" class="grid h-10 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50">Daftar akun</a>
                    </div>
                </div>
            </div>
            <p class="mt-5 text-center text-xs text-slate-400">Dibuat di Indonesia untuk berbagai jenis organisasi.</p>
        </section>

        <section class="hidden max-w-sm lg:block">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-indigo-600">Mengapa MWork?</p>
            <div class="mt-5 space-y-5">
                <article class="border-l-2 border-indigo-300 pl-4"><h3 class="text-sm font-semibold text-slate-900">Absensi yang fleksibel</h3><p class="mt-1 text-sm leading-6 text-slate-600">Aturan terjadwal, fleksibel, WFO, WFH, dan laporan harian.</p></article>
                <article class="border-l-2 border-indigo-300 pl-4"><h3 class="text-sm font-semibold text-slate-900">Lokasi tercatat</h3><p class="mt-1 text-sm leading-6 text-slate-600">Setiap absensi menyimpan posisi GPS sebagai bagian dari riwayat.</p></article>
                <article class="border-l-2 border-indigo-300 pl-4"><h3 class="text-sm font-semibold text-slate-900">Akses sesuai peran</h3><p class="mt-1 text-sm leading-6 text-slate-600">Admin, operator, atasan, dan anggota memperoleh halaman yang sesuai.</p></article>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('togglePassword').addEventListener('click',function(){const input=document.getElementById('password');input.type=input.type==='password'?'text':'password';this.textContent=input.type==='password'?'Lihat':'Tutup'});

        const installEntry = document.getElementById('pwaInstallEntry');
        const syncInstallEntry = () => {
            if (installEntry && window.MWorkPWA) installEntry.hidden = window.MWorkPWA.isStandalone();
        };

        installEntry?.addEventListener('click', async (event) => {
            const pwa = window.MWorkPWA;
            if (!pwa || pwa.isIos()) return;

            event.preventDefault();
            const installPage = installEntry.href;

            if (!pwa.canInstall()) {
                installEntry.textContent = 'Membuka pemasangan…';
                await pwa.waitForPrompt(1200);
            }

            if (!pwa.canInstall()) {
                window.location.href = installPage;
                return;
            }

            installEntry.textContent = 'Konfirmasi pemasangan';
            const choice = await pwa.promptInstall();
            if (choice?.outcome === 'accepted') {
                installEntry.hidden = true;
                return;
            }

            installEntry.textContent = 'Pasang aplikasi';
        });

        syncInstallEntry();
        window.addEventListener('mwork-pwa-changed', syncInstallEntry);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota · MWork</title>
    <meta name="robots" content="noindex,follow">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
    @include('auth.partials.registration-classic-styles')
</head>
<body class="registration-page min-h-screen antialiased">
    <main class="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-8 sm:px-6">
        <section class="registration-window w-full shadow-md">
            <header class="registration-titlebar">
                <div class="flex items-center gap-3"><img src="{{ asset('mworkputih.png') }}" alt="MWork" class="h-7 w-7 object-contain"><strong class="text-sm">MWork · Pendaftaran Anggota</strong></div>
                <div class="flex gap-4 text-xs font-semibold"><a href="{{ route('registrations.choice') }}" class="text-slate-300 hover:text-white">Ganti jenis pendaftaran</a><a href="{{ route('login') }}" class="text-slate-300 hover:text-white">Halaman masuk</a></div>
            </header>

            <div class="grid lg:grid-cols-[360px_minmax(0,1fr)]">
                <aside class="border-b border-slate-400 bg-slate-100 p-6 lg:border-b-0 lg:border-r lg:p-8">
                    <p class="text-xs font-bold uppercase tracking-[.14em] text-blue-700">Untuk anggota organisasi</p>
                    <h1 class="mt-3 text-2xl font-bold leading-tight text-slate-900">Daftar menggunakan kode perusahaan</h1>
                    <p class="mt-4 text-sm leading-6 text-slate-600">Kode perusahaan diberikan oleh admin organisasi. Kode ini memastikan akun Anda masuk ke workspace yang benar.</p>
                    <div class="registration-help mt-7">
                        <div class="registration-section-title">Proses pendaftaran</div>
                        <ol class="divide-y divide-slate-300 px-4 text-sm">
                            <li class="flex gap-3 py-4"><span class="registration-number">01</span><span><strong class="block text-slate-800">Isi data akun</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Masukkan kode perusahaan, username, tim, dan password.</span></span></li>
                            <li class="flex gap-3 py-4"><span class="registration-number">02</span><span><strong class="block text-slate-800">Menunggu persetujuan</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Admin memeriksa data dan dapat menyesuaikan tim Anda.</span></span></li>
                            <li class="flex gap-3 py-4"><span class="registration-number">03</span><span><strong class="block text-slate-800">Mulai menggunakan MWork</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Setelah disetujui, masuk memakai ID workspace perusahaan.</span></span></li>
                        </ol>
                    </div>
                    <p class="mt-5 text-xs leading-5 text-slate-500">Tidak memiliki kode perusahaan? Hubungi admin atau pengelola organisasi Anda.</p>
                </aside>

                <div class="bg-white">
                    <div class="registration-section-title">Formulir akun anggota</div>
                    <div class="p-6 sm:p-8 lg:p-10">
                        @if($organization)
                            <div class="mb-6 flex items-center gap-4 border border-blue-300 bg-blue-50 p-4"><span class="grid h-11 w-11 place-items-center text-base font-bold text-white" style="background:{{ $organization->primary_color }}">{{ strtoupper(substr($organization->name,0,1)) }}</span><div><strong class="block text-slate-900">{{ $organization->name }}</strong><span class="text-xs text-slate-600">Kode perusahaan sudah terisi melalui tautan undangan.</span></div></div>
                        @endif
                        <h2 class="text-2xl font-bold text-slate-900">Buat akun anggota</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan data yang mudah Anda ingat. Akun belum dapat dipakai sebelum disetujui admin perusahaan.</p>

                        @if($errors->any())
                            <div class="mt-5 border border-rose-300 bg-rose-50 p-4 text-sm text-rose-800"><strong>Periksa kembali data berikut:</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif

                        <form method="POST" action="{{ route('members.register.store') }}" class="mt-7 space-y-5">@csrf
                            <label class="block"><span class="mb-2 block text-sm font-bold text-slate-700">Kode perusahaan</span><input id="organizationCode" name="organization_code" value="{{ old('organization_code',$organization?->company_code) }}" @readonly($organization) required class="h-12 w-full border border-slate-400 px-4 font-mono text-sm uppercase outline-none read-only:bg-slate-100" placeholder="Contoh: MM-A7K9P2QX"><span class="mt-1.5 block text-xs text-slate-500">Salin kode persis seperti yang diberikan admin perusahaan.</span></label>
                            <label class="block"><span class="mb-2 block text-sm font-bold text-slate-700">Username</span><input id="memberUsername" name="username" value="{{ old('username') }}" required autocomplete="username" minlength="3" pattern="[a-zA-Z0-9._-]+" class="h-12 w-full border border-slate-400 px-4 text-sm lowercase outline-none" placeholder="Contoh: budi"><span id="usernameAvailability" aria-live="polite" class="mt-2 hidden text-xs font-medium"></span><span class="mt-1 block text-xs text-slate-500">Username harus unik di perusahaan ini. Gunakan huruf, angka, titik, garis bawah, atau tanda minus.</span></label>
                            <label class="block"><span class="mb-2 block text-sm font-bold text-slate-700">Tim yang dituju</span><select id="memberTeam" name="team_id" class="h-12 w-full border border-slate-400 bg-white px-4 text-sm outline-none"><option value="">Belum memilih tim</option>@foreach($organization?->teams ?? [] as $team)<option value="{{ $team->id }}" @selected((string)old('team_id')===(string)$team->id)>{{ $team->name }}</option>@endforeach</select><span class="mt-1.5 block text-xs text-slate-500">Pilihan ini menjadi permintaan awal. Admin tetap dapat menyesuaikannya saat menyetujui akun.</span></label>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label><span class="mb-2 block text-sm font-bold text-slate-700">Password</span><input type="password" name="password" required minlength="8" autocomplete="new-password" class="h-12 w-full border border-slate-400 px-4 text-sm outline-none" placeholder="Minimal 8 karakter"></label>
                                <label><span class="mb-2 block text-sm font-bold text-slate-700">Ulangi password</span><input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="h-12 w-full border border-slate-400 px-4 text-sm outline-none" placeholder="Ketik kembali password"></label>
                            </div>
                            <button class="registration-primary h-12 w-full font-bold text-white">Kirim permintaan pendaftaran</button>
                        </form>
                        <p class="mt-5 border-t border-slate-200 pt-4 text-center text-xs leading-5 text-slate-500">Dengan mengirim formulir ini, Anda menyatakan bahwa kode perusahaan diperoleh dari organisasi yang berwenang.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        const organizationCode = document.getElementById('organizationCode');
        const memberUsername = document.getElementById('memberUsername');
        const availability = document.getElementById('usernameAvailability');
        const memberTeam = document.getElementById('memberTeam');
        let availabilityTimer;
        async function checkUsernameAvailability() {
            const code = organizationCode.value.trim();
            const username = memberUsername.value.trim().toLowerCase();
            memberUsername.value = username;
            if (!code || username.length < 3) { availability.classList.add('hidden'); return; }
            try {
                const params = new URLSearchParams({organization_code: code, username});
                const response = await fetch(`{{ route('members.username-availability') }}?${params}`);
                const result = await response.json();
                const selectedTeam = memberTeam.value;
                memberTeam.replaceChildren(new Option('Belum memilih tim', ''));
                (result.teams || []).forEach(team => memberTeam.add(new Option(team.name, team.id)));
                if ([...memberTeam.options].some(option => option.value === selectedTeam)) memberTeam.value = selectedTeam;
                if (result.available === null) { availability.classList.add('hidden'); return; }
                availability.textContent = result.available ? `Tersedia: ${result.message}` : result.message;
                availability.className = `mt-2 block text-xs font-bold ${result.available ? 'text-emerald-700' : 'text-rose-700'}`;
                memberUsername.setCustomValidity(result.available ? '' : result.message);
            } catch (_) { availability.classList.add('hidden'); memberUsername.setCustomValidity(''); }
        }
        [organizationCode, memberUsername].forEach(input => input.addEventListener('input', () => {
            memberUsername.setCustomValidity('');
            clearTimeout(availabilityTimer);
            availabilityTimer = setTimeout(checkUsernameAvailability, 350);
        }));
        if (memberUsername.value) checkUsernameAvailability();
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Organisasi · {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html,body{overflow-x:hidden}</style>
    @include('partials.pwa')
</head>
<body class="min-h-screen bg-[#f8fafc] text-slate-900 antialiased">
    @include('partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-950/60 lg:hidden"></div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 backdrop-blur-xl sm:px-8">
            <div class="flex items-center gap-4">
                <button id="menuButton" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 lg:hidden">Menu</button>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-slate-500">Pengaturan workspace</p>
                    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Profil & Identitas Organisasi</h1>
                </div>
            </div>
            <div class="hidden items-center gap-3 sm:flex">
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Sistem aktif</span>
            </div>
        </header>

        <main class="p-4 sm:p-8 lg:p-10">
            @if(session('success'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

            <div class="mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Profil dan identitas organisasi</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola informasi publik, kode unik pendaftaran perusahaan, dan identitas login domain organisasi {{ $organization->name }}.</p>
            </div>
            
            <div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
                <form method="POST" action="{{ route('admin.organization.update') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    @csrf @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">Nama Tampilan Organisasi</label>
                            <input name="name" value="{{ $organization->name }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3.5 text-sm font-medium text-slate-900 outline-none focus:border-slate-500">
                            <span class="mt-1 block text-[11px] text-slate-400">Nama resmi yang muncul pada header, dashboard, dan laporan cetak PDF.</span>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-slate-700">ID Login Perusahaan (Slug Identitas)</label>
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">Locked / Permanen</span>
                            </div>
                            <div class="flex h-11 items-center rounded-xl border border-slate-200 bg-slate-50 px-3.5 font-mono text-sm font-bold text-slate-800">
                                {{ $organization->slug }}
                            </div>
                            <div class="mt-2 rounded-xl bg-slate-50 border border-slate-200 p-3.5 text-xs text-slate-600 space-y-1">
                                <p class="font-semibold text-slate-900">Format ID Login Seluruh Anggota:</p>
                                <p class="font-mono text-slate-800 text-[11px]">username&#64;{{ $organization->slug }}{{ $organization->plan_details->has_custom_suffix ? '' : '.mm' }}</p>
                                <p class="text-[11px] text-slate-400">Setiap anggota masuk menggunakan akhiran domain di atas agar terhubung langsung ke workspace ini.</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Jenis Instansi / Organisasi</label>
                                <select name="type" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-xs text-slate-800 outline-none focus:border-slate-500">
                                    <option value="company" @selected($organization->type==='company')>Perusahaan / PT / CV</option>
                                    <option value="small_business" @selected($organization->type==='small_business')>UMKM / Usaha Mandiri</option>
                                    <option value="school" @selected($organization->type==='school')>Sekolah / Perguruan Tinggi</option>
                                    <option value="government" @selected($organization->type==='government')>Instansi Pemerintah</option>
                                    <option value="community" @selected($organization->type==='community')>Komunitas / Yayasan</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Warna Brand Utama</label>
                                <input type="color" name="primary_color" value="{{ $organization->primary_color }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white p-1.5 cursor-pointer">
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-5">
                            <button class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-3 text-xs font-bold text-white transition-colors">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Information Card & Company Code -->
                <div class="space-y-5">
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Kode Perusahaan (Pendaftaran Anggota)</h3>
                        <p class="mt-1 text-xs text-slate-500">Bagikan kode unik ini kepada calon anggota/karyawan baru untuk mendaftar mandiri.</p>
                        
                        <div class="mt-4 flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Kode Perusahaan</span>
                                <strong class="font-mono text-xl font-bold tracking-wider text-slate-900">{{ $organization->company_code }}</strong>
                            </div>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $organization->company_code }}'); alert('Kode perusahaan tersalin!')" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Salin Kode
                            </button>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Informasi Paket Workspace</h3>
                        <div class="mt-3 space-y-2 text-xs text-slate-600">
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span>Paket Langganan</span>
                                <strong class="text-slate-900 capitalize">{{ $organization->plan_details->name }}</strong>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-2">
                                <span>Batas Maksimal Pengguna</span>
                                <strong class="text-slate-900">{{ $organization->plan_details->max_users }} Akun</strong>
                            </div>
                            <div class="flex justify-between pb-1">
                                <span>Penyimpanan Log Audit</span>
                                <strong class="text-slate-900">{{ $organization->plan_details->has_audit_log ? 'Aktif' : 'Terbatas (3 Log)' }}</strong>
                            </div>
                        </div>
                        <a href="{{ route('admin.billing') }}" class="mt-4 block w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 text-center text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                            Kelola / Upgrade Langganan
                        </a>
                    </article>
                </div>
            </div>
        </main>
    </div>

    <script>
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay'),menuButton=document.getElementById('menuButton');
        menuButton.addEventListener('click',()=>{sidebar.classList.remove('-translate-x-full');overlay.classList.remove('hidden')});
        overlay.addEventListener('click',()=>{sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')});
    </script>
</body>
</html>

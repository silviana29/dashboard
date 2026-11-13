<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Policy · {{ $organization->name }}</title>
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
                    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Aturan absensi</h1>
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
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Aturan kebijakan absensi</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola jadwal waktu kerja, toleransi keterlambatan, jam pulang minimal, dan kategori perizinan untuk {{ $organization->name }}.</p>
            </div>
            
            <div class="grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
                <!-- Form & Saved Policy Presets -->
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-bold text-slate-900 text-base">Buat Aturan Absensi Baru</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Aturan yang dibuat dapat dipasangkan ke masing-masing anggota atau kelompok tim.</p>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.attendance-types.store') }}" class="mt-6 grid gap-5">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">1. Mode Pengaturan Waktu</label>
                            <select id="ruleMode" name="mode" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-xs text-slate-800 outline-none focus:border-slate-500">
                                <option value="scheduled">Terjadwal — Memiliki batas waktu masuk & jam pulang minimal</option>
                                <option value="flexible">Fleksibel — Bebas masuk/keluar tanpa perhitungan keterlambatan</option>
                                <option value="report">Laporan Harian — Mengirimkan 1 foto & ringkasan kegiatan harian</option>
                            </select>
                        </div>

                        <div id="ruleScheduleFields" class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Batas Waktu Tepat Masuk</label>
                                <input type="time" name="jam_telat" value="08:15" required class="h-11 w-full rounded-xl border border-slate-300 px-3.5 text-sm font-bold text-slate-900 outline-none focus:border-slate-500">
                                <span class="mt-1 block text-[11px] text-slate-400">Absen lewat waktu ini dicatat Terlambat</span>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">Jam Pulang Minimal</label>
                                <input type="time" name="jam_pulang_minimal" value="17:00" required class="h-11 w-full rounded-xl border border-slate-300 px-3.5 text-sm font-bold text-slate-900 outline-none focus:border-slate-500">
                                <span class="mt-1 block text-[11px] text-slate-400">Absen sebelum jam ini memicu peringatan</span>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">2. Nama Aturan Presets</label>
                            <input name="name" required maxlength="80" placeholder="Contoh: Jam Kerja Reguler Kantor, Shift Lapangan, Lapor Magang" class="h-11 w-full rounded-xl border border-slate-300 px-3.5 text-xs text-slate-800 outline-none focus:border-slate-500">
                        </div>

                        <button class="rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-3 text-xs font-bold text-white transition-colors">
                            Simpan Aturan Absensi
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <h4 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-600">Daftar Aturan Tersimpan</h4>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @forelse($attendanceTypes as $type)
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div>
                                        <strong class="block text-xs font-bold text-slate-900">{{ $type->name }}</strong>
                                        <span class="mt-1 block text-[11px] font-medium text-slate-500">{{ $type->mode_label }}</span>
                                        @if($type->mode === 'scheduled')
                                            <span class="mt-1 block text-[10px] text-slate-400">Masuk: {{ substr($type->jam_telat ?? '08:15', 0, 5) }} · Pulang: {{ substr($type->jam_pulang_minimal ?? '17:00', 0, 5) }}</span>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('admin.attendance-types.destroy',$type) }}" onsubmit="return confirm('Hapus aturan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 col-span-full">Belum ada aturan absensi tersimpan.</p>
                            @endforelse
                        </div>
                    </div>
                </article>

                <!-- Leave Categories Section -->
                <article class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-bold text-slate-900 text-base">Kategori Izin & Meninggalkan Tugas</h3>
                        <p class="mt-0.5 text-xs text-slate-500">Pilihan kategori yang dapat dipilih anggota saat mengajukan form izin.</p>
                    </div>

                    <div class="mt-5">
                        <span class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2.5">Kategori Aktif</span>
                        <div class="flex flex-wrap gap-2">
                            @forelse($jenisIzin as $izin)
                                <form method="POST" action="{{ route('admin.jenis_izin.destroy',$izin) }}" onsubmit="return confirm('Hapus kategori {{ $izin->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button class="rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                        {{ $izin->nama }} ×
                                    </button>
                                </form>
                            @empty
                                <span class="text-xs text-slate-400">Belum ada kategori izin.</span>
                            @endforelse
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.jenis_izin.store') }}" class="mt-6 border-t border-slate-100 pt-5">
                        @csrf
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Tambah Kategori Izin Baru</label>
                        <div class="flex gap-2">
                            <input name="nama" required placeholder="Contoh: Sakit, Dinas Luar, Keperluan Keluarga" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 px-3 text-xs outline-none focus:border-slate-500">
                            <button class="rounded-xl bg-slate-900 hover:bg-slate-800 px-4 text-xs font-bold text-white transition-colors">Tambah</button>
                        </div>
                    </form>
                </article>
            </div>
        </main>
    </div>

    <script>
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay'),menuButton=document.getElementById('menuButton');
        menuButton.addEventListener('click',()=>{sidebar.classList.remove('-translate-x-full');overlay.classList.remove('hidden')});
        overlay.addEventListener('click',()=>{sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')});

        const ruleMode = document.getElementById('ruleMode');
        const ruleScheduleFields = document.getElementById('ruleScheduleFields');
        if (ruleMode && ruleScheduleFields) {
            ruleMode.addEventListener('change', function() {
                ruleScheduleFields.style.display = this.value === 'scheduled' ? 'grid' : 'none';
            });
        }
    </script>
</body>
</html>

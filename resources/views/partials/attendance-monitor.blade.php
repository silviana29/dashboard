<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} · {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html,body{overflow-x:hidden}
        dialog{opacity:0;transform:translateY(12px) scale(.98);transition:.18s ease;transition-behavior:allow-discrete}
        dialog[open]{opacity:1;transform:none}
        dialog::backdrop{background:rgba(15,23,42,.62);backdrop-filter:blur(5px)}
        @starting-style{dialog[open]{opacity:0;transform:translateY(12px) scale(.98)}}

        /* Print CSS: Hides navigation header, filter form, export buttons, and dialogs */
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { background: #fff !important; color: #000 !important; }
            header, .no-print, form, dialog { display: none !important; }
            .print-only-header { display: block !important; margin-bottom: 20px; }
            .print-table-container { border: 1px solid #ccc !important; box-shadow: none !important; margin: 0 !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #ddd !important; padding: 8px !important; color: #000 !important; }
            .hidden.md\:block { display: block !important; }
            .md\:hidden { display: none !important; }
        }
        .print-only-header { display: none; }
    </style>
    @include('partials.pwa')
</head>
<body class="min-h-screen bg-[#f8fafc] text-slate-900 antialiased">
@php
    $records = collect();
    foreach ($siswas as $student) {
        if ($student->absensis->isEmpty() && $displayMode === 'daily') $records->push(['student'=>$student,'attendance'=>null]);
        foreach ($student->absensis as $attendance) $records->push(['student'=>$student,'attendance'=>$attendance]);
    }
    $isManagerView = $viewerLabel === 'Atasan';
@endphp

    <!-- Header Navigation (Hidden when printing) -->
    <header class="no-print sticky top-0 z-20 border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 min-w-0">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded bg-slate-900 text-sm font-bold text-white">
                    {{ strtoupper(substr($organization->name,0,1)) }}
                </span>
                <div class="min-w-0">
                    <strong class="block truncate text-sm text-slate-900">{{ $organization->name }}</strong>
                    <span class="text-xs text-slate-500 font-normal">{{ $viewerLabel }} Console</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <!-- Printable Header (Visible only when printed) -->
        <div class="print-only-header">
            <h2 class="text-xl font-bold">{{ $organization->name }}</h2>
            <h3 class="text-base font-semibold">{{ $pageTitle }}</h3>
            <p class="text-xs text-gray-600">Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
            @if(request()->filled('siswa_id') || request()->filled('tanggal') || request()->filled('bulan'))
                <p class="text-xs text-gray-600 font-semibold mt-1">Filter: 
                    {{ request()->filled('siswa_id') ? 'Anggota selected ' : '' }}
                    {{ request()->filled('tanggal') ? 'Tanggal: '.request('tanggal').' ' : '' }}
                    {{ request()->filled('bulan') ? 'Bulan: '.request('bulan').' ' : '' }}
                </p>
            @endif
            <hr class="my-2 border-gray-400">
        </div>

        <!-- Page Title Banner (Hidden when printing) -->
        <div class="no-print flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pemantauan kehadiran</span>
                <h1 class="mt-0.5 text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ $pageTitle }}</h1>
                <p class="mt-1 text-xs text-slate-500 max-w-xl">{{ $scopeDescription }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route($exportRoute, array_merge(request()->query(), ['format' => 'excel'])) }}" class="inline-flex items-center justify-center rounded border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50">
                    Export Excel
                </a>
                <button type="button" onclick="window.print()" class="inline-flex items-center justify-center rounded border border-slate-900 bg-slate-900 px-3 py-2 text-xs font-medium text-white hover:bg-slate-800">
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <!-- Clean Filter Form with Date Range & Active Filter Indicators (Hidden when printing) -->
        <section class="no-print mt-5 rounded border border-slate-200 bg-white p-4">
            <form method="GET" action="{{ route($filterRoute) }}" class="grid gap-3 sm:grid-cols-2 md:grid-cols-5 md:items-end">
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Anggota Tim</label>
                    <select name="siswa_id" class="h-9 w-full rounded border border-slate-300 bg-white px-2.5 text-xs outline-none focus:border-slate-500">
                        <option value="">Semua anggota</option>
                        @foreach($all_siswas as $student)
                            <option value="{{ $student->id }}" @selected((string)$siswaId===(string)$student->id)>{{ $student->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai', $tanggalMulai ?? '') }}" class="h-9 w-full rounded border border-slate-300 px-2.5 text-xs outline-none focus:border-slate-500" placeholder="Tanggal Mulai">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai', $tanggalSelesai ?? '') }}" class="h-9 w-full rounded border border-slate-300 px-2.5 text-xs outline-none focus:border-slate-500" placeholder="Tanggal Selesai">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-slate-600">Atau Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan',$bulan) }}" class="h-9 w-full rounded border border-slate-300 px-2.5 text-xs outline-none focus:border-slate-500">
                </div>
                <div class="flex items-center gap-2">
                    <button class="h-9 flex-1 rounded bg-slate-900 px-3 text-xs font-medium text-white hover:bg-slate-800">Terapkan Filter</button>
                    @if(request()->filled('siswa_id') || request()->filled('tanggal') || request()->filled('bulan') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai'))
                        <a href="{{ route($filterRoute) }}" class="grid h-9 place-items-center rounded border border-slate-300 bg-white px-3 text-xs font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                    @endif
                </div>
            </form>

            @php
                $activeFilterLabels = [];
                if (request()->filled('siswa_id')) {
                    $selectedStudentName = $all_siswas->firstWhere('id', request('siswa_id'))?->nama_lengkap ?? 'Anggota';
                    $activeFilterLabels[] = 'Anggota: ' . $selectedStudentName;
                }
                if (request()->filled('tanggal_mulai') && request()->filled('tanggal_selesai')) {
                    $activeFilterLabels[] = 'Periode: ' . \Carbon\Carbon::parse(request('tanggal_mulai'))->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse(request('tanggal_selesai'))->translatedFormat('d M Y');
                } elseif (request()->filled('tanggal_mulai')) {
                    $activeFilterLabels[] = 'Mulai: ' . \Carbon\Carbon::parse(request('tanggal_mulai'))->translatedFormat('d M Y');
                } elseif (request()->filled('tanggal_selesai')) {
                    $activeFilterLabels[] = 'Sampai: ' . \Carbon\Carbon::parse(request('tanggal_selesai'))->translatedFormat('d M Y');
                } elseif (request()->filled('tanggal')) {
                    $activeFilterLabels[] = 'Tanggal: ' . \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d M Y');
                }
                if (request()->filled('bulan')) {
                    $activeFilterLabels[] = 'Bulan: ' . \Carbon\Carbon::parse(request('bulan'))->translatedFormat('F Y');
                }
            @endphp

            @if(count($activeFilterLabels) > 0)
                <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-2.5 text-xs">
                    <span class="font-medium text-slate-700">Filter Aktif:</span>
                    @foreach($activeFilterLabels as $label)
                        <span class="inline-flex items-center rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-normal text-slate-800">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Data Section -->
        <section class="print-table-container mt-5 overflow-hidden rounded border border-slate-200 bg-white">
            <div class="no-print flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">{{ $displayMode === 'daily' ? 'Kehadiran Harian' : 'Riwayat Kehadiran' }}</h2>
                    <p class="text-xs text-slate-500">Daftar kehadiran anggota tim.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if(count($activeFilterLabels) > 0)
                        <span class="rounded border border-slate-300 bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">Filtered</span>
                    @endif
                    <span class="rounded border border-slate-200 bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ $records->count() }} Data</span>
                </div>
            </div>

            <!-- Traditional Full Desktop View (Hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3.5">Anggota</th>
                            <th class="px-5 py-3.5">Tanggal & Aturan</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Waktu</th>
                            <th class="px-5 py-3.5">Aktivitas / Keterangan</th>
                            <th class="px-5 py-3.5">Bukti & Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($records as $row)
                            @php
                                $student = $row['student'];
                                $a = $row['attendance'];
                                $dateStr = $a ? $a->tanggal->translatedFormat('d M Y') : \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y');
                                $ruleName = $a?->attendance_type_name ?: ($student->attendanceType?->name ?? 'Belum ada aturan');
                                
                                if (!$a) {
                                    $statusLabel = $isManagerView ? 'Belum Masuk' : 'Belum Tercatat';
                                    $statusStyle = 'bg-slate-100 text-slate-600';
                                } elseif ($a->izin) {
                                    $statusLabel = 'Izin · ' . ($a->jenisIzin?->nama ?? 'Lainnya');
                                    $statusStyle = 'bg-amber-50 text-amber-700 border border-amber-200/60';
                                } elseif ($a->report_at) {
                                    $statusLabel = 'Laporan Terkirim';
                                    $statusStyle = 'bg-violet-50 text-violet-700 border border-violet-200/60';
                                } elseif ($a->status_pulang === 'Pulang Cepat') {
                                    $statusLabel = 'Selesai · Pulang Cepat';
                                    $statusStyle = 'bg-rose-50 text-rose-700 border border-rose-200/60';
                                } elseif ($a->status_ketepatan === 'Terlambat') {
                                    $statusLabel = $isManagerView ? 'Masuk · Terlambat' : 'Terlambat';
                                    $statusStyle = 'bg-rose-50 text-rose-700 border border-rose-200/60';
                                } else {
                                    $statusLabel = $isManagerView ? ($a->jam_pulang ? 'Selesai' : 'Masuk') : ($a->status_ketepatan === 'Tidak Berlaku' ? 'Tercatat' : 'Tepat Waktu');
                                    $statusStyle = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors align-top">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-700 shadow-sm">
                                            {{ strtoupper(substr($student->nama_lengkap,0,2)) }}
                                        </span>
                                        <div>
                                            <strong class="block font-semibold text-slate-900 text-sm">{{ $student->nama_lengkap }}</strong>
                                            <span class="text-xs text-slate-400">{{ $student->team?->name ?? $student->asal_pkl }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <strong class="block font-bold text-slate-800">{{ $dateStr }}</strong>
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold text-indigo-700 mt-0.5">{{ $ruleName }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusStyle }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-700 font-medium">
                                    @if($a?->report_at)
                                        <span class="font-bold text-slate-900">{{ $a->report_at->format('H:i') }}</span>
                                        <span class="block text-[11px] text-slate-400">Waktu laporan</span>
                                    @elseif($a?->jam_masuk)
                                        <div>Masuk <strong class="text-slate-900">{{ substr($a->jam_masuk,0,5) }}</strong></div>
                                        <div>Keluar <strong class="text-slate-900">{{ $a->jam_pulang ? substr($a->jam_pulang,0,5) : '—' }}</strong></div>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 max-w-xs text-slate-600">
                                    <p class="line-clamp-2 leading-relaxed">{{ $a?->report_at ? $a->aktivitas_hari_ini : ($a?->izin ? ($a->keterangan_izin ?: 'Tanpa keterangan') : ($a?->aktivitas_hari_ini ?: '—')) }}</p>
                                    @if($a?->keterangan_telat)
                                        <span class="mt-1 block text-[11px] font-semibold text-amber-700">Alasan terlambat: {{ $a->keterangan_telat }}</span>
                                    @endif
                                    @if($a?->keterangan_pulang_cepat)
                                        <span class="mt-1 block rounded border border-rose-200 bg-rose-50 px-2 py-1.5 text-[11px] font-semibold text-rose-700">Alasan pulang cepat: {{ $a->keterangan_pulang_cepat }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($a?->foto_masuk_url)
                                            <button type="button" data-image="{{ $a->foto_masuk_url }}" class="preview-image h-9 w-9 overflow-hidden rounded-lg border border-slate-200">
                                                <img src="{{ $a->foto_masuk_url }}" class="h-full w-full object-cover" alt="Foto">
                                            </button>
                                        @endif
                                        @if($a?->lokasi_masuk_lat)
                                            <a target="_blank" href="https://maps.google.com/?q={{ $a->lokasi_masuk_lat }},{{ $a->lokasi_masuk_long }}" class="rounded-lg bg-cyan-50 border border-cyan-200 px-2.5 py-1.5 text-xs font-bold text-cyan-700 hover:bg-cyan-100">Peta GPS</a>
                                        @endif
                                        @if($a?->file_izin)
                                            <a target="_blank" href="{{ asset('storage/'.$a->file_izin) }}" class="rounded-lg bg-amber-50 border border-amber-200 px-2.5 py-1.5 text-xs font-bold text-amber-700 hover:bg-amber-100">Izin</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center text-slate-400">Tidak ada data kehadiran yang sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Hidden on Desktop) -->
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse($records as $row)
                    @php
                        $student = $row['student'];
                        $a = $row['attendance'];
                        $dateStr = $a ? $a->tanggal->translatedFormat('d M Y') : \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y');
                        $ruleName = $a?->attendance_type_name ?: ($student->attendanceType?->name ?? 'Belum ada aturan');
                        
                        // Status Badge Setup
                        if (!$a) {
                            $statusLabel = $isManagerView ? 'Belum Masuk' : 'Belum Tercatat';
                            $statusStyle = 'bg-slate-100 text-slate-600';
                        } elseif ($a->izin) {
                            $statusLabel = 'Izin · ' . ($a->jenisIzin?->nama ?? 'Lainnya');
                            $statusStyle = 'bg-amber-50 text-amber-700 border border-amber-200/60';
                        } elseif ($a->report_at) {
                            $statusLabel = 'Laporan Terkirim';
                            $statusStyle = 'bg-violet-50 text-violet-700 border border-violet-200/60';
                        } elseif ($a->status_pulang === 'Pulang Cepat') {
                            $statusLabel = 'Selesai · Pulang Cepat';
                            $statusStyle = 'bg-rose-50 text-rose-700 border border-rose-200/60';
                        } elseif ($a->status_ketepatan === 'Terlambat') {
                            $statusLabel = $isManagerView ? 'Masuk · Terlambat' : 'Terlambat';
                            $statusStyle = 'bg-rose-50 text-rose-700 border border-rose-200/60';
                        } else {
                            $statusLabel = $isManagerView ? ($a->jam_pulang ? 'Selesai' : 'Masuk') : ($a->status_ketepatan === 'Tidak Berlaku' ? 'Tercatat' : 'Tepat Waktu');
                            $statusStyle = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60';
                        }

                        $cardDetail = [
                            'studentName' => $student->nama_lengkap,
                            'studentMeta' => ($student->team?->name ?? $student->asal_pkl) . ($student->gedung ? ' · ' . $student->gedung->nama : ''),
                            'dateStr' => $dateStr,
                            'ruleName' => $ruleName,
                            'statusLabel' => $statusLabel,
                            'statusStyle' => $statusStyle,
                            'timeInfo' => $a?->report_at 
                                ? 'Laporan: ' . $a->report_at->format('H:i') 
                                : ($a?->jam_masuk ? 'Masuk ' . substr($a->jam_masuk,0,5) . ($a->jam_pulang ? ' · Pulang ' . substr($a->jam_pulang,0,5) : '') : '—'),
                            'activity' => $a?->report_at ? $a->aktivitas_hari_ini : ($a?->izin ? ($a->keterangan_izin ?: 'Tanpa keterangan izin') : ($a?->aktivitas_hari_ini ?: '—')),
                            'lateNote' => $a?->keterangan_telat,
                            'earlyNote' => $a?->keterangan_pulang_cepat,
                            'photoIn' => $a?->foto_masuk_url,
                            'photoOut' => $a?->foto_keluar_url,
                            'mapUrl' => $a?->lokasi_masuk_lat ? "https://maps.google.com/?q={$a->lokasi_masuk_lat},{$a->lokasi_masuk_long}" : null,
                            'leaveFile' => $a?->file_izin ? asset('storage/'.$a->file_izin) : null,
                        ];
                    @endphp

                    <article data-card-detail='@json($cardDetail)' class="flex flex-col gap-3 p-4 sm:px-5 md:flex-row md:items-center md:justify-between transition-colors hover:bg-slate-50/60">
                        <!-- Member & Date Info -->
                        <div class="flex items-center gap-3 min-w-0 md:w-1/3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-indigo-50 text-xs font-bold text-indigo-700 shadow-sm">
                                {{ strtoupper(substr($student->nama_lengkap,0,2)) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <strong class="block truncate text-sm font-semibold text-slate-900">{{ $student->nama_lengkap }}</strong>
                                <span class="block truncate text-xs text-slate-500">{{ $dateStr }} · <span class="text-indigo-600 font-medium">{{ $ruleName }}</span></span>
                            </div>
                        </div>

                        @if($a?->keterangan_pulang_cepat)
                            <div class="rounded border border-rose-200 bg-rose-50 px-3 py-2 text-xs leading-5 text-rose-700 md:hidden">
                                <strong>Alasan pulang cepat:</strong> {{ $a->keterangan_pulang_cepat }}
                            </div>
                        @endif

                        <!-- Status & Time (Desktop View) -->
                        <div class="hidden md:flex md:items-center md:gap-4 md:w-1/2 text-xs">
                            <div class="w-1/3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <div class="w-2/3 truncate text-slate-600">
                                @if($a?->report_at)
                                    <span class="font-medium text-slate-800">Laporan {{ $a->report_at->format('H:i') }}</span>
                                @elseif($a?->jam_masuk)
                                    <span class="font-medium text-slate-800">Masuk {{ substr($a->jam_masuk,0,5) }}</span>
                                    @if($a->jam_pulang)
                                        <span class="text-slate-400">· Pulang {{ substr($a->jam_pulang,0,5) }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                                @if($a?->aktivitas_hari_ini || $a?->keterangan_izin)
                                    <span class="block text-[11px] text-slate-400 truncate mt-0.5">{{ $a->aktivitas_hari_ini ?: $a->keterangan_izin }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Mobile Status & Action Buttons -->
                        <div class="flex items-center justify-between md:justify-end gap-2 shrink-0 border-t border-slate-100 pt-2.5 md:border-none md:pt-0">
                            <div class="md:hidden">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $statusStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <!-- Proof Thumbnail Quick Actions -->
                                @if($a?->foto_masuk_url)
                                    <button type="button" data-image="{{ $a->foto_masuk_url }}" class="preview-image hidden sm:block h-8 w-8 overflow-hidden rounded-lg border border-slate-200">
                                        <img src="{{ $a->foto_masuk_url }}" class="h-full w-full object-cover" alt="Foto">
                                    </button>
                                @endif

                                <!-- Open Detail Modal Button -->
                                <button type="button" class="open-monitor-detail rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:border-indigo-300 hover:text-indigo-600 transition-colors">
                                    Detail
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-16 text-center text-sm text-slate-400">
                        Tidak ada data kehadiran yang sesuai filter.
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <!-- Monitor Card Detail Dialog Modal -->
    <dialog id="monitorDetailModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-3xl bg-white p-0 shadow-2xl outline-none">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h3 id="modalStudentName" class="font-bold text-slate-900 text-base"></h3>
                <p id="modalStudentMeta" class="text-xs text-slate-500"></p>
            </div>
            <button type="button" onclick="monitorDetailModal.close()" class="text-2xl text-slate-400 hover:text-slate-600 outline-none">×</button>
        </div>

        <div class="space-y-4 px-6 py-5 text-xs">
            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal & Aturan</p>
                    <p id="modalDateRule" class="mt-0.5 font-bold text-slate-800 text-sm"></p>
                </div>
                <div id="modalStatusBadge"></div>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu & Jam Kerja</p>
                <p id="modalTimeInfo" class="mt-0.5 text-sm font-semibold text-slate-800"></p>
            </div>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Aktivitas / Keterangan</p>
                <div id="modalActivity" class="mt-1 rounded-xl bg-slate-50 p-3 text-slate-700 leading-relaxed break-words font-medium"></div>
            </div>

            <div id="modalNotesWrap" class="space-y-1.5 hidden">
                <p id="modalLateNote" class="rounded-lg bg-amber-50 p-2.5 text-amber-800 font-medium hidden"></p>
                <p id="modalEarlyNote" class="rounded-lg bg-rose-50 p-2.5 text-rose-800 font-medium hidden"></p>
            </div>

            <!-- Proof Photos & Map Links -->
            <div class="border-t border-slate-100 pt-3">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Bukti Masuk / Keluar / Lokasi</p>
                <div class="flex flex-wrap items-center gap-3">
                    <div id="modalPhotoInWrap" class="hidden">
                        <button type="button" id="btnPhotoIn" class="preview-image flex items-center gap-2 rounded-xl border border-slate-200 p-1.5 hover:border-indigo-300">
                            <img id="imgPhotoIn" class="h-10 w-10 rounded-lg object-cover" alt="Masuk">
                            <span class="text-[11px] font-semibold text-slate-600 pr-2">Foto Masuk</span>
                        </button>
                    </div>
                    <div id="modalPhotoOutWrap" class="hidden">
                        <button type="button" id="btnPhotoOut" class="preview-image flex items-center gap-2 rounded-xl border border-slate-200 p-1.5 hover:border-indigo-300">
                            <img id="imgPhotoOut" class="h-10 w-10 rounded-lg object-cover" alt="Keluar">
                            <span class="text-[11px] font-semibold text-slate-600 pr-2">Foto Keluar</span>
                        </button>
                    </div>
                    <a id="modalMapLink" target="_blank" class="hidden inline-flex items-center gap-1.5 rounded-xl bg-cyan-50 border border-cyan-200/80 px-3 py-2 font-bold text-cyan-700 hover:bg-cyan-100">
                        📍 Buka Peta GPS
                    </a>
                    <a id="modalLeaveLink" target="_blank" class="hidden inline-flex items-center gap-1.5 rounded-xl bg-amber-50 border border-amber-200/80 px-3 py-2 font-bold text-amber-700 hover:bg-amber-100">
                        📄 Berkas Izin
                    </a>
                </div>
            </div>
        </div>

        <div class="flex justify-end border-t border-slate-100 px-6 py-4 bg-slate-50/50">
            <button type="button" onclick="monitorDetailModal.close()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50">Tutup</button>
        </div>
    </dialog>

    <!-- Image Preview Modal -->
    <dialog id="imagePreview" class="m-auto max-w-3xl rounded-3xl bg-white p-3 shadow-2xl outline-none">
        <button type="button" onclick="imagePreview.close()" class="mb-2 ml-auto grid h-9 w-9 place-items-center rounded-full bg-slate-100 text-lg font-bold text-slate-500 hover:text-slate-800">×</button>
        <img id="previewImage" class="max-h-[80vh] w-full rounded-2xl object-contain" alt="Preview bukti absensi">
    </dialog>

    <script>
        const monitorDetailModal = document.getElementById('monitorDetailModal');
        document.querySelectorAll('.open-monitor-detail').forEach(button => {
            button.addEventListener('click', () => {
                const article = button.closest('[data-card-detail]');
                const data = JSON.parse(article.dataset.cardDetail || '{}');

                document.getElementById('modalStudentName').textContent = data.studentName || '-';
                document.getElementById('modalStudentMeta').textContent = data.studentMeta || '-';
                document.getElementById('modalDateRule').textContent = (data.dateStr || '') + ' (' + (data.ruleName || '') + ')';
                
                document.getElementById('modalStatusBadge').innerHTML = `<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold ${data.statusStyle}">${data.statusLabel}</span>`;
                document.getElementById('modalTimeInfo').textContent = data.timeInfo || '—';
                document.getElementById('modalActivity').textContent = data.activity || '—';

                // Notes
                const notesWrap = document.getElementById('modalNotesWrap');
                const lateNote = document.getElementById('modalLateNote');
                const earlyNote = document.getElementById('modalEarlyNote');
                
                lateNote.classList.toggle('hidden', !data.lateNote);
                if (data.lateNote) lateNote.textContent = 'Alasan terlambat: ' + data.lateNote;
                
                earlyNote.classList.toggle('hidden', !data.earlyNote);
                if (data.earlyNote) earlyNote.textContent = 'Alasan pulang cepat: ' + data.earlyNote;
                
                notesWrap.classList.toggle('hidden', !data.lateNote && !data.earlyNote);

                // Proof elements
                const pInWrap = document.getElementById('modalPhotoInWrap');
                const pOutWrap = document.getElementById('modalPhotoOutWrap');
                const mapLink = document.getElementById('modalMapLink');
                const leaveLink = document.getElementById('modalLeaveLink');

                pInWrap.classList.toggle('hidden', !data.photoIn);
                if (data.photoIn) {
                    document.getElementById('imgPhotoIn').src = data.photoIn;
                    document.getElementById('btnPhotoIn').dataset.image = data.photoIn;
                }

                pOutWrap.classList.toggle('hidden', !data.photoOut);
                if (data.photoOut) {
                    document.getElementById('imgPhotoOut').src = data.photoOut;
                    document.getElementById('btnPhotoOut').dataset.image = data.photoOut;
                }

                mapLink.classList.toggle('hidden', !data.mapUrl);
                if (data.mapUrl) mapLink.href = data.mapUrl;

                leaveLink.classList.toggle('hidden', !data.leaveFile);
                if (data.leaveFile) leaveLink.href = data.leaveFile;

                monitorDetailModal.showModal();
            });
        });

        // Image Preview handler
        document.querySelectorAll('.preview-image').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                previewImage.src = button.dataset.image;
                imagePreview.showModal();
            });
        });
        imagePreview.addEventListener('click', event => { if (event.target === imagePreview) imagePreview.close(); });
    </script>
</body>
</html>

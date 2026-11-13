<!DOCTYPE html>
<html lang="id">

<head>
    <title>Absensi · {{ $organization->name }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- LEAFLET JS & CSS UNTUK PETA INTERAKTIF -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '{{ $organization->primary_color }}',
                        base: '#09090b',
                        surface: '#18181b',
                    },
                    boxShadow: {
                        'glow-brand': '0 0 30px -10px rgba(99, 102, 241, 0.4)',
                        'glow-success': '0 10px 30px -10px rgba(16, 185, 129, 0.4)',
                        'glow-danger': '0 10px 30px -10px rgba(244, 63, 94, 0.4)',
                        'float': '0 -15px 40px -10px rgba(0,0,0,0.5)',
                    }
                }
            }
        }
    </script>
    @include('partials.pwa')
    <style>
        body { background-color: #09090b; color: #f4f4f5; overscroll-behavior-y: none; }
        ::-webkit-scrollbar { display: none; }
        * { -ms-overflow-style: none; scrollbar-width: none; -webkit-tap-highlight-color: transparent; }

        .dark-tiles { filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%); }

        /* Animasi Titik Biru Lokasi */
        .blue-marker {
            width: 18px; height: 18px;
            background-color: #3b82f6;
            border: 3px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.8);
            position: relative;
        }
        .blue-marker::before {
            content: '';
            position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background-color: rgba(59, 130, 246, 0.4);
            border-radius: 50%;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.5); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .leaflet-container { background: #09090b; outline: none; }
    </style>
</head>

<body class="h-[100dvh] w-full overflow-hidden font-sans antialiased selection:bg-indigo-500/20 relative">

    <!-- FULLSCREEN INTERACTIVE MAP BACKGROUND -->
    <div class="absolute inset-0 z-0 bg-base pointer-events-auto">
        <div id="map-bg" class="w-full h-full">
            <div class="h-full w-full flex items-center justify-center">
                <div class="animate-pulse flex flex-col items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-white/10 flex items-center justify-center text-lg">📍</div>
                    <span class="text-[10px] font-bold text-zinc-500 tracking-widest uppercase">Mencari Satelit...</span>
                </div>
            </div>
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-base via-base/70 to-transparent pointer-events-none lg:bg-gradient-to-r lg:from-base lg:via-base/50 lg:to-transparent"></div>
    </div>

    <!-- FLOATING NAVIGATION (Responsive Width) -->
    <nav class="absolute top-4 left-4 right-4 z-40 mx-auto max-w-3xl lg:max-w-none lg:left-6 lg:right-6 pointer-events-auto">
        <div class="flex items-center justify-between rounded-[2rem] bg-white/5 px-5 py-3.5 backdrop-blur-2xl shadow-float">
            <div class="flex items-center gap-3">
                <div class="grid h-9 w-9 place-items-center rounded-[1rem] text-sm font-bold text-white shadow-glow-brand transition-transform active:scale-95" style="background:{{ $organization->primary_color }}">
                    {{ strtoupper(substr($organization->name,0,1)) }}
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm font-extrabold text-white tracking-wide truncate max-w-[150px] sm:max-w-xs">{{ $organization->name }}</h1>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="rounded-xl bg-white/10 px-4 py-2 text-[11px] sm:text-xs font-bold text-zinc-300 transition hover:bg-white/20 hover:text-white active:scale-90">
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <!-- FLOATING TIME & DATE BUBBLE -->
    <div class="absolute top-[5.5rem] left-0 right-0 lg:left-auto lg:right-6 z-30 mx-auto max-w-3xl lg:max-w-none flex justify-center pointer-events-none">
        <div class="rounded-[1.5rem] bg-base/60 backdrop-blur-xl px-4 py-2 shadow-float flex items-center gap-2 pointer-events-auto">
            <span id="clock" class="text-xs sm:text-sm font-extrabold text-indigo-400 tracking-widest tabular-nums">--:--:--</span>
            <span class="w-1 h-1 rounded-full bg-zinc-600"></span>
            <span class="text-[9px] sm:text-[10px] font-bold text-zinc-300 uppercase tracking-widest">{{ now()->translatedFormat('d M Y') }}</span>
        </div>
    </div>

    <!-- RESPONSIVE PANEL MAIN CONTAINER -->
    <!-- Mobile: Bottom Sheet | Desktop (lg): Widget Kompak di Kiri Bawah -->
    <main class="fixed bottom-4 sm:bottom-6 left-0 right-0 lg:left-6 lg:right-auto lg:w-[380px] z-10 flex flex-col justify-end px-3 sm:px-6 lg:px-0 pointer-events-none">

        <div class="w-full max-w-3xl mx-auto lg:mx-0 rounded-[2rem] sm:rounded-[2.5rem] bg-surface/85 backdrop-blur-3xl p-5 sm:p-8 lg:p-6 pointer-events-auto shadow-float max-h-[72dvh] lg:max-h-[80dvh] overflow-y-auto overscroll-contain flex flex-col">

            <!-- Welcome Header -->
            <div class="flex flex-col sm:flex-row lg:flex-col justify-between items-start gap-3 sm:gap-4 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white leading-tight">Halo, {{ $nama }}</h2>
                    <p class="mt-0.5 text-xs sm:text-sm font-medium text-zinc-400">
                        @if($attendanceMode === 'report')
                            {{ $absensi?->report_at ? 'Laporan hari ini sudah terkirim.' : 'Siap mengirim laporan pekerjaan hari ini?' }}
                        @elseif($absensi?->izin && !$absensi?->jam_masuk)
                            Pengajuan izin hari ini sudah tercatat.
                        @elseif($absensi?->jam_masuk && !$absensi?->jam_pulang)
                            Sudah selesai bekerja? Jangan lupa absen pulang.
                        @elseif($absensi?->jam_pulang)
                            Absensi hari ini sudah lengkap. Sampai jumpa besok.
                        @else
                            Siap untuk memulai pekerjaan?
                        @endif
                    </p>
                </div>

                <div class="flex flex-col items-start sm:items-end lg:items-start gap-1.5 sm:gap-2 lg:gap-1.5 w-full">
                    <div class="flex justify-between w-full items-center">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] sm:text-xs font-bold text-white shadow-sm">{{ $attendanceType?->name ?? 'Belum diatur' }}</span>

                        <!-- Status Lokasi -->
                        @if (!$gedung && $attendanceMode !== 'report')
                        <div class="text-right flex flex-col items-end">
                            <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest hidden sm:block lg:hidden mb-0.5">Lokasi Fleksibel</p>
                            <p id="distance" class="text-[11px] sm:text-xs font-bold text-indigo-400">Menunggu GPS...</p>
                        </div>
                        @elseif ($gedung && $attendanceMode !== 'report')
                        <div class="text-right flex flex-col items-end">
                            <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest hidden sm:block lg:hidden mb-0.5">Jarak</p>
                            <p id="distance" class="text-[11px] sm:text-xs font-bold text-indigo-400">Menunggu GPS...</p>
                        </div>
                        @endif
                    </div>

                    @if ($gedung && $attendanceMode !== 'report')
                    <span id="statusGedung" class="mt-1 inline-block rounded-full bg-white/10 px-2 py-0.5 text-[9px] font-bold text-white self-start sm:self-end lg:self-start">-</span>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success') || $errors->any() || session('error'))
            <div class="mb-5 space-y-2.5">
                @if (session('success')) <div class="rounded-[1.25rem] bg-emerald-500/10 p-3.5 text-xs sm:text-sm font-bold text-emerald-400 flex items-center gap-2"><span>✨</span> {{ session('success') }}</div> @endif
                @if ($errors->any())
                <div class="rounded-[1.25rem] bg-rose-500/10 p-3.5 text-xs sm:text-sm font-bold text-rose-400">
                    <div class="flex items-center gap-2 mb-1.5"><span>⚠️</span> Kesalahan:</div>
                    <ul class="list-disc pl-6 text-[11px] opacity-80"> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                </div>
                @endif
                @if (session('error')) <div class="rounded-[1.25rem] bg-rose-500/10 p-3.5 text-xs font-bold text-rose-400 flex items-center gap-2"><span>🚨</span> {{ session('error') }}</div> @endif
            </div>
            @endif

            <!-- DYNAMIC ACTION SECTION -->
            <div class="mb-2">
                @if($attendanceMode === 'report')
                    @if(!$absensi?->report_at)
                        <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Aktivitas Hari Ini</label>
                        <textarea id="laporan_harian" name="aktivitas_hari_ini" form="formLaporan" rows="3" required class="mb-3 w-full resize-none rounded-[1.25rem] bg-white/5 px-4 py-3.5 text-[13px] font-medium text-white placeholder-zinc-600 outline-none focus:bg-white/10 transition-colors" placeholder="Ketik laporan kerjamu..."></textarea>
                        <button id="btnLaporHarian" disabled class="w-full rounded-[1.25rem] bg-indigo-600 py-3.5 text-[13px] font-extrabold text-white shadow-glow-brand transition-all active:scale-95 disabled:bg-white/5 disabled:text-zinc-600 disabled:shadow-none">Kirim Foto & Laporan</button>
                    @endif
                @else

                    @if(!$absensi || (!$absensi->jam_masuk && !$absensi->izin))
                        <!-- STATE 1: BELUM MASUK -->
                        @if($modeAbsensi === 'regulated')
                        <div id="lateReasonGroup" class="mb-3 hidden">
                            <label class="block text-[10px] font-bold text-rose-400 uppercase tracking-widest mb-2">Alasan Terlambat</label>
                            <textarea id="keterangan_telat" name="keterangan_telat" form="formMasuk" rows="2" disabled class="w-full resize-none rounded-[1.25rem] bg-rose-500/10 px-4 py-3.5 text-[13px] font-medium text-white outline-none focus:bg-rose-500/20"></textarea>
                        </div>
                        @endif

                        <div class="grid grid-cols-2 gap-2.5 sm:gap-3">
                            <button type="button" id="btnAbsenMasuk" class="col-span-2 w-full rounded-[1.25rem] bg-emerald-500 py-3.5 text-[13px] font-extrabold text-white shadow-glow-success transition-all active:scale-[0.98] disabled:bg-white/5 disabled:text-zinc-600 disabled:shadow-none">
                                Absen Masuk
                            </button>
                            <button type="button" onclick="showIzinModal()" class="col-span-2 w-full rounded-[1.25rem] bg-white/5 py-3.5 text-[13px] font-extrabold text-zinc-300 transition-all active:scale-[0.98] hover:bg-white/10">
                                Ajukan Izin
                            </button>
                        </div>

                    @elseif($absensi && $absensi->jam_masuk && !$absensi->jam_pulang)
                        <!-- STATE 2: SUDAH MASUK, BELUM KELUAR -->
                        <div class="mb-3">
                            <label class="block text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Catatan Pulang (Opsional)</label>
                            <textarea id="aktivitas_hari_ini" name="aktivitas_hari_ini" form="formKeluar" rows="2" class="w-full resize-none rounded-[1.25rem] bg-white/5 px-4 py-3.5 text-[13px] font-medium text-white placeholder-zinc-600 outline-none focus:bg-white/10 transition-colors" placeholder="Ringkasan tugas..."></textarea>
                        </div>

                        @if($modeAbsensi === 'regulated')
                        <div id="earlyReasonGroup" class="mb-3 hidden">
                            <label class="block text-[10px] font-bold text-amber-400 uppercase tracking-widest mb-2">Alasan Pulang Cepat</label>
                            <textarea id="keterangan_pulang_cepat" name="keterangan_pulang_cepat" form="formKeluar" rows="2" disabled class="w-full resize-none rounded-[1.25rem] bg-amber-500/10 px-4 py-3.5 text-[13px] font-medium text-white outline-none focus:bg-amber-500/20"></textarea>
                        </div>
                        @endif

                        <button type="button" id="btnAbsenKeluar" class="w-full rounded-[1.25rem] bg-rose-500 py-3.5 text-[13px] font-extrabold text-white shadow-glow-danger transition-all active:scale-[0.98] disabled:bg-white/5 disabled:text-zinc-600 disabled:shadow-none">
                            Absen Keluar
                        </button>
                    @endif
                @endif
            </div>

            <!-- CONDITIONAL TODAY'S LOG -->
            @if ($absensi)
            <div class="mt-4 rounded-[1.5rem] bg-white/5 p-4">
                <h3 class="text-[11px] font-extrabold text-white mb-3 uppercase tracking-widest">Log Hari Ini</h3>

                @if ($absensi->report_at)
                    <div class="flex gap-3 items-start">
                        <img src="{{ $absensi->foto_masuk_url }}" alt="Laporan" class="h-14 w-14 object-cover rounded-xl cursor-pointer bg-black/50 shrink-0" onclick="showImageModal('{{ $absensi->foto_masuk_url }}')">
                        <div class="flex-1 min-w-0">
                            <span class="inline-block bg-indigo-500/20 text-indigo-400 px-2.5 py-0.5 rounded-lg text-[9px] font-bold uppercase mb-1.5 truncate max-w-full">Terkirim {{ $absensi->report_at->format('H:i') }}</span>
                            <p class="text-[11px] text-zinc-300 font-medium whitespace-pre-line leading-relaxed">{{ $absensi->aktivitas_hari_ini }}</p>
                        </div>
                    </div>
                @elseif ($absensi->izin && !$absensi->jam_masuk)
                    <div class="flex flex-col items-start gap-1.5">
                        <span class="bg-amber-500/20 text-amber-400 px-2.5 py-1 rounded-lg text-[10px] font-bold">{{ $absensi->jenisIzin->nama ?? 'Izin' }}</span>
                        <p class="text-[11px] text-zinc-300 font-medium">{{ $absensi->keterangan_izin ?: 'Tanpa keterangan' }}</p>
                        @if ($absensi->file_izin)
                        <a href="{{ asset('storage/' . $absensi->file_izin) }}" target="_blank" class="mt-1 bg-white/10 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold active:scale-95">Lihat Berkas Izin</a>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col gap-2.5">
                        <div class="flex-1 bg-black/20 p-3 rounded-2xl flex flex-row items-center justify-between">
                            <div>
                                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-0.5">Masuk</p>
                                <p class="text-sm font-extrabold text-white">{{ $absensi->jam_masuk ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i') : '--:--' }}</p>
                                @if ($absensi->status_ketepatan == 'Terlambat')
                                <span class="inline-block mt-0.5 bg-rose-500/20 text-rose-400 px-1.5 py-0.5 rounded text-[8px] font-bold">Telat</span>
                                @else
                                <span class="inline-block mt-0.5 bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded text-[8px] font-bold">Tepat</span>
                                @endif
                            </div>
                            @if($absensi->foto_masuk_url)
                            <img src="{{ $absensi->foto_masuk_url }}" class="h-10 w-10 object-cover rounded-[0.85rem] cursor-pointer bg-black shrink-0" onclick="showImageModal('{{ $absensi->foto_masuk_url }}')">
                            @endif
                        </div>

                        @if($absensi->jam_pulang)
                        <div class="flex-1 bg-black/20 p-3 rounded-2xl flex flex-row items-center justify-between">
                            <div>
                                <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest mb-0.5">Keluar</p>
                                <p class="text-sm font-extrabold text-white">{{ \Carbon\Carbon::parse($absensi->jam_pulang)->format('H:i') }}</p>

                                @php
                                    $durasiHariIni = $absensi->durasi_kerja;
                                    if (!$durasiHariIni && $absensi->jam_masuk && $absensi->jam_pulang) {
                                        $m = \Carbon\Carbon::parse($absensi->jam_masuk);
                                        $p = \Carbon\Carbon::parse($absensi->jam_pulang);
                                        $durasiHariIni = $m->diff($p)->format('%hj %im');
                                    } elseif ($durasiHariIni) {
                                        $durasiHariIni = substr($durasiHariIni, 0, 5);
                                    }
                                @endphp
                                @if($durasiHariIni)
                                <span class="inline-block mt-0.5 bg-indigo-500/20 text-indigo-400 px-1.5 py-0.5 rounded text-[8px] font-bold">{{ $durasiHariIni }}</span>
                                @endif
                            </div>
                            @if($absensi->foto_keluar_url)
                            <img src="{{ $absensi->foto_keluar_url }}" class="h-10 w-10 object-cover rounded-[0.85rem] cursor-pointer bg-black shrink-0" onclick="showImageModal('{{ $absensi->foto_keluar_url }}')">
                            @endif
                        </div>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            <button id="btnBukaRiwayat" class="mt-auto pt-4 w-full rounded-[1.25rem] bg-transparent py-3 text-[11px] font-extrabold text-zinc-400 active:scale-95 transition-all hover:text-white">
                Lihat Riwayat Bulanan
            </button>
        </div>

        <form method="POST" action="{{ route('siswa.absen.masuk',[], true )}}" id="formMasuk" class="hidden">@csrf <input type="hidden" name="latitude" id="lat"> <input type="hidden" name="longitude" id="long"> <input type="hidden" name="foto" id="fotoMasuk"></form>
        <form method="POST" action="{{ route('siswa.absen.keluar',[], true)}}" id="formKeluar" class="hidden">@csrf <input type="hidden" name="foto" id="fotoKeluar"></form>
        <form method="POST" action="{{ route('siswa.lapor-harian',[], true) }}" id="formLaporan" class="hidden">@csrf <input type="hidden" name="foto" id="fotoLaporan"> <input type="hidden" name="latitude" id="reportLat" required> <input type="hidden" name="longitude" id="reportLong" required></form>
    </main>

    <!-- Kamera Modal -->
    <div id="cameraModal" class="hidden fixed inset-0 z-50 flex flex-col justify-center sm:justify-end bg-base/90 p-4 backdrop-blur-xl">
        <div class="w-full max-w-md mx-auto bg-surface rounded-[2rem] p-4 shadow-float mb-0 sm:mb-6">
            <video id="videoPreview" autoplay muted playsinline class="w-full h-[50dvh] object-cover rounded-[1.5rem] bg-black transform -scale-x-100 mb-4"></video>
            <canvas id="canvas" class="hidden"></canvas>
            <div class="flex flex-col gap-2.5">
                <button id="captureBtn" class="w-full rounded-[1.25rem] bg-indigo-600 py-3.5 text-sm font-extrabold text-white active:scale-95 shadow-glow-brand">📸 Ambil Foto</button>
                <div class="flex gap-2.5">
                    <button id="switchCameraBtn" class="flex-1 rounded-[1.25rem] bg-white/10 py-3.5 text-xs font-bold text-white active:scale-95">🔄 Putar</button>
                    <button id="closeCameraBtn" class="flex-1 rounded-[1.25rem] bg-rose-500/20 py-3.5 text-xs font-bold text-rose-400 active:scale-95">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="hidden fixed inset-0 z-50 flex justify-center items-end sm:items-center bg-base/90 p-0 sm:p-4 backdrop-blur-xl">
        <div class="w-full max-w-3xl h-[85dvh] flex flex-col bg-surface rounded-t-[2rem] sm:rounded-[2.5rem] shadow-float">
            <div class="flex justify-between items-center p-5 sm:p-6 pb-3">
                <h4 class="text-lg font-extrabold text-white">Riwayat Bulanan</h4>
                <button id="closeHistoryModal" class="rounded-full bg-white/10 h-8 w-8 flex items-center justify-center font-bold text-white active:scale-90">&times;</button>
            </div>
            <div class="px-5 sm:px-6 pb-3 flex gap-3">
                <select id="selectBulan" class="flex-1 rounded-[1rem] bg-white/5 px-3 py-2.5 text-xs font-bold text-white outline-none focus:bg-white/10 appearance-none"></select>
                <select id="selectTahun" class="flex-1 rounded-[1rem] bg-white/5 px-3 py-2.5 text-xs font-bold text-white outline-none focus:bg-white/10 appearance-none"></select>
            </div>
            <div class="flex-1 overflow-y-auto p-5 sm:p-6 pt-0">
                <div id="riwayatList" class="space-y-2.5">
                    <div class="bg-white/5 py-10 rounded-[1.5rem] text-center text-xs font-bold text-zinc-500">Memuat...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-base/95 p-6 backdrop-blur-xl" onclick="closeImageModal()">
        <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-[85dvh] rounded-[1.5rem] object-contain shadow-float">
    </div>

    <!-- Izin Modal -->
    <div id="izinModal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-base/90 p-0 sm:p-4 backdrop-blur-xl">
        <form method="POST" action="{{ route('siswa.absen.izin') }}" enctype="multipart/form-data" onsubmit="return confirm('Kirim pengajuan izin?')" class="w-full max-w-md bg-surface rounded-t-[2rem] sm:rounded-[2.5rem] shadow-float flex flex-col h-[90dvh] sm:h-auto">
            @csrf
            <div class="p-6 pb-3">
                <h3 class="text-xl font-extrabold text-white">Form Izin</h3>
            </div>
            <div class="p-6 pt-0 space-y-3 overflow-y-auto flex-1">
                <select name="jenis_izin_id" required class="w-full rounded-[1.25rem] bg-white/5 px-4 py-3.5 text-[13px] font-bold text-white outline-none appearance-none">
                    @if(isset($jenisIzin))
                        @foreach($jenisIzin as $izin)
                        <option value="{{ $izin->id }}" class="bg-surface">{{ $izin->nama }}</option>
                        @endforeach
                    @endif
                </select>
                <textarea name="keperluan" rows="3" placeholder="Keterangan lengkap..." class="w-full resize-none rounded-[1.25rem] bg-white/5 px-4 py-3.5 text-[13px] font-medium text-white placeholder-zinc-500 outline-none"></textarea>

                @if($absensi?->jam_masuk)
                <div class="bg-amber-500/10 p-4 rounded-[1.25rem]">
                    <p class="text-[10px] font-bold text-amber-400 mb-1.5 uppercase tracking-widest">Izin di Tengah Waktu</p>
                    <input type="time" name="izin_dari_jam" value="{{ old('izin_dari_jam', now()->format('H:i')) }}" required class="w-full rounded-xl bg-amber-500/10 px-3 py-2.5 text-[13px] font-bold text-amber-100 outline-none">
                </div>
                @endif

                <div class="bg-white/5 p-4 rounded-[1.25rem]">
                    <p class="text-[10px] font-bold text-zinc-400 mb-1.5 uppercase tracking-widest">Berkas (Opsional)</p>
                    <input type="file" name="file_izin" class="w-full text-[11px] font-bold text-zinc-400 file:mr-3 file:rounded-xl file:bg-white/10 file:border-0 file:px-3 file:py-1.5 file:text-white">
                </div>
            <div class="p-5 flex gap-2.5 mt-auto">
                <button type="button" onclick="closeModal('izinModal')" class="flex-1 rounded-[1.25rem] bg-white/10 py-3.5 text-[13px] font-bold text-white active:scale-95">Batal</button>
                <button type="submit" class="flex-1 rounded-[1.25rem] bg-indigo-600 py-3.5 text-[13px] font-extrabold text-white active:scale-95 shadow-glow-brand">Kirim</button>
            </div>
        </form>
    </div>

    <!-- Early Checkout Warning Modal -->
    <div id="locationRequiredModal" class="hidden fixed inset-0 z-[70] flex items-center justify-center bg-base/90 p-4 backdrop-blur-xl">
        <div class="w-full max-w-sm rounded-[2rem] border border-white/10 bg-surface p-6 text-white shadow-float">
            <div class="flex items-start gap-3">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-indigo-500/15 text-indigo-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                </div>
                <div><h3 class="text-base font-extrabold">Aktifkan lokasi</h3><p id="locationModalMessage" class="mt-1 text-xs leading-5 text-zinc-400">MWork memerlukan lokasi aktif sebelum absensi dapat dilanjutkan.</p></div>
            </div>
            <div class="mt-5 rounded-xl border border-white/5 bg-white/5 p-4 text-xs leading-5 text-zinc-300">
                <p>1. Nyalakan GPS atau Layanan Lokasi perangkat.</p>
                <p class="mt-1">2. Izinkan MWork mengakses lokasi.</p>
                <p class="mt-1">3. Tetap di halaman ini. Lokasi akan terdeteksi otomatis.</p>
            </div>
            <div id="locationWaitingStatus" class="mt-4 flex items-center gap-2 text-xs font-bold text-indigo-400"><span class="h-2 w-2 animate-pulse rounded-full bg-indigo-400"></span><span>Menunggu lokasi aktif...</span></div>
            <button type="button" id="retryLocationBtn" class="mt-5 w-full rounded-xl bg-indigo-600 py-3 text-xs font-extrabold text-white active:scale-95">Coba deteksi sekarang</button>
        </div>
    </div>

    <!-- Early Checkout Warning Modal -->
    <div id="earlyCheckoutWarningModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-base/90 p-4 backdrop-blur-xl">
        <div class="w-full max-w-md bg-surface rounded-[2rem] border border-white/10 p-6 shadow-float text-white">
            <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-amber-500/20 text-amber-400 font-bold text-lg">!</div>
                <div>
                    <h3 class="font-extrabold text-base text-white">Peringatan Jam Pulang</h3>
                    <p class="text-xs text-zinc-400">Anda melakukan absen sebelum jam jadwal pulang.</p>
                </div>
            </div>

            <div class="py-4 space-y-3 text-xs leading-relaxed text-zinc-300">
                <div class="rounded-xl bg-white/5 p-3.5 border border-white/5 space-y-1.5">
                    <p>Jadwal Jam Pulang: <strong id="warnJamPulangText" class="text-white">--:--</strong></p>
                    <p>Sisa Waktu Menuju Pulang: <strong id="warnSisaWaktuText" class="text-amber-400 font-extrabold">--</strong></p>
                </div>
                <p class="font-semibold text-zinc-200">Apakah Anda yakin ingin melakukan absen pulang sekarang?</p>
            </div>

            <div class="flex gap-2.5 pt-2">
                <button type="button" onclick="closeModal('earlyCheckoutWarningModal')" class="flex-1 rounded-[1.25rem] bg-white/10 py-3 text-xs font-bold text-zinc-300 hover:bg-white/15 active:scale-95 transition-all">
                    Batal
                </button>
                <button type="button" id="confirmEarlyCheckoutBtn" class="flex-1 rounded-[1.25rem] bg-rose-500 py-3 text-xs font-extrabold text-white hover:bg-rose-600 active:scale-95 transition-all shadow-glow-danger">
                    Ya, Absen Pulang
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function showImageModal(src) { document.getElementById('modalImage').src = src; document.getElementById('imageModal').classList.remove('hidden'); }
        function closeImageModal() { document.getElementById('modalImage').src = ''; document.getElementById('imageModal').classList.add('hidden'); }
        function showIzinModal() { document.getElementById('izinModal').classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        window.toggleRiwayatDetail = function(id) {
            const detailEl = document.getElementById(id);
            const iconEl = document.getElementById(id.replace('detail-', 'icon-'));
            if (detailEl.classList.contains('hidden')) {
                detailEl.classList.remove('hidden');
                if (iconEl) iconEl.classList.add('rotate-180');
            } else {
                detailEl.classList.add('hidden');
                if (iconEl) iconEl.classList.remove('rotate-180');
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const clockEl = document.getElementById('clock');
            const distanceEl = document.getElementById('distance');
            const statusGedungEl = document.getElementById('statusGedung');
            const cameraModal = document.getElementById('cameraModal');
            const video = document.getElementById('videoPreview');
            const canvas = document.getElementById('canvas');

            let stream; let currentFacingMode = 'user'; let currentForm = null;
            let leafletMap = null;
            let locationMarker = null;

            const gedLat = parseFloat("{{ $gedung->latitude ?? 0 }}");
            const gedLong = parseFloat("{{ $gedung->longitude ?? 0 }}");
            const gedRadius = parseFloat("{{ $gedung->radius_meter ?? 10 }}");

            setInterval(() => {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false});
            }, 1000);

            let isMapInitialized = false;
            let hasLocation = false;
            let locationWatchId = null;
            let locationRetryTimer = null;
            let pendingLocationAction = null;
            let lastLocationAt = 0;
            const locationModal = document.getElementById('locationRequiredModal');
            const locationModalMessage = document.getElementById('locationModalMessage');
            const locationWaitingStatus = document.getElementById('locationWaitingStatus');

            function showLocationModal(message) {
                locationModalMessage.textContent = message || 'MWork memerlukan lokasi aktif sebelum absensi dapat dilanjutkan.';
                locationWaitingStatus.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-indigo-400"></span><span>Menunggu lokasi aktif...</span>';
                locationModal.classList.remove('hidden');
            }

            function scheduleLocationRetry() {
                window.clearTimeout(locationRetryTimer);
                locationRetryTimer = window.setTimeout(startLocationTracking, 3000);
            }

            function handleLocationSuccess(pos) {
                hasLocation = true;
                lastLocationAt = Date.now();
                window.clearTimeout(locationRetryTimer);
                const lat = pos.coords.latitude;
                const long = pos.coords.longitude;
                if(document.getElementById('lat')) { document.getElementById('lat').value = lat; document.getElementById('long').value = long; }
                if(document.getElementById('reportLat')) { document.getElementById('reportLat').value = lat; document.getElementById('reportLong').value = long; }

                try {
                    if(!isMapInitialized && window.L) {
                        document.getElementById('map-bg').innerHTML = '';
                        leafletMap = L.map('map-bg', { zoomControl: false, attributionControl: false }).setView([lat, long], 18);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, className: 'dark-tiles' }).addTo(leafletMap);
                        const blueIcon = L.divIcon({ className: 'custom-div-icon', html: "<div class='blue-marker'></div>", iconSize: [18, 18], iconAnchor: [9, 9] });
                        locationMarker = L.marker([lat, long], {icon: blueIcon}).addTo(leafletMap);
                        isMapInitialized = true;
                    } else if (locationMarker) {
                        locationMarker.setLatLng([lat, long]);
                    }
                } catch (mapError) {
                    console.warn('Peta tidak dapat dimuat, tetapi koordinat absensi tetap tersedia.', mapError);
                }

                if(distanceEl) distanceEl.textContent = `${lat.toFixed(5)}, ${long.toFixed(5)}`;
                if(statusGedungEl && !gedLat) statusGedungEl.textContent = 'Lokasi Ditemukan';
                if(document.getElementById('btnLaporHarian')) document.getElementById('btnLaporHarian').disabled = false;

                if (gedLat && gedLong && gedRadius) {
                    const R = 6371e3; const dLat = (gedLat - lat) * Math.PI / 180; const dLon = (gedLong - long) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(lat * Math.PI / 180) * Math.cos(gedLat * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
                    const jarak = R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    if(distanceEl) distanceEl.textContent = jarak.toFixed(0) + ' m';
                    if(statusGedungEl) {
                        const inside = jarak <= gedRadius;
                        statusGedungEl.textContent = inside ? 'Dalam Area' : 'Luar Area';
                        statusGedungEl.className = `mt-1 inline-block rounded-full ${inside ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'} px-2 py-0.5 text-[9px] lg:text-[10px] font-bold self-start sm:self-end lg:self-start`;
                    }
                }

                if (!locationModal.classList.contains('hidden')) {
                    locationWaitingStatus.innerHTML = '<span class="h-2 w-2 rounded-full bg-emerald-400"></span><span class="text-emerald-400">Lokasi ditemukan. Melanjutkan...</span>';
                    window.setTimeout(() => locationModal.classList.add('hidden'), 500);
                }
                if (pendingLocationAction) {
                    const action = pendingLocationAction;
                    pendingLocationAction = null;
                    window.setTimeout(action, 550);
                }
            }

            function handleLocationError(err) {
                const denied = err.code === 1;
                const hasRecentLocation = hasLocation && (Date.now() - lastLocationAt < 5 * 60 * 1000);
                if (hasRecentLocation && !denied) {
                    scheduleLocationRetry();
                    return;
                }
                hasLocation = false;
                const message = denied
                    ? 'Akses lokasi belum diizinkan. Izinkan lokasi untuk MWork melalui pengaturan aplikasi atau browser.'
                    : 'Lokasi perangkat belum aktif atau sinyal GPS belum ditemukan. Nyalakan lokasi dan tetap di halaman ini.';
                showLocationModal(message);
                if(distanceEl) distanceEl.textContent = denied ? 'Izin lokasi diperlukan' : 'Menunggu GPS...';
                if(statusGedungEl) { statusGedungEl.textContent = 'Menunggu lokasi'; statusGedungEl.className = 'mt-1 inline-block rounded-full bg-amber-500/20 px-2 py-0.5 text-[9px] font-bold text-amber-400 self-start sm:self-end lg:self-start'; }
                scheduleLocationRetry();
            }

            function startLocationTracking() {
                if (!navigator.geolocation) {
                    showLocationModal('Perangkat ini tidak mendukung deteksi lokasi. Gunakan perangkat atau browser yang memiliki GPS.');
                    return;
                }
                if (locationWatchId !== null) navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = navigator.geolocation.watchPosition(handleLocationSuccess, handleLocationError, { enableHighAccuracy: true, timeout: 7000, maximumAge: 0 });
            }

            function runWhenLocationReady(action) {
                if (hasLocation && (Date.now() - lastLocationAt < 5 * 60 * 1000)) return action();
                pendingLocationAction = action;
                showLocationModal('Lokasi harus aktif sebelum Anda dapat melanjutkan absensi.');
                startLocationTracking();
            }

            document.getElementById('retryLocationBtn').addEventListener('click', startLocationTracking);
            window.addEventListener('focus', () => { if (!hasLocation) startLocationTracking(); });
            document.addEventListener('visibilitychange', () => { if (!document.hidden && !hasLocation) startLocationTracking(); });
            if (navigator.permissions?.query) {
                navigator.permissions.query({name:'geolocation'}).then(permission => {
                    permission.addEventListener('change', startLocationTracking);
                }).catch(() => {});
            }
            startLocationTracking();

            async function startCam() {
                try {
                    if (stream) stream.getTracks().forEach(t => t.stop());
                    stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: currentFacingMode } } });
                    video.srcObject = stream; video.classList.toggle('-scale-x-100', currentFacingMode === 'user');
                    cameraModal.classList.remove('hidden');
                } catch (e) { alert('Kamera gagal: ' + e.message); }
            }
            function stopCam() { if(stream) stream.getTracks().forEach(t => t.stop()); cameraModal.classList.add('hidden'); }

            document.getElementById('closeCameraBtn').addEventListener('click', stopCam);
            document.getElementById('switchCameraBtn').addEventListener('click', () => { currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user'; startCam(); });

            const jamTelat = "{{ substr($jamTelat ?? '08:00', 0, 5) }}";
            const jamPulangMinimal = "{{ substr($jamPulangMinimal ?? '17:00', 0, 5) }}";
            const isRegulatedAttendance = @json($modeAbsensi === 'regulated');

            function currentTimeValue() {
                const now = new Date();
                return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            }

            function syncTimeReasonFields() {
                const currentTime = currentTimeValue();
                const lateGroup = document.getElementById('lateReasonGroup');
                const lateReason = document.getElementById('keterangan_telat');
                const earlyGroup = document.getElementById('earlyReasonGroup');
                const earlyReason = document.getElementById('keterangan_pulang_cepat');

                if (lateGroup && lateReason) {
                    const isLate = currentTime > jamTelat;
                    lateGroup.classList.toggle('hidden', !isLate);
                    lateReason.disabled = !isLate;
                    lateReason.required = isLate;
                }
                if (earlyGroup && earlyReason) {
                    const isEarly = currentTime < jamPulangMinimal;
                    earlyGroup.classList.toggle('hidden', !isEarly);
                    earlyReason.disabled = !isEarly;
                    earlyReason.required = isEarly;
                }
            }
            syncTimeReasonFields();
            window.setInterval(syncTimeReasonFields, 30000);

            function triggerStartCamForForm(form) {
                runWhenLocationReady(() => {
                    if (form.reportValidity()) {
                        currentForm = form;
                        startCam();
                    }
                });
            }

            ['btnAbsenMasuk', 'btnAbsenKeluar', 'btnLaporHarian'].forEach(id => {
                const btn = document.getElementById(id);
                if(btn) btn.addEventListener('click', () => {
                    const formId = id === 'btnAbsenMasuk' ? 'formMasuk' : (id === 'btnAbsenKeluar' ? 'formKeluar' : 'formLaporan');
                    const form = document.getElementById(formId);

                    if (id === 'btnAbsenKeluar' && isRegulatedAttendance && currentTimeValue() < jamPulangMinimal) {
                        const reason = document.getElementById('keterangan_pulang_cepat');
                        if (reason && !reason.value.trim()) {
                            reason.setCustomValidity('Alasan pulang cepat wajib diisi.');
                            reason.reportValidity();
                            reason.focus();
                            reason.addEventListener('input', () => reason.setCustomValidity(''), {once:true});
                            return;
                        }
                    }

                    triggerStartCamForForm(form);
                });
            });

            document.getElementById('captureBtn').addEventListener('click', function() {
                if(!currentForm) return; this.disabled = true;
                const scale = Math.min(1, 800 / Math.max(video.videoWidth, video.videoHeight));
                canvas.width = video.videoWidth * scale; canvas.height = video.videoHeight * scale;
                const ctx = canvas.getContext('2d');
                if (currentFacingMode === 'user') { ctx.translate(canvas.width, 0); ctx.scale(-1, 1); }
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const b64 = canvas.toDataURL('image/jpeg', 0.6);
                if(currentForm.id === 'formMasuk') document.getElementById('fotoMasuk').value = b64;
                else if(currentForm.id === 'formKeluar') document.getElementById('fotoKeluar').value = b64;
                else document.getElementById('fotoLaporan').value = b64;

                stopCam(); currentForm.submit();
            });

            const selBln = document.getElementById('selectBulan'); const selThn = document.getElementById('selectTahun');
            const bln = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const now = new Date();
            bln.forEach((n, i) => selBln.add(new Option(n, i+1, false, i === now.getMonth())));
            for(let i = now.getFullYear(); i >= now.getFullYear()-2; i--) selThn.add(new Option(i, i));

            async function fetchR() {
                const list = document.getElementById('riwayatList');
                list.innerHTML = `<div class="bg-white/5 py-10 rounded-[1.5rem] text-center text-xs font-bold text-zinc-500 animate-pulse">Memuat data...</div>`;
                try {
                    const res = await fetch(`${window.location.origin}/siswa/riwayat?bulan=${selBln.value}&tahun=${selThn.value}`, {headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'}});
                    const data = await res.json(); list.innerHTML = '';
                    if(data.length > 0) {
                        data.forEach((d, index) => {
                            const uniqueId = `riwayat-${index}`;
                            let stat = 'Tercatat', cls = 'bg-white/10 text-white';
                            if(d.report_at) { stat = 'Laporan'; cls = 'bg-indigo-500/20 text-indigo-400'; }
                            else if(d.jam_masuk && d.status_ketepatan === 'Terlambat') { stat = 'Telat'; cls = 'bg-rose-500/20 text-rose-400'; }
                            else if(d.izin == 1) { stat = 'Izin'; cls = 'bg-amber-500/20 text-amber-400'; }
                            else if(d.jam_masuk) { stat = 'Tepat'; cls = 'bg-emerald-500/20 text-emerald-400'; }

                            let computedDurasi = d.durasi_kerja;
                            if (!computedDurasi && d.jam_masuk && d.jam_pulang) {
                                const m = new Date(`1970-01-01T${d.jam_masuk}`);
                                const p = new Date(`1970-01-01T${d.jam_pulang}`);
                                let diffMin = Math.floor((p - m) / 60000);
                                if (diffMin > 0) {
                                    computedDurasi = `${Math.floor(diffMin / 60)}j ${diffMin % 60}m`;
                                } else {
                                    computedDurasi = '-';
                                }
                            } else if (computedDurasi) {
                                computedDurasi = computedDurasi.substring(0, 5);
                            } else {
                                computedDurasi = '-';
                            }

                            const ket = String(d.aktivitas_hari_ini || d.keterangan_izin || '').replace(/[<>]/g, '');

                            list.insertAdjacentHTML('beforeend', `
                                <div class="bg-white/5 rounded-[1.25rem] transition-all overflow-hidden">
                                    <div class="p-4 flex flex-row gap-3 justify-between items-center cursor-pointer active:bg-white/10 transition-colors" onclick="toggleRiwayatDetail('detail-${uniqueId}')">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <span class="text-xs font-extrabold text-white">${new Date(d.tanggal).toLocaleDateString('id-ID', {day:'2-digit', month:'short'})}</span>
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${cls}">${stat}</span>
                                            </div>
                                            <p class="text-[10px] font-bold text-zinc-400 truncate">${d.report_at ? d.report_at.substring(11,16) : (d.jam_masuk ? d.jam_masuk.substring(0,5) : '-')} ➔ ${d.jam_pulang ? d.jam_pulang.substring(0,5) : '-'}</p>
                                        </div>
                                        <div class="flex items-center gap-2.5 shrink-0">
                                            <div class="flex gap-1.5">
                                                ${d.foto_masuk_url ? `<img src="${d.foto_masuk_url}" class="h-9 w-9 rounded-[0.75rem] object-cover bg-black" onclick="event.stopPropagation(); showImageModal('${d.foto_masuk_url}')">` : ''}
                                                ${d.foto_keluar_url ? `<img src="${d.foto_keluar_url}" class="h-9 w-9 rounded-[0.75rem] object-cover bg-black" onclick="event.stopPropagation(); showImageModal('${d.foto_keluar_url}')">` : ''}
                                            </div>
                                            <div class="text-zinc-500 transition-transform duration-300" id="icon-${uniqueId}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="detail-${uniqueId}" class="hidden px-4 pb-4 pt-1">
                                        <div class="bg-black/20 rounded-xl p-3.5">
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest">Catatan / Aktivitas</span>
                                                <span class="text-[10px] font-bold text-indigo-400">Durasi: ${computedDurasi}</span>
                                            </div>
                                            <p class="text-[11px] text-zinc-300 font-medium whitespace-pre-line leading-relaxed">${ket ? ket : 'Tidak ada catatan untuk hari ini.'}</p>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else list.innerHTML = `<div class="bg-white/5 py-10 rounded-[1.5rem] text-center text-xs font-bold text-zinc-500">Belum ada riwayat</div>`;
                } catch(e) { list.innerHTML = `<div class="bg-rose-500/10 py-10 rounded-[1.5rem] text-center text-xs font-bold text-rose-500">Error memuat data</div>`; }
            }

            document.getElementById('btnBukaRiwayat').addEventListener('click', () => { document.getElementById('historyModal').classList.remove('hidden'); fetchR(); });
            document.getElementById('closeHistoryModal').addEventListener('click', () => document.getElementById('historyModal').classList.add('hidden'));
            selBln.addEventListener('change', fetchR); selThn.addEventListener('change', fetchR);
        });
    </script>
</body>
</html>

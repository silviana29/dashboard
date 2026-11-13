@include('partials.attendance-monitor', [
    'pageTitle' => 'Monitoring kehadiran lokasi',
    'viewerLabel' => 'Operator',
    'scopeDescription' => 'Pantau anggota dalam cakupan '.($gedung?->nama ?? 'lokasi Anda').' beserta aturan, laporan, foto, dan GPS.',
    'filterRoute' => 'pegawai.absensi.rekap',
    'exportRoute' => 'pegawai.absensi.export',
])

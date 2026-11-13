@include('partials.attendance-monitor', [
    'pageTitle' => 'Kehadiran anggota tim',
    'viewerLabel' => 'Atasan',
    'scopeDescription' => 'Lihat siapa yang sudah masuk, belum masuk, izin, atau mengirim laporan harian dari anggota tim Anda.',
    'filterRoute' => 'guru.guru.rekap',
    'exportRoute' => 'guru.export.rekap',
    'organization' => auth()->user()->organization,
])

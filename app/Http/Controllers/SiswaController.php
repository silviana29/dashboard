<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Absensi;
use App\Helpers\LogHelper;
use App\Models\Setting;
use Carbon\Carbon;
use App\Models\JenisIzin;
use App\Models\AttendanceType;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
    

class SiswaController extends Controller
{
    private const MAX_FOTO_BYTES = 200 * 1024;
    private const MAX_FOTO_DIMENSION = 1280;

    /**
     * Menampilkan foto absensi dari storage untuk hosting yang tidak
     * mendukung symbolic link public/storage.
     */
    public function showFotoAbsensi(string $path)
    {
        if (!preg_match('#^(?:\d+/)?\d{4}-\d{2}/[a-zA-Z0-9._-]+\.(?:jpe?g|png|webp)$#i', $path)) {
            abort(404);
        }

        $allowed = Absensi::where(function ($query) use ($path) {
                $query->where('foto_masuk', $path)->orWhere('foto_keluar', $path);
            })
            ->whereHas('siswa.user', fn ($query) => $query->where('organization_id', Auth::user()->organization_id))
            ->exists();
        abort_unless($allowed, 403);

        $storagePath = $this->absensiPhotoPath($path);
        $disk = Storage::disk(self::ABSENSI_PHOTO_DISK);

        abort_unless($disk->exists($storagePath), 404);

        return $disk->response($storagePath, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /**
     * Menampilkan halaman absensi siswa.
     */
    public function absenForm()
    {
        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $attendanceType = $siswaProfile->attendanceType
            ?? AttendanceType::where('organization_id', $user->organization_id)->where('mode', 'scheduled')->first();
        $today = now()->toDateString();
        $policy = $this->attendancePolicy($user->organization_id, $attendanceType);
        $jenisIzin = JenisIzin::where('organization_id', $user->organization_id)->orderBy('nama')->get();

        $absenHariIni = $siswaProfile->absensis()->where('tanggal', '>=', $today)->where('tanggal', '<', now()->addDay()->toDateString())->first();
        $this->setAbsensiPhotoUrls($absenHariIni);

        return view('siswa.absen', [
            'nama' => $siswaProfile->nama_lengkap,
            'absensi' => $absenHariIni,
            'gedung' => $siswaProfile->gedung,
            'serverTime' => now()->getTimestamp(),
            'jamTelat' => $policy['jam_telat'],
            'jamPulangMinimal' => $policy['jam_pulang_minimal'],
            'modeAbsensi' => $policy['mode_absensi'],
            'jenisIzin' => $jenisIzin,
            'organization' => $user->organization,
            'attendanceType' => $attendanceType,
            'attendanceMode' => $attendanceType?->mode ?? 'scheduled',
        ]);
    }

    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $attendanceType = $siswaProfile->attendanceType;
        if (!$attendanceType || $attendanceType->mode === 'report') {
            return back()->with('error', 'Tipe absensi Anda tidak menggunakan absen masuk.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|string',
            'keterangan_telat' => 'nullable|string|max:1000',
        ]);

        $gedung = $siswaProfile->gedung;
        $policy = $this->attendancePolicy($user->organization_id, $attendanceType);
        $now = Carbon::now();
        $jamMasuk = $now->format('H:i:s');
        $isRegulated = $policy['mode_absensi'] === 'regulated';
        $isTelat = $isRegulated && $now->format('H:i') > substr($policy['jam_telat'], 0, 5);

        if ($isTelat && !$request->filled('keterangan_telat')) {
            throw ValidationException::withMessages([
                'keterangan_telat' => 'Keterangan wajib diisi karena Anda melakukan absen setelah batas jam masuk.',
            ]);
        }

        $statusKetepatan = !$isRegulated ? 'Tidak Berlaku' : ($isTelat ? 'Terlambat' : 'Tepat Waktu');

        $imageData = $this->compressFoto($request->foto);
        $bulanFolder = $user->organization_id . '/' . now()->format('Y-m');
        $filename = 'absen_masuk_' . $user->id . '_' . time() . '.jpg';

        $fotoTersimpan = Storage::disk(self::ABSENSI_PHOTO_DISK)->put(
            $this->absensiPhotoPath("{$bulanFolder}/{$filename}"),
            $imageData
        );

        if (!$fotoTersimpan) {
            throw ValidationException::withMessages([
                'foto' => 'Foto gagal disimpan di server. Silakan hubungi admin hosting.',
            ]);
        }

        $isWFO = false;
        if ($gedung) {
            $distance = $this->getDistance($request->latitude, $request->longitude, $gedung->latitude, $gedung->longitude);
            $isWFO = $distance <= $gedung->radius_meter;
        }

        Absensi::updateOrCreate(
            ['siswa_id' => $siswaProfile->id, 'tanggal' => $now->toDateString()],
            [
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $bulanFolder . '/' . $filename,
                'lokasi_masuk_lat' => $request->latitude,
                'lokasi_masuk_long' => $request->longitude,
                'status_kerja' => $isWFO,
                'attendance_type_id' => $attendanceType->id,
                'attendance_type_name' => $attendanceType->name,
                'attendance_mode' => $attendanceType->mode,
                'status_kehadiran' => true,
                'status_ketepatan' => $statusKetepatan,
                'keterangan_telat' => $isTelat ? $request->input('keterangan_telat') : null,
                'izin' => false,
                'jenis_izin_id' => null,
                'keterangan_izin' => null,
                'file_izin' => null,
            ]
        );

        $logMasuk = 'Siswa melakukan absen masuk pada jam ' . $jamMasuk;
        if ($isTelat) {
            $logMasuk .= ' (Terlambat). Alasan: ' . $request->keterangan_telat;
        }
        LogHelper::log($request, 'Absen Masuk', $logMasuk);

        return redirect()->route('siswa.absen.form')->with('success', 'Absen masuk berhasil!');
    }


    public function absenKeluar(Request $request)
    {
        $attendanceType = Auth::user()->siswaProfile->attendanceType;
        if (!$attendanceType || $attendanceType->mode === 'report') {
            return back()->with('error', 'Tipe absensi Anda tidak menggunakan absen keluar.');
        }

        $request->validate([
            'foto' => 'required|string',
            'aktivitas_hari_ini' => 'nullable|string|max:1000',
            'keterangan_pulang_cepat' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;
        $today = now()->toDateString();

        $absen = Absensi::where('siswa_id', $siswaProfile->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($absen && $absen->jam_masuk && !$absen->jam_pulang) {
            $policy = $this->attendancePolicy($user->organization_id, $attendanceType);
            $jamPulang = now()->format('H:i:s');
            $isRegulated = $policy['mode_absensi'] === 'regulated';
            $isPulangCepat = $isRegulated && now()->format('H:i') < substr($policy['jam_pulang_minimal'], 0, 5);

            if ($isPulangCepat && !$request->filled('keterangan_pulang_cepat')) {
                throw ValidationException::withMessages([
                    'keterangan_pulang_cepat' => 'Keterangan wajib diisi karena Anda pulang sebelum waktu minimal.',
                ]);
            }

            $imageData = $this->compressFoto($request->foto);
            $bulanFolder = $user->organization_id . '/' . now()->format('Y-m');
            $filename = 'absen_keluar_' . $user->id . '_' . time() . '.jpg';


            $fotoTersimpan = Storage::disk(self::ABSENSI_PHOTO_DISK)->put(
                $this->absensiPhotoPath("{$bulanFolder}/{$filename}"),
                $imageData
            );

            if (!$fotoTersimpan) {
                throw ValidationException::withMessages([
                    'foto' => 'Foto gagal disimpan di server. Silakan hubungi admin hosting.',
                ]);
            }

            $absen->update([
                'jam_pulang' => $jamPulang,
                'foto_keluar' => $bulanFolder . '/' . $filename,
                'status_pulang' => !$isRegulated ? 'Tidak Berlaku' : ($isPulangCepat ? 'Pulang Cepat' : 'Sesuai Jadwal'),
                'aktivitas_hari_ini' => $request->input('aktivitas_hari_ini'),
                'keterangan_pulang_cepat' => $isPulangCepat ? $request->input('keterangan_pulang_cepat') : null,
            ]);

            $logPulang = 'Siswa melakukan absen keluar pada jam ' . $jamPulang;
            if ($isPulangCepat) {
                $logPulang .= ' (Pulang Cepat). Alasan: ' . $request->keterangan_pulang_cepat;
            }
            LogHelper::log($request, 'Absen Keluar', $logPulang);

            return redirect()->route('siswa.absen.form')->with('success', 'Absen keluar berhasil!');
        }

        LogHelper::log($request, 'Gagal Absen Keluar', 'Siswa mencoba absen keluar tanpa absen masuk');

        return redirect()->route('siswa.absen.form')->with('error', 'Anda belum bisa melakukan absen keluar saat ini.');
    }

    public function laporHarian(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'string'],
            'aktivitas_hari_ini' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = Auth::user();
        $siswa = $user->siswaProfile;
        $attendanceType = $siswa->attendanceType;
        if (!$attendanceType || $attendanceType->mode !== 'report') {
            return back()->with('error', 'Tipe absensi Anda bukan Lapor Harian.');
        }

        $existing = Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', today())->first();
        if ($existing?->report_at) {
            return back()->with('error', 'Laporan harian hari ini sudah dikirim.');
        }

        $imageData = $this->compressFoto($request->foto);
        $bulanFolder = $user->organization_id . '/' . now()->format('Y-m');
        $filename = 'lapor_harian_' . $user->id . '_' . time() . '.jpg';
        $stored = Storage::disk(self::ABSENSI_PHOTO_DISK)->put(
            $this->absensiPhotoPath("{$bulanFolder}/{$filename}"),
            $imageData
        );
        if (!$stored) {
            throw ValidationException::withMessages(['foto' => 'Foto laporan gagal disimpan.']);
        }

        Absensi::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => today()->toDateString()],
            [
                'attendance_type_id' => $attendanceType->id,
                'attendance_type_name' => $attendanceType->name,
                'attendance_mode' => 'report',
                'report_at' => now(),
                'status_kehadiran' => true,
                'status_ketepatan' => 'Tidak Berlaku',
                'status_pulang' => 'Tidak Berlaku',
                'foto_masuk' => $bulanFolder . '/' . $filename,
                'foto_keluar' => null,
                'aktivitas_hari_ini' => $request->aktivitas_hari_ini,
                'lokasi_masuk_lat' => $request->latitude,
                'lokasi_masuk_long' => $request->longitude,
            ]
        );

        LogHelper::log($request, 'Lapor Harian', "Mengirim laporan harian dengan tipe {$attendanceType->name} dari koordinat {$request->latitude}, {$request->longitude}");
        return back()->with('success', 'Laporan harian berhasil dikirim.');
    }

    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Mengubah foto Base64 menjadi JPEG dengan ukuran maksimal 200 KB.
     */
    private function compressFoto(string $fotoBase64): string
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) {
            throw ValidationException::withMessages([
                'foto' => 'Server belum mendukung kompresi foto (ekstensi PHP GD belum aktif).',
            ]);
        }

        $encodedImage = preg_replace('#^data:image/[a-zA-Z0-9.+-]+;base64,#i', '', $fotoBase64);
        $imageData = base64_decode($encodedImage, true);
        $sourceImage = $imageData !== false ? @imagecreatefromstring($imageData) : false;

        if ($sourceImage === false) {
            throw ValidationException::withMessages([
                'foto' => 'Data foto tidak valid. Silakan ambil foto kembali.',
            ]);
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $scale = min(1, self::MAX_FOTO_DIMENSION / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));

        $workingImage = $this->resizeFoto($sourceImage, $width, $height);
        imagedestroy($sourceImage);

        // Turunkan kualitas lebih dahulu. Jika masih terlalu besar, kecilkan
        // dimensinya dan ulangi sampai hasil benar-benar di bawah 200 KB.
        for ($resizeAttempt = 0; $resizeAttempt < 10; $resizeAttempt++) {
            for ($quality = 85; $quality >= 35; $quality -= 5) {
                ob_start();
                imagejpeg($workingImage, null, $quality);
                $compressed = ob_get_clean();

                if ($compressed !== false && strlen($compressed) <= self::MAX_FOTO_BYTES) {
                    imagedestroy($workingImage);
                    return $compressed;
                }
            }

            $newWidth = max(1, (int) round(imagesx($workingImage) * 0.85));
            $newHeight = max(1, (int) round(imagesy($workingImage) * 0.85));
            $smallerImage = $this->resizeFoto($workingImage, $newWidth, $newHeight);
            imagedestroy($workingImage);
            $workingImage = $smallerImage;
        }

        imagedestroy($workingImage);

        throw ValidationException::withMessages([
            'foto' => 'Foto tidak dapat dikompres hingga 200 KB. Silakan ambil foto kembali.',
        ]);
    }

    private function resizeFoto($sourceImage, int $width, int $height)
    {
        $resizedImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($resizedImage, 255, 255, 255);
        imagefill($resizedImage, 0, 0, $white);

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $width,
            $height,
            imagesx($sourceImage),
            imagesy($sourceImage)
        );

        return $resizedImage;
    }

    public function getRiwayat(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|between:1,12',
            'tahun' => 'required|integer',
        ]);

        $user = Auth::user();
        $siswaProfile = $user->siswaProfile;

        $riwayat = Absensi::where('siswa_id', $siswaProfile->id)
            ->whereYear('tanggal', $request->tahun)
            ->whereMonth('tanggal', $request->bulan)
            ->orderBy('tanggal', 'asc')
            ->get();

        foreach ($riwayat as $absensi) {
            $this->setAbsensiPhotoUrls($absensi);
        }

        return response()->json($riwayat);
    }



    public function absenIzin(Request $request)
    {
        $request->validate([
            'jenis_izin_id' => ['required', Rule::exists('jenis_izin', 'id')->where('organization_id', $request->user()->organization_id)],
            'keperluan' => 'nullable|string',
            'izin_dari_jam' => 'nullable|date_format:H:i',
            'file_izin' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $user = Auth::user();
        $siswa = $user->siswaProfile;

        $absensiHariIni = Absensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if ($absensiHariIni?->jam_pulang) {
            return back()->with('error', 'Izin tidak dapat diajukan setelah Anda absen pulang.');
        }

        if ($absensiHariIni?->izin) {
            return back()->with('error', 'Anda sudah mengajukan izin hari ini.');
        }

        if ($absensiHariIni?->jam_masuk && !$request->filled('izin_dari_jam')) {
            throw ValidationException::withMessages([
                'izin_dari_jam' => 'Jam mulai izin wajib diisi untuk izin di tengah waktu kerja.',
            ]);
        }

        $path = null;
        if ($request->hasFile('file_izin')) {
            $path = $request->file('file_izin')->store('izin_berkas/' . $user->organization_id, 'public');
        }

        $isIzinParsial = (bool) $absensiHariIni?->jam_masuk;

        $izinData = [
            'status_kehadiran' => $isIzinParsial,
            'izin' => true,
            'jenis_izin_id' => $request->jenis_izin_id,
            'keterangan_izin' => $request->keperluan,
            'izin_dari_jam' => $isIzinParsial ? $request->izin_dari_jam : null,
            'file_izin' => $path,
        ];

        if ($absensiHariIni) {
            $absensiHariIni->update($izinData);
        } else {
            Absensi::create(array_merge($izinData, [
                'siswa_id' => $siswa->id,
                'tanggal' => now()->toDateString(),
            ]));
        }


        $jenisIzin = JenisIzin::where('organization_id', $user->organization_id)->find($request->jenis_izin_id);
        $namaIzin = $jenisIzin ? $jenisIzin->nama : 'ID: ' . $request->jenis_izin_id;

        $logMessage = 'Siswa "' . $siswa->nama_lengkap . '" mengajukan izin (' . $namaIzin . ')';
        if ($isIzinParsial) {
            $logMessage .= ' mulai jam ' . $request->izin_dari_jam;
        }
        $logMessage .= ' dengan keperluan: ' . ($request->keperluan ?: '-');

        LogHelper::log($request, 'Pengajuan Izin Siswa', $logMessage);

        return back()->with('success', 'Izin berhasil dikirim');
    }

    private function attendancePolicy(int $organizationId, ?AttendanceType $attendanceType = null): array
    {
        if ($attendanceType) {
            return [
                'jam_telat' => $attendanceType->jam_telat ?: '08:15:00',
                'jam_pulang_minimal' => $attendanceType->jam_pulang_minimal ?: '17:00:00',
                'mode_absensi' => $attendanceType->mode === 'scheduled' ? 'regulated' : 'simple',
            ];
        }

        $settings = Setting::where('organization_id', $organizationId)
            ->whereIn('key', ['jam_telat', 'jam_pulang_minimal', 'mode_absensi'])
            ->pluck('value', 'key');

        return [
            'jam_telat' => $settings->get('jam_telat', '08:15:00'),
            'jam_pulang_minimal' => $settings->get('jam_pulang_minimal', '17:00:00'),
            'mode_absensi' => $settings->get('mode_absensi', 'regulated'),
        ];
    }
}

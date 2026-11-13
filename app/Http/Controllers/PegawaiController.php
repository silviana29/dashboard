<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Models\SiswaProfile;
use App\Models\Gedung;
use App\Models\PegawaiProfile;
use Carbon\Carbon;
use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Setting;

class PegawaiController extends Controller
{
    public function rekapAbsensi(Request $request)
    {
        $pegawai = Auth::user()->pegawaiProfile;
        $gedung = $pegawai->gedung;
        $organization = Auth::user()->organization;
        $jamTelat = Setting::where('organization_id', Auth::user()->organization_id)->where('key', 'jam_telat')->value('value') ?? '08:15:00';

        $siswaId = $request->input('siswa_id');
        $bulan = $request->input('bulan');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $tanggal = $request->input('tanggal', ($request->has('bulan') || $tanggalMulai || $tanggalSelesai) ? null : now()->toDateString());
        $all_siswas = SiswaProfile::where('gedung_id', $pegawai->gedung_id)
            ->with(['attendanceType', 'team'])
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        $querySiswa = SiswaProfile::where('gedung_id', $pegawai->gedung_id);
        if ($siswaId) {
            $querySiswa->where('id', $siswaId);
        }
        $absensiQuery = function ($q) use ($tanggal, $bulan, $tanggalMulai, $tanggalSelesai) {
            if ($tanggalMulai && $tanggalSelesai) {
                $q->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            } elseif ($tanggalMulai) {
                $q->whereDate('tanggal', '>=', $tanggalMulai);
            } elseif ($tanggalSelesai) {
                $q->whereDate('tanggal', '<=', $tanggalSelesai);
            } elseif ($bulan) {
                $date = Carbon::parse($bulan);
                $q->whereYear('tanggal', $date->year)->whereMonth('tanggal', $date->month);
            } elseif ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }
            $q->orderBy('tanggal', 'desc');
        };

        if ($bulan || $tanggalMulai || $tanggalSelesai || ($siswaId && !$tanggal)) {
            $displayMode = 'monthly';
        } else {
            $displayMode = 'daily';
        }

        $siswas = $querySiswa->with(['attendanceType', 'team', 'gedung', 'absensis' => $absensiQuery, 'absensis.jenisIzin'])
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        $this->setStudentAbsensiPhotoUrls($siswas);

        $logMessage = 'Pegawai melihat rekap absensi';

        if ($siswaId) {
            $namaSiswa = $all_siswas->where('id', $siswaId)->first()?->nama_lengkap ?? 'Siswa Tidak Dikenal';
            $logMessage .= " untuk siswa: $namaSiswa";
        }

        if ($tanggal) {
            $logMessage .= " pada tanggal: " . Carbon::parse($tanggal)->format('d M Y');
        } elseif ($bulan) {
            $logMessage .= " pada bulan: " . $date = Carbon::parse($bulan) ->format('F Y');
        } else {
            $logMessage .= " (semua data)";
        }

        $logMessage .= ' di gedung: ' . ($gedung?->nama ?? 'tanpa lokasi');

        LogHelper::log($request, 'Lihat Rekap Absensi', $logMessage);

        return view('pegawai.absensi', compact(
            'siswas',
            'all_siswas',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'gedung',
            'bulan',
            'siswaId',
            'displayMode',
            'jamTelat',
            'organization'
        ));
    }
    public function exportRekap(Request $request)
    {
        $pegawai = Auth::user()->pegawaiProfile;
        $gedung = $pegawai->gedung;

        $query = SiswaProfile::where('gedung_id', $gedung->id);

        $displayMode = 'daily';
        $tanggal = $request->input('tanggal', now()->toDateString());
        if ($request->filled('bulan')) {
            $displayMode = 'monthly';
            list($tahun, $bulan) = explode('-', $request->bulan);
            $query->with(['absensis' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->orderBy('tanggal', 'asc');
            }]);
        } else {
            $query->with(['absensis' => function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            }]);
        }

        if ($request->filled('siswa_id')) {
            $query->where('id', $request->siswa_id);
            if ($request->filled('bulan')) {
                $displayMode = 'user_history';
            }
        }
        $siswas = $query->orderBy('nama_lengkap', 'asc')->get();
        $this->setStudentAbsensiPhotoUrls($siswas);
        $jamTelat = Setting::where('organization_id', Auth::user()->organization_id)->where('key', 'jam_telat')->value('value') ?? '08:15:00';

        $periode = $request->filled('bulan') ? $request->bulan : $tanggal;
        $filename = 'rekap-absensi-pegawai-' . $periode . '.xlsx';

        return Excel::download(new RekapAbsensiExport($siswas, $tanggal), $filename);
    }
}

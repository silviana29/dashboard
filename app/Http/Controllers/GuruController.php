<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Models\SiswaProfile;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\User;
use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function rekapAbsensi(Request $request)
    {
        $guru = Auth::user();
        $jamTelat = Setting::where('organization_id', $guru->organization_id)->where('key', 'jam_telat')->value('value') ?? '08:15:00';
        $siswaId = $request->input('siswa_id');
        $bulan = $request->input('bulan');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $tanggal = $request->input('tanggal', ($request->has('bulan') || $tanggalMulai || $tanggalSelesai) ? null : now()->toDateString());

        $teamIds = $guru->supervisedTeams()->pluck('teams.id');
        $legacyMemberIds = $guru->siswaYangDiawasi()->pluck('siswa_profiles.id');
        $baseQuery = SiswaProfile::whereHas('user', fn ($query) => $query->where('organization_id', $guru->organization_id))
            ->where(function ($query) use ($teamIds, $legacyMemberIds) {
                $query->whereIn('team_id', $teamIds)->orWhereIn('id', $legacyMemberIds);
            });
        $all_siswas = (clone $baseQuery)->with(['attendanceType', 'team'])->orderBy('nama_lengkap')->get();
        $querySiswa = clone $baseQuery;
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
            ->orderBy('nama_lengkap')
            ->get();
        $this->setStudentAbsensiPhotoUrls($siswas);
        LogHelper::log($request, 'Lihat absensi tim', 'Atasan membuka rekap anggota tim yang dipantau.');

        return view('guru.guru', compact(
            'siswas',
            'all_siswas',
            'tanggal',
            'tanggalMulai',
            'tanggalSelesai',
            'bulan',
            'siswaId',
            'displayMode',
            'jamTelat'
        ));
    }

    public function exportRekap(Request $request)
    {
        $guru = Auth::user();
        $jamTelat = Setting::where('organization_id', $guru->organization_id)->where('key', 'jam_telat')->value('value') ?? '08:15:00';
        $tanggal = $request->input('tanggal', now()->toDateString());

        $teamIds = $guru->supervisedTeams()->pluck('teams.id');
        $legacyMemberIds = $guru->siswaYangDiawasi()->pluck('siswa_profiles.id');
        $query = SiswaProfile::whereHas('user', fn ($builder) => $builder->where('organization_id', $guru->organization_id))
            ->where(function ($builder) use ($teamIds, $legacyMemberIds) {
                $builder->whereIn('team_id', $teamIds)->orWhereIn('id', $legacyMemberIds);
            });

        $displayMode = 'daily';
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

        $siswas = $query->orderBy('nama_lengkap')->get();
        $this->setStudentAbsensiPhotoUrls($siswas);

        $periode = $request->filled('bulan') ? $request->bulan : $tanggal;
        $filename = 'rekap-absensi-guru-' . $periode . '.xlsx';
        LogHelper::log($request, 'Ekspor absensi tim', "Atasan mengunduh rekap periode {$periode}.");

        return Excel::download(new RekapAbsensiExport($siswas, $tanggal), $filename);
    }
}

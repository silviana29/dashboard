<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $casts = [
        'tanggal' => 'date',
        'izin' => 'boolean',
        'status_kehadiran' => 'boolean',
        'status_kerja' => 'boolean',
        'report_at' => 'datetime',
    ];

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'lokasi_masuk_lat',
        'lokasi_masuk_long',
        'jam_pulang',
        'status_kerja',
        'status_kehadiran',
        'foto_masuk',
        'foto_keluar',
        'keterangan_izin',
        'jenis_izin_id',
        'file_izin',
        'izin',
        'status_ketepatan',
        'aktivitas_hari_ini',
        'keterangan_telat',
        'keterangan_pulang_cepat',
        'status_pulang',
        'izin_dari_jam',
        'attendance_type_id',
        'attendance_type_name',
        'attendance_mode',
        'report_at',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(SiswaProfile::class, 'siswa_id');
    }

    public function jenisIzin(): BelongsTo
    {
        return $this->belongsTo(JenisIzin::class, 'jenis_izin_id');
    }

    public function attendanceType(): BelongsTo
    {
        return $this->belongsTo(AttendanceType::class);
    }

    public function getDurasiKerjaAttribute(): ?string
    {
        if ($this->jam_pulang && $this->jam_masuk) {
            $masuk = Carbon::parse($this->jam_masuk);
            $pulang = Carbon::parse($this->jam_pulang);

            $durasi = $pulang->diff($masuk);
            return $durasi->format('%h jam %i menit');
        }

        return null;
    }

    public function getAttendanceTypeLabelAttribute(): string
    {
        return $this->attendance_type_name ?: ($this->status_kerja ? 'WFO' : 'WFH');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaProfile extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'nama_lengkap',
        'asal_pkl',
        'gedung_id',
        'attendance_type_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

    public function attendanceType(): BelongsTo
    {
        return $this->belongsTo(AttendanceType::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }
    public function guru()
    {
        return $this->belongsToMany(User::class, 'guru_siswa', 'siswa_id', 'guru_id');
    }
}

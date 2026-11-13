<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'organization_id',
        'requested_team_id',
        'username',
        'password',
        'role',
        'approval_status',
        'is_billing_suspended',
        'requested_at',
        'approved_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_billing_suspended' => 'boolean',
    ];

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function requestedTeam()
    {
        return $this->belongsTo(Team::class, 'requested_team_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->siswaProfile?->nama_lengkap
            ?? $this->pegawaiProfile?->nama_lengkap
            ?? $this->username;
    }

    public function getLoginIdentityAttribute(): string
    {
        $hasCustom = $this->organization?->plan_details->has_custom_suffix ?? false;
        $suffix = $hasCustom ? '' : '.mm';
        return $this->username . '@' . ($this->organization?->slug ?? 'workspace') . $suffix;
    }

    public function pegawaiProfile()
    {
        return $this->hasOne(PegawaiProfile::class);
    }
    public function siswaYangDiawasi()
    {
        return $this->belongsToMany(SiswaProfile::class, 'guru_siswa', 'guru_id', 'siswa_id');
    }

    public function supervisedTeams()
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }
}

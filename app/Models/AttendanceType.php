<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceType extends Model
{
    protected $fillable = ['organization_id', 'name', 'mode', 'jam_telat', 'jam_pulang_minimal', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SiswaProfile::class);
    }

    public function getModeLabelAttribute(): string
    {
        return match ($this->mode) {
            'scheduled' => 'Terjadwal · masuk ' . substr((string) $this->jam_telat, 0, 5) . ', pulang minimal ' . substr((string) $this->jam_pulang_minimal, 0, 5),
            'flexible' => 'Fleksibel · masuk dan keluar tanpa aturan jam',
            default => 'Lapor · satu foto, keterangan, dan GPS aktual',
        };
    }
}

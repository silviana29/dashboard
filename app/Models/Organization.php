<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'company_code',
        'type',
        'timezone',
        'primary_color',
        'is_active',
        'plan_id',
        'plan',
        'subscription_status',
        'subscription_ends_at',
        'subscription_reminder_sent_at',
        'suspended_reason',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
        'subscription_reminder_sent_at' => 'datetime',
    ];

    public function subscriptionPlan()
    {
        return $this->belongsTo(Plan::class, 'plan_id')->withTrashed();
    }

    public function getPlanDetailsAttribute()
    {
        if ($this->plan_id) {
            return $this->subscriptionPlan ?? $this->fallbackPlan();
        }
        return $this->fallbackPlan();
    }

    protected function fallbackPlan()
    {
        // Try to map from text plan
        $plan = Plan::where('name', $this->plan)->first();
        if ($plan) {
            return $plan;
        }
        // Fallback to dynamic default or first plan
        return Plan::where('price', 0)->first() ?? Plan::first() ?? new Plan([
            'name' => 'Free',
            'price' => 0,
            'max_users' => 10,
            'retention_months' => 3,
            'has_audit_log' => false,
            'has_custom_suffix' => false
        ]);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function gedungs(): HasMany
    {
        return $this->hasMany(Gedung::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function jenisIzin(): HasMany
    {
        return $this->hasMany(JenisIzin::class);
    }

    public function attendanceTypes(): HasMany
    {
        return $this->hasMany(AttendanceType::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function supportThreads(): HasMany
    {
        return $this->hasMany(SupportThread::class);
    }

    public function planOffers(): HasMany
    {
        return $this->hasMany(PlanOffer::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'company' => 'Perusahaan',
            'school' => 'Sekolah',
            'government' => 'Instansi',
            'community' => 'Komunitas',
            default => 'UMKM',
        };
    }
}

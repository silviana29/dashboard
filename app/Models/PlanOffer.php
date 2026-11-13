<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanOffer extends Model
{
    protected $fillable = [
        'organization_id', 'plan_id', 'support_thread_id', 'offered_by',
        'status', 'offered_at', 'expires_at', 'paid_at',
    ];

    protected $casts = [
        'offered_at' => 'datetime',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function plan() { return $this->belongsTo(Plan::class)->withTrashed(); }
    public function supportThread() { return $this->belongsTo(SupportThread::class); }
    public function offeredBy() { return $this->belongsTo(User::class, 'offered_by'); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function isAvailable(): bool
    {
        return $this->status === 'offered' && (! $this->expires_at || $this->expires_at->isFuture());
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'duration_value',
        'duration_unit',
        'max_users',
        'retention_months',
        'has_audit_log',
        'has_custom_suffix',
        'is_private',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_value' => 'integer',
        'max_users' => 'integer',
        'retention_months' => 'integer',
        'has_audit_log' => 'boolean',
        'has_custom_suffix' => 'boolean',
        'is_private' => 'boolean',
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function payments() { return $this->hasMany(Payment::class); }
    public function offers() { return $this->hasMany(PlanOffer::class); }

    public function getDurationLabelAttribute(): string
    {
        $value = max(1, (int) $this->duration_value);
        return $value.' '.($this->duration_unit === 'days' ? 'hari' : 'bulan');
    }

    public function periodEnd(Carbon $start): Carbon
    {
        $value = max(1, (int) $this->duration_value);
        return $this->duration_unit === 'days'
            ? $start->copy()->addDays($value)->subDay()
            : $start->copy()->addMonthsNoOverflow($value)->subDay();
    }
}

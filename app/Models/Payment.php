<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'organization_id',
        'plan_id',
        'plan_offer_id',
        'recorded_by',
        'amount',
        'status',
        'gateway',
        'merchant_order_id',
        'invoice_number',
        'invoice_issued_at',
        'method',
        'reference',
        'gateway_reference',
        'payment_url',
        'duration_months',
        'duration_value',
        'duration_unit',
        'expires_at',
        'customer_email',
        'customer_phone',
        'gateway_payload',
        'period_start',
        'period_end',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'invoice_issued_at' => 'datetime',
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',
        'duration_months' => 'integer',
        'duration_value' => 'integer',
        'gateway_payload' => 'array',
    ];

    public function organization(){ return $this->belongsTo(Organization::class); }
    public function plan(){ return $this->belongsTo(Plan::class)->withTrashed(); }
    public function planOffer(){ return $this->belongsTo(PlanOffer::class); }
    public function recorder(){ return $this->belongsTo(User::class, 'recorded_by'); }
}

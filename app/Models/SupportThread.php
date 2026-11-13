<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportThread extends Model
{
    protected $fillable = ['organization_id','created_by','subject','category','priority','status','last_message_at'];
    protected $casts = ['last_message_at'=>'datetime'];
    public function organization(){ return $this->belongsTo(Organization::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
    public function messages(){ return $this->hasMany(SupportMessage::class); }
    public function planOffers(){ return $this->hasMany(PlanOffer::class); }
}

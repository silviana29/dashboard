<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['support_thread_id','user_id','sender_role','body','read_at'];
    protected $casts = ['read_at'=>'datetime'];
    public function thread(){ return $this->belongsTo(SupportThread::class, 'support_thread_id'); }
    public function user(){ return $this->belongsTo(User::class); }
}

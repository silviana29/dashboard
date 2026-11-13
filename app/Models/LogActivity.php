<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'user',
        'role',
        'aksi',
        'keterangan',
        'ip_address',
        'user_agent',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

}

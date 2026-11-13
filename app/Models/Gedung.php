<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    protected $fillable = [
        'organization_id',
        'nama',
        'latitude',
        'longitude',
        'radius_meter',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function siswaProfiles()
    {
        return $this->hasMany(SiswaProfile::class);
    }

    public function pegawaiProfiles()
    {
        return $this->hasMany(PegawaiProfile::class);
    }
}

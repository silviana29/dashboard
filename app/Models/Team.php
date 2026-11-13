<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = ['organization_id', 'name', 'description'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SiswaProfile::class);
    }

    public function supervisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}

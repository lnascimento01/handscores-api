<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',     // UUID persistente do app
        'platform',      // android|ios
        'model',
        'ip',
        'verified_at',
        'last_seen_at',
    ];

    protected $casts = [
        'verified_at'  => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->verified_at);
    }
}

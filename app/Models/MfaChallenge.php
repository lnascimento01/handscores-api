<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MfaChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'factor_id',
        'channel',
        'code',
        'expires_at',
        'attempts',
        'ip',
        'user_agent',
        'consumed',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function factor()
    {
        return $this->belongsTo(MfaFactor::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MfaFactor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',        // totp|sms|email|whatsapp
        'label',
        'secret',
        'destination',
        'verified',
        'meta',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'meta'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenges()
    {
        return $this->hasMany(MfaChallenge::class, 'factor_id');
    }
}

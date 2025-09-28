<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $table = 'refresh_tokens';

    protected $fillable = [
        'user_id',
        'token',
        'ip_address',
        'user_agent',
        'revoked',
        'expires_at'
        ];

    protected $casts = [
        'revoked' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

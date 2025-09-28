<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billings';

    protected $fillable = [
        'user_id',
        'transaction_code',
        'amount',
        'status',
        'description'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function billing(){
        return $this->hasMany(Billing::class);
    }

    public function refreshToken(){
        return $this->hasMany(RefreshToken::class);
    }
}

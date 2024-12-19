<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    protected $fillable = [
        'user_id',
        'location_branch',
        'payment_enabled',
        'topup_enabled'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function topups()
    {
        return $this->hasMany(Topup::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'card_balance',
        'expiry_date'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function topups()
    {
        return $this->hasMany(Topup::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $fillable = [
        'terminal_id',
        'card_id',
        'topup_date_time',
        'amount'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}

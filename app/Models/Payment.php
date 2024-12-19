<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    protected $fillable = [
        'terminal_id',
        'card_id',
        'schedule_id',
        'payment_date_time',
        'amount_paid'
    ];

    public function terminal()
    {
        return $this->belongsTo(Terminal::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}

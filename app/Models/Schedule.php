<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'train_title',
        'train_category',
        'start_station',
        'end_station',
        'start_time',
        'end_time'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

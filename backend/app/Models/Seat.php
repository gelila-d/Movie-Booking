<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditorium_id',
        'row_letter',
        'seat_number',
        'seat_label',
        'type', // 'regular', 'vip', 'accessible'
        'status', // 'active', 'maintenance'
    ];

    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }
}

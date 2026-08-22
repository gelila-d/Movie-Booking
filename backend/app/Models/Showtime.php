<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'auditorium_id',
        'auditorium',
        'start_time',
        'end_time',
        'price',
        'total_seats',
        'available_seats',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'float',
        'total_seats' => 'integer',
        'available_seats' => 'integer',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function auditoriumDetail()
    {
        return $this->belongsTo(Auditorium::class, 'auditorium_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

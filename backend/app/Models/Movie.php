<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'show_time',
        'total_seats',
        'available_seats',
        'image',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
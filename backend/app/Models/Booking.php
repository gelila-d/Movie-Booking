<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Movie;
use App\Models\Showtime;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'showtime_id',
        'seats_booked',
        'seat_numbers',
        'total_price',
    ];

    protected $casts = [
        'seat_numbers' => 'array',
        'total_price' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}
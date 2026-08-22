<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'genre',
        'duration',
        'rating',
        'trailer_url',
        'is_published',
        'show_time',
        'image',
        'available_seats',
        'total_seats',
    ];

    protected $casts = [
        'show_time' => 'datetime',
        'is_published' => 'boolean',
        'duration' => 'integer',
        'available_seats' => 'integer',
        'total_seats' => 'integer',
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
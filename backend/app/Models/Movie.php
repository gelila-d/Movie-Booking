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
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
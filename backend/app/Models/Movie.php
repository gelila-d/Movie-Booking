<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'description',
        'release_date',
        'price',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
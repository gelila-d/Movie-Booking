<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Movie;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'seats_booked',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
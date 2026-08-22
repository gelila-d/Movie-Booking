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
        'ticket_details',
        'total_price',
        'payment_status',
        'payment_method',
        'transaction_ref',
        'status',
        'refund_status',
        'refund_amount',
        'refund_ref',
        'cancelled_at',
    ];

    protected $casts = [
        'seat_numbers' => 'array',
        'ticket_details' => 'array',
        'total_price' => 'float',
        'refund_amount' => 'float',
        'cancelled_at' => 'datetime',
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
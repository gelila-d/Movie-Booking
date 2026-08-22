<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    protected $table = 'auditoriums';

    protected $fillable = [
        'cinema_id',
        'name',
        'rows_count',
        'seats_per_row',
        'total_seats',
        'seat_layout',
    ];

    protected $casts = [
        'rows_count' => 'integer',
        'seats_per_row' => 'integer',
        'total_seats' => 'integer',
        'seat_layout' => 'array',
    ];

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}

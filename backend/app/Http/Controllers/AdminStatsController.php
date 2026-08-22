<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $totalMovies = Movie::count();
        $totalShowtimes = Showtime::count();

        // Calculate seats from showtimes if available, or fall back to movie table
        if ($totalShowtimes > 0) {
            $totalSeats = Showtime::sum('total_seats');
            $availableSeats = Showtime::sum('available_seats');
        } else {
            $totalSeats = Movie::sum('total_seats');
            $availableSeats = Movie::sum('available_seats');
        }

        $totalBookedSeats = Booking::sum('seats_booked');

        $showtimeStats = Showtime::with('movie')->get()->map(function ($st) {
            $booked = $st->total_seats - $st->available_seats;
            return [
                'id' => $st->id,
                'movie_title' => $st->movie ? $st->movie->title : 'Unknown',
                'auditorium' => $st->auditorium,
                'start_time' => $st->start_time,
                'price' => $st->price,
                'total_seats' => $st->total_seats,
                'available_seats' => $st->available_seats,
                'booked_seats' => $booked,
                'fill_rate' => $st->total_seats > 0 ? round(($booked / $st->total_seats) * 100, 2) : 0
            ];
        });

        $movieStats = Movie::all()->map(function ($movie) {
            $movieShowtimes = Showtime::where('movie_id', $movie->id)->get();
            if ($movieShowtimes->count() > 0) {
                $totalS = $movieShowtimes->sum('total_seats');
                $availS = $movieShowtimes->sum('available_seats');
                $bookedS = $totalS - $availS;
            } else {
                $totalS = $movie->total_seats;
                $availS = $movie->available_seats;
                $bookedS = $totalS - $availS;
            }

            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'total_seats' => $totalS,
                'available_seats' => $availS,
                'booked_seats' => $bookedS,
                'fill_rate' => $totalS > 0 ? round(($bookedS / $totalS) * 100, 2) : 0
            ];
        });

        return response()->json([
            'summary' => [
                'total_movies' => $totalMovies,
                'total_showtimes' => $totalShowtimes,
                'total_seats' => $totalSeats,
                'available_seats' => $availableSeats,
                'booked_seats' => $totalBookedSeats,
                'overall_fill_rate' => $totalSeats > 0 ? round(($totalBookedSeats / $totalSeats) * 100, 2) : 0
            ],
            'movie_stats' => $movieStats,
            'showtime_stats' => $showtimeStats,
        ]);
    }
}

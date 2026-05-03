<?php

namespace App\Http\Controllers;

use App\Models\Movie;
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
        $totalSeats = Movie::sum('total_seats');
        $availableSeats = Movie::sum('available_seats');
        $totalBookedSeats = $totalSeats - $availableSeats;
        
        // Alternatively, count from bookings
        // $totalBookedSeats = Booking::sum('seats_booked');

        $movieStats = Movie::all()->map(function ($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'total_seats' => $movie->total_seats,
                'available_seats' => $movie->available_seats,
                'booked_seats' => $movie->total_seats - $movie->available_seats,
                'fill_rate' => $movie->total_seats > 0 ? round((($movie->total_seats - $movie->available_seats) / $movie->total_seats) * 100, 2) : 0
            ];
        });

        return response()->json([
            'summary' => [
                'total_movies' => $totalMovies,
                'total_seats' => $totalSeats,
                'available_seats' => $availableSeats,
                'booked_seats' => $totalBookedSeats,
                'overall_fill_rate' => $totalSeats > 0 ? round(($totalBookedSeats / $totalSeats) * 100, 2) : 0
            ],
            'movie_stats' => $movieStats
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $today = Carbon::today();

        // 1. TODAY'S REAL-TIME METRICS
        $todayBookings = Booking::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->get();

        $todayTicketsSold = $todayBookings->sum('seats_booked');
        $todayRevenue = $todayBookings->sum('total_price');

        $showingMoviesCount = Movie::where('is_published', true)->count();
        $registeredUsersCount = User::count();

        $todayShowtimes = Showtime::whereDate('start_time', $today)->get();
        $todayCapacity = $todayShowtimes->sum('total_seats');
        $todayBookedSeats = $todayCapacity - $todayShowtimes->sum('available_seats');
        $todayOccupancyRate = $todayCapacity > 0 ? round(($todayBookedSeats / $todayCapacity) * 100, 1) : 0;

        // 2. CANCELLATION & REFUND STATS
        $totalBookingsCount = Booking::count();
        $cancelledBookings = Booking::where('status', 'cancelled')->get();
        $cancelledCount = $cancelledBookings->count();
        $totalRefundedAmount = $cancelledBookings->sum('refund_amount');
        $cancellationRate = $totalBookingsCount > 0 ? round(($cancelledCount / $totalBookingsCount) * 100, 1) : 0;

        // 3. REVENUE & BOOKINGS BY MOVIE REPORT
        $movieReports = Movie::all()->map(function ($movie) {
            $movieBookings = Booking::where('movie_id', $movie->id)
                ->where('status', '!=', 'cancelled')
                ->get();

            $ticketsSold = $movieBookings->sum('seats_booked');
            $revenue = $movieBookings->sum('total_price');

            $movieShowtimes = Showtime::where('movie_id', $movie->id)->get();
            $totalCap = $movieShowtimes->sum('total_seats');
            $availCap = $movieShowtimes->sum('available_seats');
            $fillRate = $totalCap > 0 ? round((($totalCap - $availCap) / $totalCap) * 100, 1) : 0;

            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'image' => $movie->image,
                'tickets_sold' => $ticketsSold,
                'revenue' => $revenue,
                'fill_rate' => $fillRate,
            ];
        })->sortByDesc('tickets_sold')->values();

        // 4. MOST BOOKED MOVIE
        $mostBookedMovie = $movieReports->first();

        // 5. MOST POPULAR SHOWTIME
        $showtimeStats = Showtime::with(['movie', 'auditoriumDetail.cinema'])->get()->map(function ($st) {
            $booked = $st->total_seats - $st->available_seats;
            $fillRate = $st->total_seats > 0 ? round(($booked / $st->total_seats) * 100, 1) : 0;
            return [
                'id' => $st->id,
                'movie_title' => $st->movie ? $st->movie->title : 'Unknown',
                'cinema_hall' => $st->auditoriumDetail?.cinema?.name ? "{$st->auditoriumDetail->cinema->name} - {$st->auditoriumDetail->name}" : $st->auditorium,
                'start_time' => $st->start_time,
                'total_seats' => $st->total_seats,
                'available_seats' => $st->available_seats,
                'booked_seats' => $booked,
                'fill_rate' => $fillRate
            ];
        })->sortByDesc('fill_rate')->values();

        $mostPopularShowtime = $showtimeStats->first();

        // 6. REVENUE BY DATE (LAST 7 DAYS TREND)
        $revenueByDate = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $displayDate = $date->format('M d');

            $dayBookings = Booking::whereDate('created_at', $dateStr)
                ->where('status', '!=', 'cancelled')
                ->get();

            $revenueByDate[] = [
                'date' => $displayDate,
                'raw_date' => $dateStr,
                'revenue' => $dayBookings->sum('total_price'),
                'tickets' => $dayBookings->sum('seats_booked'),
            ];
        }

        // 7. SUMMARY STATS
        $totalMovies = Movie::count();
        $totalShowtimes = Showtime::count();
        $totalSeats = Showtime::sum('total_seats');
        $totalBookedSeats = Booking::where('status', '!=', 'cancelled')->sum('seats_booked');

        return response()->json([
            'today' => [
                'tickets_sold' => $todayTicketsSold,
                'revenue' => $todayRevenue,
                'movies_showing' => $showingMoviesCount,
                'registered_users' => $registeredUsersCount,
                'occupancy_rate' => $todayOccupancyRate,
            ],
            'summary' => [
                'total_movies' => $totalMovies,
                'total_showtimes' => $totalShowtimes,
                'total_seats' => $totalSeats,
                'booked_seats' => $totalBookedSeats,
                'overall_fill_rate' => $totalSeats > 0 ? round(($totalBookedSeats / $totalSeats) * 100, 1) : 0
            ],
            'reports' => [
                'most_booked_movie' => $mostBookedMovie,
                'most_popular_showtime' => $mostPopularShowtime,
                'revenue_by_movie' => $movieReports,
                'revenue_by_date' => $revenueByDate,
                'cancellations' => [
                    'total_cancelled' => $cancelledCount,
                    'refunded_amount' => $totalRefundedAmount,
                    'cancellation_rate' => $cancellationRate
                ]
            ],
            'movie_stats' => $movieReports,
            'showtime_stats' => $showtimeStats,
        ]);
    }
}

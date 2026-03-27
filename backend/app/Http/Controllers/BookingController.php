<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Store a new booking while preventing overbooking.
     */
    public function store(Request $request)
    {
        // Validate the request
        // Validate the request
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'string',
        ]);

        $seatsRequested = $validated['seat_numbers'];

        // Use a DB transaction to prevent race conditions
        return DB::transaction(function () use ($validated, $seatsRequested) {

            // Lock the movie row to prevent overbooking
            $movie = Movie::lockForUpdate()->find($validated['movie_id']);

            $seatsCount = count($seatsRequested);

            if ($movie->available_seats < $seatsCount) {
                return response()->json(['message' => 'Not enough seats'], 400);
            }

            // Check if any of the requested seats are already booked for this movie
            $existingBookings = Booking::where('movie_id', $movie->id)->get();
            $bookedSeats = [];
            foreach ($existingBookings as $b) {
                if ($b->seat_numbers) {
                    $bookedSeats = array_merge($bookedSeats, $b->seat_numbers);
                }
            }

            $overlappingSeats = array_intersect($seatsRequested, $bookedSeats);
            if (count($overlappingSeats) > 0) {
                return response()->json([
                    'message' => 'Some seats are already booked',
                    'unavailable_seats' => array_values($overlappingSeats)
                ], 400);
            }

            // Reduce available seats
            $movie->available_seats -= $seatsCount;
            $movie->save();

            // Create the booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'seats_booked' => $seatsCount,
                'seat_numbers' => $seatsRequested,
            ]);

            return response()->json($booking, 201);
        });
    }

    /**
     * Optionally, list bookings for the authenticated user.
     */
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())->with('movie')->get();
        return response()->json($bookings);
    }

    /**
     * Cancel a booking (optional).
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Restore seats
        DB::transaction(function () use ($booking) {
            $movie = Movie::lockForUpdate()->find($booking->movie_id);
            $movie->available_seats += $booking->seats_booked;
            $movie->save();

            $booking->delete();
        });

        return response()->json(['message' => 'Booking cancelled']);
    }

    /**
     * Get booked seats for a movie
     */
    public function getBookedSeats($movieId)
    {
        $existingBookings = Booking::where('movie_id', $movieId)->get();
        $bookedSeats = [];
        foreach ($existingBookings as $b) {
            if ($b->seat_numbers) {
                $bookedSeats = array_merge($bookedSeats, $b->seat_numbers);
            }
        }
        return response()->json(array_unique($bookedSeats));
    }
}   
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
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'seats_booked' => 'required|integer|min:1',
        ]);

        // Use a DB transaction to prevent race conditions
        return DB::transaction(function () use ($validated) {

            // Lock the movie row to prevent overbooking
            $movie = Movie::lockForUpdate()->find($validated['movie_id']);

            if ($movie->available_seats < $validated['seats_booked']) {
                return response()->json(['message' => 'Not enough seats'], 400);
            }

            // Reduce available seats
            $movie->available_seats -= $validated['seats_booked'];
            $movie->save();

            // Create the booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'seats_booked' => $validated['seats_booked'],
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
}
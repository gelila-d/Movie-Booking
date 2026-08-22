<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Store a new booking while preventing overbooking and calculating Birr totals.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'showtime_id' => 'nullable|exists:showtimes,id',
            'movie_id' => 'required_without:showtime_id|exists:movies,id',
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'string',
            'ticket_details' => 'nullable|array',
        ]);

        $seatsRequested = $validated['seat_numbers'];
        $seatsCount = count($seatsRequested);
        $ticketDetailsInput = $request->input('ticket_details', []);

        return DB::transaction(function () use ($request, $validated, $seatsRequested, $seatsCount, $ticketDetailsInput) {
            if ($request->filled('showtime_id')) {
                $showtime = Showtime::lockForUpdate()->find($validated['showtime_id']);

                if (!$showtime) {
                    return response()->json(['message' => 'Showtime not found'], 404);
                }

                if ($showtime->available_seats < $seatsCount) {
                    return response()->json(['message' => 'Not enough seats available for this showtime'], 400);
                }

                $existingBookings = Booking::where('showtime_id', $showtime->id)->get();
                $bookedSeats = [];
                foreach ($existingBookings as $b) {
                    if ($b->seat_numbers) {
                        $bookedSeats = array_merge($bookedSeats, $b->seat_numbers);
                    }
                }

                $overlappingSeats = array_intersect($seatsRequested, $bookedSeats);
                if (count($overlappingSeats) > 0) {
                    return response()->json([
                        'message' => 'Some selected seats are already booked for this showtime',
                        'unavailable_seats' => array_values($overlappingSeats)
                    ], 400);
                }

                $showtime->available_seats -= $seatsCount;
                $showtime->save();

                // Calculate total price in Birr from ticket details or default showtime base price
                $totalPrice = 0;
                $processedDetails = [];

                if (!empty($ticketDetailsInput) && is_array($ticketDetailsInput)) {
                    foreach ($ticketDetailsInput as $detail) {
                        $seatId = $detail['seat_id'] ?? null;
                        $price = floatval($detail['price'] ?? $showtime->price);
                        $type = $detail['type'] ?? 'Regular';
                        $totalPrice += $price;
                        $processedDetails[] = [
                            'seat_id' => $seatId,
                            'type' => $type,
                            'price' => $price,
                        ];
                    }
                } else {
                    $totalPrice = $seatsCount * ($showtime->price ?? 100);
                    foreach ($seatsRequested as $sId) {
                        $processedDetails[] = [
                            'seat_id' => $sId,
                            'type' => 'Regular',
                            'price' => floatval($showtime->price ?? 100),
                        ];
                    }
                }

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'movie_id' => $showtime->movie_id,
                    'showtime_id' => $showtime->id,
                    'seats_booked' => $seatsCount,
                    'seat_numbers' => $seatsRequested,
                    'ticket_details' => $processedDetails,
                    'total_price' => $totalPrice,
                ]);

                return response()->json($booking->load(['movie', 'showtime.auditoriumDetail.cinema']), 201);
            } else {
                $movie = Movie::lockForUpdate()->find($validated['movie_id']);

                if (!$movie) {
                    return response()->json(['message' => 'Movie not found'], 404);
                }

                if ($movie->available_seats < $seatsCount) {
                    return response()->json(['message' => 'Not enough seats'], 400);
                }

                $existingBookings = Booking::where('movie_id', $movie->id)->whereNull('showtime_id')->get();
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

                $movie->available_seats -= $seatsCount;
                $movie->save();

                $totalPrice = $seatsCount * 100.00; // Base 100 Birr

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'movie_id' => $movie->id,
                    'seats_booked' => $seatsCount,
                    'seat_numbers' => $seatsRequested,
                    'total_price' => $totalPrice,
                ]);

                return response()->json($booking->load('movie'), 201);
            }
        });
    }

    /**
     * List bookings for the authenticated user.
     */
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['movie', 'showtime.auditoriumDetail.cinema'])
            ->latest()
            ->get();
        return response()->json($bookings);
    }

    /**
     * Cancel a booking.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($booking) {
            if ($booking->showtime_id) {
                $showtime = Showtime::lockForUpdate()->find($booking->showtime_id);
                if ($showtime) {
                    $showtime->available_seats += $booking->seats_booked;
                    $showtime->save();
                }
            } else if ($booking->movie_id) {
                $movie = Movie::lockForUpdate()->find($booking->movie_id);
                if ($movie) {
                    $movie->available_seats += $booking->seats_booked;
                    $movie->save();
                }
            }

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
        return response()->json(array_values(array_unique($bookedSeats)));
    }

    /**
     * Get all bookings (Admin only)
     */
    public function allBookings(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $bookings = Booking::with(['user', 'movie', 'showtime.auditoriumDetail.cinema'])->latest()->get();
        return response()->json($bookings);
    }
}
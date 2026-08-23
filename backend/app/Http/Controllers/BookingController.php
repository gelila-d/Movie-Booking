<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Store a new booking with payment status and transaction reference.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'showtime_id' => 'nullable|exists:showtimes,id',
            'movie_id' => 'required_without:showtime_id|exists:movies,id',
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'string',
            'ticket_details' => 'nullable|array',
            'payment_method' => 'nullable|string|in:telebirr,cbe_birr,chapa,boa,card',
        ]);

        $seatsRequested = $validated['seat_numbers'];
        $seatsCount = count($seatsRequested);
        $ticketDetailsInput = $request->input('ticket_details', []);
        $paymentMethod = $validated['payment_method'] ?? 'telebirr';

        // Generate a realistic transaction reference code based on payment method
        $prefix = match($paymentMethod) {
            'telebirr' => 'TB',
            'cbe_birr' => 'CBE',
            'chapa' => 'CHP',
            'boa' => 'BOA',
            default => 'TXN'
        };
        $transactionRef = $prefix . '-' . strtoupper(Str::random(4)) . rand(1000, 9999);

        return DB::transaction(function () use ($request, $validated, $seatsRequested, $seatsCount, $ticketDetailsInput, $paymentMethod, $transactionRef) {
            if ($request->filled('showtime_id')) {
                $showtime = Showtime::lockForUpdate()->find($validated['showtime_id']);

                if (!$showtime) {
                    return response()->json(['message' => 'Showtime not found'], 404);
                }

                // Security Rule: Prevent booking expired or started showtimes
                if ($showtime->start_time && Carbon::parse($showtime->start_time)->lessThanOrEqualTo(Carbon::now())) {
                    return response()->json([
                        'message' => 'Booking is closed for this showtime because it has already started or ended.'
                    ], 422);
                }

                if ($showtime->available_seats < $seatsCount) {
                    return response()->json(['message' => 'Not enough seats available for this showtime'], 400);
                }

                // Check collision only against active confirmed bookings
                $existingBookings = Booking::where('showtime_id', $showtime->id)
                    ->where('status', '!=', 'cancelled')
                    ->get();

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

                // Calculate total price in Birr
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
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'transaction_ref' => $transactionRef,
                    'status' => 'confirmed',
                    'refund_status' => 'none',
                ]);

                // Create database-backed BookingSeat records with unique constraint enforcement
                foreach ($processedDetails as $pd) {
                    $seatLabel = $pd['seat_id'];
                    $seatObj = \App\Models\Seat::where('auditorium_id', $showtime->auditorium_id)
                        ->where('seat_label', $seatLabel)
                        ->first();

                    if ($seatObj) {
                        \App\Models\BookingSeat::create([
                            'booking_id' => $booking->id,
                            'showtime_id' => $showtime->id,
                            'seat_id' => $seatObj->id,
                            'ticket_type' => strtolower($pd['type']),
                            'price' => $pd['price'],
                        ]);
                    }
                }

                return response()->json($booking->load(['movie', 'showtime.auditoriumDetail.cinema']), 201);
            } else {
                $movie = Movie::lockForUpdate()->find($validated['movie_id']);

                if (!$movie) {
                    return response()->json(['message' => 'Movie not found'], 404);
                }

                if ($movie->available_seats < $seatsCount) {
                    return response()->json(['message' => 'Not enough seats'], 400);
                }

                $existingBookings = Booking::where('movie_id', $movie->id)
                    ->whereNull('showtime_id')
                    ->where('status', '!=', 'cancelled')
                    ->get();

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

                $totalPrice = $seatsCount * 100.00;

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'movie_id' => $movie->id,
                    'seats_booked' => $seatsCount,
                    'seat_numbers' => $seatsRequested,
                    'total_price' => $totalPrice,
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'transaction_ref' => $transactionRef,
                    'status' => 'confirmed',
                    'refund_status' => 'none',
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
     * Cancel a booking with 2-hour cutoff validation, seat restoration, and refund processing.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'This ticket booking has already been cancelled.'], 400);
        }

        // Determine showtime start time for 2-hour cutoff rule
        $startTime = null;
        if ($booking->showtime_id && $booking->showtime) {
            $startTime = Carbon::parse($booking->showtime->start_time);
        } elseif ($booking->movie_id && $booking->movie && $booking->movie->show_time) {
            $startTime = Carbon::parse($booking->movie->show_time);
        }

        if ($startTime) {
            $now = Carbon::now();
            if ($now->greaterThanOrEqualTo($startTime)) {
                return response()->json([
                    'message' => 'Cannot cancel ticket for a showtime that has already started or passed.'
                ], 422);
            }

            $minutesUntilShow = $now->diffInMinutes($startTime, false);
            if ($minutesUntilShow < 120) {
                $hoursLeft = round($minutesUntilShow / 60, 1);
                return response()->json([
                    'message' => "Cancellations and refunds are only permitted at least 2 hours before showtime. Your showtime starts in {$minutesUntilShow} minutes ({$hoursLeft} hours left)."
                ], 422);
            }
        }

        return DB::transaction(function () use ($booking) {
            // 1. Restore seat availability
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

            // 2. Issue 100% Refund Simulation & Record Audit History
            $refundRef = 'REF-' + strtoupper(Str::random(4)) . rand(1000, 9999);
            $refundAmount = $booking->total_price ?? 0;

            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
                'refund_status' => 'full_refund',
                'refund_amount' => $refundAmount,
                'refund_ref' => $refundRef,
                'cancelled_at' => Carbon::now(),
            ]);

            return response()->json([
                'message' => "Ticket booking cancelled successfully! A full refund of {$refundAmount} ETB has been issued via {$booking->payment_method} (Ref: {$refundRef}).",
                'booking' => $booking->fresh(['movie', 'showtime.auditoriumDetail.cinema'])
            ]);
        });
    }

    /**
     * Get booked seats for a movie excluding cancelled reservations
     */
    public function getBookedSeats($movieId)
    {
        $existingBookings = Booking::where('movie_id', $movieId)
            ->where('status', '!=', 'cancelled')
            ->get();

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
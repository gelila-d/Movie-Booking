<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Booking;
use App\Models\Auditorium;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShowtimeController extends Controller
{
    /**
     * Display a listing of showtimes.
     */
    public function index(Request $request)
    {
        $query = Showtime::with(['movie', 'auditoriumDetail.cinema'])->orderBy('start_time', 'asc');

        if ($request->has('movie_id')) {
            $query->where('movie_id', $request->query('movie_id'));
        }

        if (!$request->user() || !$request->user()->is_admin) {
            $query->where('start_time', '>=', Carbon::now()->subHours(2));
        }

        return response()->json($query->get());
    }

    /**
     * Display specified showtime.
     */
    public function show(Showtime $showtime)
    {
        return response()->json($showtime->load(['movie', 'auditoriumDetail.cinema']));
    }

    /**
     * Store a newly created showtime (Admin only).
     */
    public function store(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'auditorium_id' => 'nullable|exists:auditoriums,id',
            'auditorium' => 'required_without:auditorium_id|nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
            'vip_price' => 'nullable|numeric|min:0',
            'student_price' => 'nullable|numeric|min:0',
            'child_price' => 'nullable|numeric|min:0',
            'total_seats' => 'nullable|integer|min:1',
        ]);

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = Carbon::parse($validated['end_time']);

        if (!empty($validated['auditorium_id'])) {
            $auditoriumModel = Auditorium::with('cinema')->find($validated['auditorium_id']);
            $auditoriumName = $auditoriumModel->cinema ? "{$auditoriumModel->cinema->name} - {$auditoriumModel->name}" : $auditoriumModel->name;
            $totalSeats = $auditoriumModel->total_seats;
            $auditoriumId = $auditoriumModel->id;
        } else {
            $auditoriumName = trim($validated['auditorium']);
            $totalSeats = $validated['total_seats'] ?? 50;
            $auditoriumId = null;
        }

        // Prevent overlapping showtimes in the same auditorium
        $overlapQuery = Showtime::query();
        if ($auditoriumId) {
            $overlapQuery->where('auditorium_id', $auditoriumId);
        } else {
            $overlapQuery->where('auditorium', $auditoriumName);
        }

        $overlap = $overlapQuery->where(function ($query) use ($startTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })->first();

        if ($overlap) {
            $existingMovie = $overlap->movie ? $overlap->movie->title : 'Another show';
            return response()->json([
                'message' => "Overlapping showtime detected! Auditorium '{$auditoriumName}' is already scheduled for '{$existingMovie}' from " . 
                             Carbon::parse($overlap->start_time)->format('Y-m-d H:i') . " to " . 
                             Carbon::parse($overlap->end_time)->format('H:i') . "."
            ], 422);
        }

        $validated['auditorium'] = $auditoriumName;
        $validated['total_seats'] = $totalSeats;
        $validated['available_seats'] = $totalSeats;

        if (empty($validated['vip_price'])) {
            $validated['vip_price'] = $validated['price'] * 1.5;
        }
        if (empty($validated['student_price'])) {
            $validated['student_price'] = max(10, $validated['price'] * 0.8);
        }
        if (empty($validated['child_price'])) {
            $validated['child_price'] = max(10, $validated['price'] * 0.6);
        }

        $showtime = Showtime::create($validated);

        return response()->json($showtime->load(['movie', 'auditoriumDetail.cinema']), 201);
    }

    /**
     * Update specified showtime (Admin only).
     */
    public function update(Request $request, Showtime $showtime)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'movie_id' => 'sometimes|exists:movies,id',
            'auditorium_id' => 'nullable|exists:auditoriums,id',
            'auditorium' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'price' => 'sometimes|numeric|min:0',
            'vip_price' => 'sometimes|numeric|min:0',
            'student_price' => 'sometimes|numeric|min:0',
            'child_price' => 'sometimes|numeric|min:0',
            'total_seats' => 'sometimes|integer|min:1',
        ]);

        $startTime = isset($validated['start_time']) ? Carbon::parse($validated['start_time']) : $showtime->start_time;
        $endTime = isset($validated['end_time']) ? Carbon::parse($validated['end_time']) : $showtime->end_time;
        $auditoriumId = isset($validated['auditorium_id']) ? $validated['auditorium_id'] : $showtime->auditorium_id;

        if ($auditoriumId) {
            $auditoriumModel = Auditorium::with('cinema')->find($auditoriumId);
            $auditoriumName = $auditoriumModel->cinema ? "{$auditoriumModel->cinema->name} - {$auditoriumModel->name}" : $auditoriumModel->name;
            $validated['total_seats'] = $auditoriumModel->total_seats;
            $validated['auditorium'] = $auditoriumName;
        } else {
            $auditoriumName = isset($validated['auditorium']) ? trim($validated['auditorium']) : $showtime->auditorium;
        }

        // Check overlapping showtimes in the same auditorium excluding current showtime
        $overlapQuery = Showtime::where('id', '!=', $showtime->id);
        if ($auditoriumId) {
            $overlapQuery->where('auditorium_id', $auditoriumId);
        } else {
            $overlapQuery->where('auditorium', $auditoriumName);
        }

        $overlap = $overlapQuery->where(function ($query) use ($startTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })->first();

        if ($overlap) {
            $existingMovie = $overlap->movie ? $overlap->movie->title : 'Another show';
            return response()->json([
                'message' => "Overlapping showtime detected! Auditorium '{$auditoriumName}' is already scheduled for '{$existingMovie}' from " . 
                             Carbon::parse($overlap->start_time)->format('Y-m-d H:i') . " to " . 
                             Carbon::parse($overlap->end_time)->format('H:i') . "."
            ], 422);
        }

        if (isset($validated['total_seats']) && $validated['total_seats'] != $showtime->total_seats) {
            $diff = $validated['total_seats'] - $showtime->total_seats;
            $newAvailable = $showtime->available_seats + $diff;
            if ($newAvailable < 0) {
                return response()->json([
                    'message' => 'Total seats cannot be less than the number of seats already booked.'
                ], 422);
            }
            $validated['available_seats'] = $newAvailable;
        }

        $showtime->update($validated);

        return response()->json($showtime->load(['movie', 'auditoriumDetail.cinema']));
    }

    /**
     * Remove specified showtime (Admin only).
     */
    public function destroy(Request $request, Showtime $showtime)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $showtime->delete();

        return response()->json(['message' => 'Showtime deleted successfully']);
    }

    /**
     * Get list of booked seats for a specific showtime.
     */
    public function getBookedSeats(Showtime $showtime)
    {
        $existingBookings = Booking::where('showtime_id', $showtime->id)
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
}

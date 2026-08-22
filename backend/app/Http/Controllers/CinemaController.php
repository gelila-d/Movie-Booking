<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Auditorium;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    /**
     * Display listing of cinemas with auditoriums.
     */
    public function index()
    {
        $cinemas = Cinema::with('auditoriums')->orderBy('name', 'asc')->get();
        return response()->json($cinemas);
    }

    /**
     * Store a new cinema (Admin only).
     */
    public function storeCinema(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $cinema = Cinema::create($validated);
        return response()->json($cinema->load('auditoriums'), 201);
    }

    /**
     * Update a cinema (Admin only).
     */
    public function updateCinema(Request $request, Cinema $cinema)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $cinema->update($validated);
        return response()->json($cinema->load('auditoriums'));
    }

    /**
     * Delete a cinema (Admin only).
     */
    public function deleteCinema(Request $request, Cinema $cinema)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $cinema->delete();
        return response()->json(['message' => 'Cinema deleted successfully']);
    }

    /**
     * Store a new auditorium under a cinema (Admin only).
     */
    public function storeAuditorium(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:255',
            'rows_count' => 'required|integer|min:1|max:26', // A to Z
            'seats_per_row' => 'required|integer|min:1|max:30',
        ]);

        $validated['total_seats'] = $validated['rows_count'] * $validated['seats_per_row'];

        $auditorium = Auditorium::create($validated);
        return response()->json($auditorium->load('cinema'), 201);
    }

    /**
     * Update an auditorium (Admin only).
     */
    public function updateAuditorium(Request $request, Auditorium $auditorium)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'cinema_id' => 'sometimes|exists:cinemas,id',
            'name' => 'sometimes|string|max:255',
            'rows_count' => 'sometimes|integer|min:1|max:26',
            'seats_per_row' => 'sometimes|integer|min:1|max:30',
        ]);

        if (isset($validated['rows_count']) || isset($validated['seats_per_row'])) {
            $rows = $validated['rows_count'] ?? $auditorium->rows_count;
            $seats = $validated['seats_per_row'] ?? $auditorium->seats_per_row;
            $validated['total_seats'] = $rows * $seats;
        }

        $auditorium->update($validated);
        return response()->json($auditorium->load('cinema'));
    }

    /**
     * Delete an auditorium (Admin only).
     */
    public function deleteAuditorium(Request $request, Auditorium $auditorium)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $auditorium->delete();
        return response()->json(['message' => 'Auditorium deleted successfully']);
    }
}

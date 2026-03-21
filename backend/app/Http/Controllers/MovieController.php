<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Show all movies (Everyone can see)
     */
    public function index()
    {
        return response()->json(Movie::all());
    }

    /**
     * Create movie (Admin only)
     */
    public function store(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'show_time' => 'required|date',
            'total_seats' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['available_seats'] = $validated['total_seats'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('movies', 'public');
            $validated['image'] = $path;
        }

        $movie = Movie::create($validated);

        return response()->json($movie, 201);
    }

    /**
     * Update movie (Admin only)
     */
    public function update(Request $request, Movie $movie)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'show_time' => 'sometimes|date',
            'total_seats' => 'sometimes|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($movie->image) {
                Storage::disk('public')->delete($movie->image);
            }
            $path = $request->file('image')->store('movies', 'public');
            $validated['image'] = $path;
        }

        if (isset($validated['total_seats']) && $validated['total_seats'] != $movie->total_seats) {
            $diff = $validated['total_seats'] - $movie->total_seats;
            $validated['available_seats'] = $movie->available_seats + $diff;
            
            if ($validated['available_seats'] < 0) {
                return response()->json([
                    'errors' => ['total_seats' => ['Total seats cannot be less than seats already booked.']]
                ], 422);
            }
        }

        $movie->update($validated);

        return response()->json($movie);
    }

    /**
     * Delete movie (Admin only)
     */
    public function destroy(Request $request, Movie $movie)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($movie->image) {
            Storage::disk('public')->delete($movie->image);
        }

        $movie->delete();

        return response()->json(['message' => 'Movie deleted']);
    }
}
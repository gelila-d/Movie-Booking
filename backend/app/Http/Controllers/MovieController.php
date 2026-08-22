<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    /**
     * Show all movies (Admins see all draft/published movies; regular users see only published).
     */
    public function index(Request $request)
    {
        $query = Movie::query();

        if (!$request->user() || !$request->user()->is_admin) {
            $query->where('is_published', true);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Show a single movie
     */
    public function show(Movie $movie)
    {
        return response()->json($movie);
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
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'rating' => 'nullable|string|max:50',
            'trailer_url' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
            'show_time' => 'nullable|date',
            'total_seats' => 'nullable|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['total_seats'] = $validated['total_seats'] ?? 50;
        $validated['available_seats'] = $validated['total_seats'];
        $validated['is_published'] = filter_var($request->input('is_published', true), FILTER_VALIDATE_BOOLEAN);

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
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'rating' => 'nullable|string|max:50',
            'trailer_url' => 'nullable|string|max:500',
            'is_published' => 'nullable|boolean',
            'show_time' => 'nullable|date',
            'total_seats' => 'sometimes|integer|min:1',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->has('is_published')) {
            $validated['is_published'] = filter_var($request->input('is_published'), FILTER_VALIDATE_BOOLEAN);
        }

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
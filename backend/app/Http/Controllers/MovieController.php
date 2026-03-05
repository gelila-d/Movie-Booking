<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

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
        ]);

        $validated['available_seats'] = $validated['total_seats'];

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
            'description' => 'sometimes|string',
            'show_time' => 'sometimes|date',
            'total_seats' => 'sometimes|integer|min:1',
        ]);

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

        $movie->delete();

        return response()->json(['message' => 'Movie deleted']);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of all movies.
     */
    public function index()
    {
        return Movie::all();
    }

    /**
     * Store a newly created movie (Admin only - User ID 1).
     */
    public function store(Request $request)
    {
        // Simple admin check
        if ($request->user()->id !== 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate request (optional but recommended)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_year' => 'nullable|integer',
            'rating' => 'nullable|string|max:10',
            'genre' => 'nullable|string|max:50',
        ]);

        $movie = Movie::create($validated);

        return response()->json($movie, 201);
    }

    /**
     * Update the specified movie.
     */
    public function update(Request $request, Movie $movie)
    {
        // Validate request (optional)
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'release_year' => 'sometimes|integer',
            'rating' => 'sometimes|string|max:10',
            'genre' => 'sometimes|string|max:50',
        ]);

        $movie->update($validated);

        return response()->json($movie);
    }

    /**
     * Remove the specified movie.
     */
    public function destroy(Movie $movie)
    {
        $movie->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
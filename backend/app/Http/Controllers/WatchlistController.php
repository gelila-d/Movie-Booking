<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Movie;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * Get list of watchlisted movies for authenticated user.
     */
    public function index(Request $request)
    {
        $watchlists = Watchlist::where('user_id', auth()->id())
            ->with('movie')
            ->latest()
            ->get();

        $movies = $watchlists->pluck('movie')->filter();
        return response()->json($movies);
    }

    /**
     * Get list of watchlisted movie IDs for fast heart icon toggling.
     */
    public function ids(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        $ids = Watchlist::where('user_id', auth()->id())
            ->pluck('movie_id');

        return response()->json($ids);
    }

    /**
     * Toggle movie in user's watchlist (Add or Remove).
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $userId = auth()->id();
        $movieId = $validated['movie_id'];

        $existing = Watchlist::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Removed from watchlist',
                'status' => 'removed',
                'movie_id' => $movieId
            ]);
        } else {
            Watchlist::create([
                'user_id' => $userId,
                'movie_id' => $movieId
            ]);
            return response()->json([
                'message' => 'Added to watchlist',
                'status' => 'added',
                'movie_id' => $movieId
            ]);
        }
    }

    /**
     * Remove specified movie from watchlist.
     */
    public function destroy($movieId)
    {
        $deleted = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $movieId)
            ->delete();

        return response()->json([
            'message' => 'Removed from watchlist',
            'movie_id' => (int)$movieId
        ]);
    }
}

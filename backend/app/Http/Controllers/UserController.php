<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users (Admin only).
     */
    public function index(Request $request)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $users = User::withCount('bookings')
            ->latest()
            ->get();

        return response()->json($users);
    }

    /**
     * Toggle user active status (Disable/Enable).
     */
    public function toggleActive(Request $request, User $user)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot disable your own admin account.'], 422);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => $user->is_active ? "User {$user->name} has been enabled." : "User {$user->name} has been disabled.",
            'user' => $user
        ]);
    }

    /**
     * Toggle user admin role (Grant/Revoke Admin).
     */
    public function toggleAdmin(Request $request, User $user)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot revoke your own admin status.'], 422);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return response()->json([
            'message' => $user->is_admin ? "Admin privileges granted to {$user->name}." : "Admin privileges revoked from {$user->name}.",
            'user' => $user
        ]);
    }

    /**
     * View booking history for a specific user.
     */
    public function userBookings(Request $request, User $user)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $bookings = Booking::where('user_id', $user->id)
            ->with(['movie', 'showtime.auditoriumDetail.cinema'])
            ->latest()
            ->get();

        return response()->json([
            'user' => $user,
            'bookings' => $bookings
        ]);
    }
}

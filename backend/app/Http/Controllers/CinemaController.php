<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Auditorium;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CinemaController extends Controller
{
    /**
     * Display listing of cinemas with auditoriums.
     */
    public function index()
    {
        $cinemas = Cinema::with('auditoriums.seats')->orderBy('name', 'asc')->get();
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
     * Store a new auditorium under a cinema and generate database Seat records (Admin only).
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
            'vip_rows_count' => 'nullable|integer|min:0|max:10',
            'base_price' => 'nullable|numeric|min:50',
        ]);

        $validated['total_seats'] = $validated['rows_count'] * $validated['seats_per_row'];
        $validated['vip_rows_count'] = $validated['vip_rows_count'] ?? 2;
        $validated['base_price'] = $validated['base_price'] ?? 100.00;

        $auditorium = Auditorium::create($validated);

        // Auto-generate physical Seat database records
        $this->generateSeatsForAuditorium($auditorium);

        return response()->json($auditorium->load(['cinema', 'seats']), 201);
    }

    /**
     * Update an auditorium and regenerate Seat records (Admin only).
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
            'vip_rows_count' => 'nullable|integer|min:0|max:10',
            'base_price' => 'nullable|numeric|min:50',
        ]);

        $rebuildSeats = false;
        if (isset($validated['rows_count']) || isset($validated['seats_per_row']) || isset($validated['vip_rows_count'])) {
            $rows = $validated['rows_count'] ?? $auditorium->rows_count;
            $seats = $validated['seats_per_row'] ?? $auditorium->seats_per_row;
            $validated['total_seats'] = $rows * $seats;
            $rebuildSeats = true;
        }

        $auditorium->update($validated);

        if ($rebuildSeats) {
            $this->generateSeatsForAuditorium($auditorium);
        }

        return response()->json($auditorium->load(['cinema', 'seats']));
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

    /**
     * Get database-backed seats for an auditorium.
     */
    public function getAuditoriumSeats(Auditorium $auditorium)
    {
        // If auditorium has no seats generated yet, generate them now
        if ($auditorium->seats()->count() === 0) {
            $this->generateSeatsForAuditorium($auditorium);
        }

        return response()->json($auditorium->seats()->orderBy('row_letter')->orderBy('seat_number')->get());
    }

    /**
     * Toggle seat maintenance status (Admin only).
     */
    public function toggleSeatMaintenance(Request $request, Seat $seat)
    {
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $seat->status = ($seat->status === 'maintenance') ? 'active' : 'maintenance';
        $seat->save();

        return response()->json([
            'message' => "Seat {$seat->seat_label} status set to {$seat->status}.",
            'seat' => $seat
        ]);
    }

    /**
     * Helper: Auto-generate physical Seat database entities for an Auditorium.
     */
    private function generateSeatsForAuditorium(Auditorium $auditorium)
    {
        DB::transaction(function () use ($auditorium) {
            // Remove old unbooked seats if updating layout
            Seat::where('auditorium_id', $auditorium->id)->delete();

            $rows = $auditorium->rows_count;
            $seatsPerRow = $auditorium->seats_per_row;
            $vipRowsCount = $auditorium->vip_rows_count ?? 2;

            $alphabet = range('A', 'Z');
            $newSeats = [];

            for ($r = 0; $r < $rows; $r++) {
                $rowLetter = $alphabet[$r];
                // Determine if this row is VIP (top N rows starting from Row A or from top)
                $isVip = ($r < $vipRowsCount);

                for ($s = 1; $s <= $seatsPerRow; $s++) {
                    $seatLabel = $rowLetter . $s;

                    // Row A seats 1-2 designated as Wheelchair-accessible ♿
                    $type = 'regular';
                    if ($isVip) {
                        $type = 'vip';
                    } elseif ($r === $rows - 1 && ($s === 1 || $s === 2)) {
                        $type = 'accessible';
                    }

                    $newSeats[] = [
                        'auditorium_id' => $auditorium->id,
                        'row_letter' => $rowLetter,
                        'seat_number' => $s,
                        'seat_label' => $seatLabel,
                        'type' => $type,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Seat::insert($newSeats);
        });
    }
}

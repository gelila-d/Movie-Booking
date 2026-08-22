<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\AdminStatsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{movie}', [MovieController::class, 'show']);
Route::get('/movies/{movie}/booked-seats', [BookingController::class, 'getBookedSeats']);

// Public Showtime & Cinema Routes
Route::get('/cinemas', [CinemaController::class, 'index']);
Route::get('/showtimes', [ShowtimeController::class, 'index']);
Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show']);
Route::get('/showtimes/{showtime}/booked-seats', [ShowtimeController::class, 'getBookedSeats']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin Movie Management
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{movie}', [MovieController::class, 'update']);
    Route::delete('/movies/{movie}', [MovieController::class, 'destroy']);

    // Admin Cinema & Auditorium Management
    Route::post('/cinemas', [CinemaController::class, 'storeCinema']);
    Route::put('/cinemas/{cinema}', [CinemaController::class, 'updateCinema']);
    Route::delete('/cinemas/{cinema}', [CinemaController::class, 'deleteCinema']);
    Route::post('/auditoriums', [CinemaController::class, 'storeAuditorium']);
    Route::put('/auditoriums/{auditorium}', [CinemaController::class, 'updateAuditorium']);
    Route::delete('/auditoriums/{auditorium}', [CinemaController::class, 'deleteAuditorium']);

    // Admin Showtime Management
    Route::post('/showtimes', [ShowtimeController::class, 'store']);
    Route::put('/showtimes/{showtime}', [ShowtimeController::class, 'update']);
    Route::delete('/showtimes/{showtime}', [ShowtimeController::class, 'destroy']);

    // Booking Routes
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::get('/my-bookings', [BookingController::class, 'index']);

    // Admin Stats & Bookings Audit
    Route::get('/admin/stats', [AdminStatsController::class, 'index']);
    Route::get('/admin/bookings', [BookingController::class, 'allBookings']);
});
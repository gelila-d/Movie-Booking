<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Movie;
use App\Models\Booking;
use App\Models\User;

echo "Movies count: " . Movie::count() . "\n";
echo "Bookings count: " . Booking::count() . "\n";
echo "Users count: " . User::count() . "\n";

echo "Users:\n";
foreach (User::all() as $user) {
    echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Is Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
}

if (Movie::count() > 0) {
    echo "First Movie: " . Movie::first()->title . "\n";
}
if (Booking::count() > 0) {
    echo "Bookings:\n";
    foreach (Booking::all() as $booking) {
        echo "- ID: {$booking->id}, User ID: {$booking->user_id}, Movie ID: {$booking->movie_id}, Seats: {$booking->seats_booked}\n";
    }
}

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

echo "Movies:\n";
foreach (Movie::all() as $movie) {
    echo "- ID: {$movie->id}, Title: {$movie->title}, Description: {$movie->description}, Image: {$movie->image}, ShowTime: {$movie->show_time}\n";
}

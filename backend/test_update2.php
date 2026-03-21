<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$movie = App\Models\Movie::first();

// Assuming admin user exists and is ID 1
Auth::loginUsingId(1);

$request = Illuminate\Http\Request::create('/api/movies/'.$movie->id, 'POST', [
    '_method' => 'PUT',
    'title' => 'Updated Title',
    'description' => 'Updated Description',
    'show_time' => '2026-01-01T10:00',
    'total_seats' => 150
]);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
echo "Status: " . $response->status() . "\n";
echo "Content: " . $response->getContent() . "\n";

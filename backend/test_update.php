<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/movies/1', 'POST', [
    '_method' => 'PUT',
    'title' => 'Updated Title',
    'description' => 'Updated Desc',
    'show_time' => '2025-06-12T10:00',
    'total_seats' => 150
]);
$request->headers->set('Accept', 'application/json');

// We need a user to bypass auth
$user = App\Models\User::where('is_admin', true)->first();
if ($user) {
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
}
$app->instance('request', $request);

$response = $kernel->handle($request);
echo "Status: " . $response->status() . "\n";
echo "Content: " . $response->getContent() . "\n";

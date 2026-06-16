<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$room = \App\Models\Room::first();
echo "Room UUID: " . $room->uuid . "\n";
echo "Current Status: " . $room->status . "\n";

$room->update(['status' => 'active', 'started_at' => now()]);
echo "Set to active\n";

$request = \Illuminate\Http\Request::create("/rooms/{$room->uuid}/pause-request", 'POST');
// Mock auth
$user = \App\Models\User::find($room->party_a_id);
\Illuminate\Support\Facades\Auth::login($user);

$controller = new \App\Http\Controllers\ChatController();
$response = $controller->requestPause($request, $room->uuid);

echo "Response: " . $response->getContent() . "\n";
echo "New Status: " . $room->fresh()->status . "\n";

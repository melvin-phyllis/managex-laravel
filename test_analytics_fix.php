<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Presence;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

echo "Clearing cache...\n";
Cache::forget('analytics_activities');

// Create a presence with null check_in if one doesn't exist to provoke error if fixed fails
$user = User::first();
if ($user) {
    if (!Presence::whereNull('check_in')->exists()) {
        echo "Creating test presence with null check_in...\n";
        Presence::create([
            'user_id' => $user->id,
            'date' => now(),
            'check_in' => null,
            'notes' => 'Test null check_in',
        ]);
    } else {
        echo "Presence with null check_in already exists.\n";
    }
} else {
    echo "No user found.\n";
}

echo "Testing AnalyticsController::getRecentActivities...\n";
try {
    $controller = new \App\Http\Controllers\Admin\AnalyticsController();
    $request = new \Illuminate\Http\Request();
    
    // We need to resolve dependencies if constructor has any, but AnalyticsController seems to have none or standard ones.
    // Better to use handle if we were routing... but direct instantiation is fine if no deps.
    // Wait, middleware won't run, but the method logic will.

    $response = $controller->getRecentActivities($request);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() === 200) {
        $content = json_decode($response->getContent(), true);
        echo "Response returned " . count($content) . " items.\n";
        echo "Items:\n";
        foreach ($content as $item) {
             echo " - " . ($item['description'] ?? 'No description') . "\n";
        }
        echo "SUCCESS: Method executed without error.\n";
    } else {
        echo "FAILURE: Response status " . $response->getStatusCode() . "\n";
    }

} catch (\Throwable $e) {
    echo "CRASH: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

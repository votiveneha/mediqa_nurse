<?php
// Test broadcast configuration
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Broadcast Configuration Test ===\n\n";

echo "BROADCAST_DRIVER: " . config('broadcasting.default') . "\n";
echo "Pusher App ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
echo "Pusher Key: " . config('broadcasting.connections.pusher.key') . "\n";
echo "Pusher Secret: " . substr(config('broadcasting.connections.pusher.secret'), 0, 8) . "...\n";
echo "Pusher Cluster: " . config('broadcasting.connections.pusher.options.host') . "\n";
echo "\n";

// Check if .env matches config
echo "=== .env Values ===\n";
echo "PUSHER_APP_ID: " . ($_ENV['PUSHER_APP_ID'] ?? 'NOT SET') . "\n";
echo "PUSHER_APP_KEY: " . ($_ENV['PUSHER_APP_KEY'] ?? 'NOT SET') . "\n";
echo "PUSHER_APP_CLUSTER: " . ($_ENV['PUSHER_APP_CLUSTER'] ?? 'NOT SET') . "\n";
echo "\n";

// Test Pusher connection
try {
    $pusher = app('pusher');
    echo "✅ Pusher instance created successfully\n";
    echo "Pusher connection info:\n";
    echo "  - Host: " . $pusher->getSettings()['host'] . "\n";
    echo "  - Port: " . $pusher->getSettings()['port'] . "\n";
    echo "  - Scheme: " . $pusher->getSettings()['scheme'] . "\n";
} catch (\Exception $e) {
    echo "❌ Error creating Pusher instance: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>

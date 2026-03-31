<?php
/**
 * Simple Online Status Test
 * Tests if online status endpoint is working
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

header('Content-Type: application/json');

// Simulate nurse authentication
$nurseId = 1; // Change this to test with different user

// Test setting online status
Cache::set("user_{$nurseId}_online", true, now()->addMinutes(5));
$online = Cache::get("user_{$nurseId}_online", false);

echo json_encode([
    'user_id' => $nurseId,
    'is_online' => $online,
    'cache_key' => "user_{$nurseId}_online",
    'cache_ttl' => Cache::get("user_{$nurseId}_online") ? '5 minutes' : 'expired',
    'timestamp' => now()->toIso8601String()
], JSON_PRETTY_PRINT);

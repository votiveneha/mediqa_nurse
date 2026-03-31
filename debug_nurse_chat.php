<?php
/**
 * Debug Nurse Chat Broadcasting
 * This file helps diagnose issues with nurse chat real-time messaging
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Config;
use App\Models\Conversation;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nurse Chat Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>🔍 Nurse Chat Broadcasting Debug</h1>

    <?php
    // Test 1: Check Pusher Configuration
    echo '<div class="debug-box">';
    echo '<h2>1. Pusher Configuration</h2>';
    
    $pusherKey = Config::get('broadcasting.connections.pusher.key');
    $pusherSecret = Config::get('broadcasting.connections.pusher.secret');
    $pusherAppId = Config::get('broadcasting.connections.pusher.app_id');
    $pusherCluster = Config::get('broadcasting.connections.pusher.options.cluster') ?? 'mt1';
    $broadcastDriver = Config::get('broadcasting.default');
    
    echo '<table>';
    echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
    echo '<tr><td>Broadcast Driver</td><td>' . htmlspecialchars($broadcastDriver) . '</td><td>';
    echo $broadcastDriver === 'pusher' ? '<span class="success">✓ Correct</span>' : '<span class="error">✗ Should be "pusher"</span>';
    echo '</td></tr>';
    
    echo '<tr><td>PUSHER_APP_KEY</td><td>' . htmlspecialchars($pusherKey) . '</td><td>';
    echo !empty($pusherKey) ? '<span class="success">✓ Set</span>' : '<span class="error">✗ Not set</span>';
    echo '</td></tr>';
    
    echo '<tr><td>PUSHER_APP_SECRET</td><td>' . htmlspecialchars(substr($pusherSecret, 0, 10) . '...') . '</td><td>';
    echo !empty($pusherSecret) ? '<span class="success">✓ Set</span>' : '<span class="error">✗ Not set</span>';
    echo '</td></tr>';
    
    echo '<tr><td>PUSHER_APP_ID</td><td>' . htmlspecialchars($pusherAppId) . '</td><td>';
    echo !empty($pusherAppId) ? '<span class="success">✓ Set</span>' : '<span class="error">✗ Not set</span>';
    echo '</td></tr>';
    
    echo '<tr><td>PUSHER_CLUSTER</td><td>' . htmlspecialchars($pusherCluster) . '</td><td>';
    echo !empty($pusherCluster) ? '<span class="success">✓ Set</span>' : '<span class="error">✗ Not set</span>';
    echo '</td></tr>';
    echo '</table>';
    echo '</div>';

    // Test 2: Check Broadcasting Routes
    echo '<div class="debug-box">';
    echo '<h2>2. Broadcasting Routes</h2>';
    
    $routes = [
        'nurse.chat.send' => route('nurse.chat.send'),
        'nurse.chat.typing' => route('nurse.chat.typing'),
        'nurse.chat.online_status' => route('nurse.chat.online_status'),
        'nurse.chat.show' => route('nurse.chat.show', ['id' => 1]),
        'broadcasting.auth' => url('/broadcasting/auth'),
    ];
    
    echo '<table>';
    echo '<tr><th>Route</th><th>URL</th></tr>';
    foreach ($routes as $name => $url) {
        echo "<tr><td>{$name}</td><td>" . htmlspecialchars($url) . "</td></tr>";
    }
    echo '</table>';
    echo '</div>';

    // Test 3: Check Nurse Authentication
    echo '<div class="debug-box">';
    echo '<h2>3. Authentication Check</h2>';
    
    $nurseAuth = Auth::guard('nurse_middle')->check();
    $healthcareAuth = Auth::guard('healthcare_facilities')->check();
    $webAuth = Auth::check();
    
    echo '<table>';
    echo '<tr><th>Guard</th><th>Status</th><th>User</th></tr>';
    
    echo '<tr><td>nurse_middle</td><td>';
    echo $nurseAuth ? '<span class="success">✓ Authenticated</span>' : '<span class="warning">✗ Not authenticated</span>';
    echo '</td><td>';
    if ($nurseAuth) {
        $user = Auth::guard('nurse_middle')->user();
        echo htmlspecialchars($user->name . ' (ID: ' . $user->id . ')');
    } else {
        echo 'N/A';
    }
    echo '</td></tr>';
    
    echo '<tr><td>healthcare_facilities</td><td>';
    echo $healthcareAuth ? '<span class="success">✓ Authenticated</span>' : '<span class="info">✗ Not authenticated</span>';
    echo '</td><td>';
    if ($healthcareAuth) {
        $user = Auth::guard('healthcare_facilities')->user();
        echo htmlspecialchars($user->name . ' (ID: ' . $user->id . ')');
    } else {
        echo 'N/A';
    }
    echo '</td></tr>';
    echo '</table>';
    echo '</div>';

    // Test 4: Sample Conversations
    echo '<div class="debug-box">';
    echo '<h2>4. Recent Conversations</h2>';
    
    if ($nurseAuth) {
        $nurseId = Auth::guard('nurse_middle')->id();
        $conversations = Conversation::with(['healthcare', 'latestMessage'])
            ->where('nurse_id', $nurseId)
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();
        
        if ($conversations->count() > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Healthcare</th><th>Last Message</th><th>Updated</th></tr>';
            foreach ($conversations as $conv) {
                $lastMsg = $conv->latestMessage ? substr($conv->latestMessage->message, 0, 50) : 'No messages';
                echo '<tr>';
                echo '<td>' . $conv->id . '</td>';
                echo '<td>' . htmlspecialchars($conv->healthcare->name ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($lastMsg) . '</td>';
                echo '<td>' . ($conv->last_message_at ? $conv->last_message_at->diffForHumans() : 'Never') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">No conversations found for this nurse.</p>';
        }
    } else {
        echo '<p class="warning">Not authenticated as nurse. Please login first.</p>';
    }
    echo '</div>';

    // Test 5: JavaScript Test Code
    echo '<div class="debug-box">';
    echo '<h2>5. JavaScript Test Code</h2>';
    echo '<p>Copy this code and paste it in your browser console on the nurse chat page:</p>';
    echo '<pre><code>
// Test Pusher Connection
console.log("=== Testing Pusher Connection ===");
console.log("Pusher Key:", "' . htmlspecialchars($pusherKey) . '");
console.log("Cluster:", "' . htmlspecialchars($pusherCluster) . '");

// Check if Echo is loaded
if (typeof window.Echo !== "undefined") {
    console.log("✓ Laravel Echo is loaded");
    console.log("Echo configuration:", window.Echo);
} else {
    console.error("✗ Laravel Echo is NOT loaded");
}

// Test channel subscription
if (typeof window.Echo !== "undefined") {
    const channelId = 2; // Replace with your conversation ID
    const channel = window.Echo.private("conversation." + channelId);
    
    channel.listen(".message.sent", (data) => {
        console.log("✓ Message received!", data);
    });
    
    channel.error((error) => {
        console.error("✗ Channel error:", error);
    });
    
    console.log("Subscribed to channel: conversation." + channelId);
}

// Check Laravel data
console.log("Laravel data:", window.Laravel);
    </code></pre>';
    echo '</div>';

    // Test 6: Environment Variables
    echo '<div class="debug-box">';
    echo '<h2>6. Environment Variables</h2>';
    
    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        $envLines = explode("\n", $envContent);
        $relevantVars = ['BROADCAST_DRIVER', 'PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER'];
        
        echo '<table>';
        echo '<tr><th>Variable</th><th>Value</th></tr>';
        foreach ($envLines as $line) {
            foreach ($relevantVars as $var) {
                if (strpos($line, $var . '=') === 0) {
                    $value = trim(substr($line, strlen($var) + 1));
                    echo "<tr><td>{$var}</td><td>" . htmlspecialchars($value) . "</td></tr>";
                }
            }
        }
        echo '</table>';
    } else {
        echo '<p class="error">.env file not found!</p>';
    }
    echo '</div>';
    ?>

    <div class="debug-box">
        <h2>7. Troubleshooting Steps</h2>
        <ol>
            <li><strong>Check Pusher Dashboard:</strong> Verify your app is created and keys are correct</li>
            <li><strong>Clear Cache:</strong> Run <code>php artisan config:clear && php artisan cache:clear</code></li>
            <li><strong>Check Browser Console:</strong> Look for Pusher connection errors</li>
            <li><strong>Verify Authentication:</strong> Make sure you're logged in as a nurse</li>
            <li><strong>Test Both Ways:</strong> Send message from nurse AND healthcare to test bidirectional</li>
            <li><strong>Check Firewall:</strong> Ensure ports 443 (HTTPS) and 80 (HTTP) are open</li>
            <li><strong>Pusher Debug:</strong> Check Pusher's debug console for connection logs</li>
        </ol>
    </div>

    <div class="debug-box">
        <h2>8. Quick Fix Commands</h2>
        <pre><code>cd <?php echo __DIR__; ?>
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
        </code></pre>
    </div>
</body>
</html>

<?php
/**
 * Test Nurse Chat Broadcasting
 * Access this page while logged in as nurse to test real-time messaging
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\Conversation;
use App\Models\Message;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nurse Chat Real-time Test</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .test-box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; font-weight: bold; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 3px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        button:hover { opacity: 0.9; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        #log { background: #000; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace; height: 300px; overflow-y: auto; }
        .log-entry { margin: 5px 0; padding: 5px; border-left: 3px solid #0f0; }
        .log-error { border-left-color: #f00; color: #f00; }
        .log-warn { border-left-color: #ffc107; color: #ffc107; }
        .log-info { border-left-color: #00f; color: #00f; }
    </style>
</head>
<body>
    <h1>🔍 Nurse Chat Real-time Broadcasting Test</h1>
    
    <?php
    $nurseAuth = Auth::guard('nurse_middle')->check();
    ?>

    <div class="test-box">
        <h2>1. Authentication Status</h2>
        <?php if ($nurseAuth): ?>
            <p class="success">✓ Logged in as Nurse</p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars(Auth::guard('nurse_middle')->user()->name); ?></p>
            <p><strong>ID:</strong> <?php echo Auth::guard('nurse_middle')->id(); ?></p>
            <p><strong>Role:</strong> <?php echo Auth::guard('nurse_middle')->user()->role; ?></p>
        <?php else: ?>
            <p class="error">✗ NOT logged in as nurse!</p>
            <p>Please login as a nurse first at: <a href="/mediqa_nurse/nurse/login">Nurse Login</a></p>
        <?php endif; ?>
    </div>

    <?php if ($nurseAuth): 
        $nurseId = Auth::guard('nurse_middle')->id();
        $conversations = Conversation::with(['healthcare', 'latestMessage'])
            ->where('nurse_id', $nurseId)
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();
    ?>
    
    <div class="test-box">
        <h2>2. Your Conversations</h2>
        <?php if ($conversations->count() > 0): ?>
            <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th>ID</th>
                    <th>Healthcare</th>
                    <th>Last Message</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($conversations as $conv): ?>
                <tr>
                    <td><?php echo $conv->id; ?></td>
                    <td><?php echo htmlspecialchars($conv->healthcare->name ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(substr($conv->latestMessage->message ?? 'No messages', 0, 50)); ?></td>
                    <td>
                        <a href="/mediqa_nurse/nurse/chat/conversation/<?php echo $conv->id; ?>" target="_blank">
                            <button class="btn-primary">Open Chat</button>
                        </a>
                        <button class="btn-success" onclick="testSend(<?php echo $conv->id; ?>)">Test Send</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="warning">No conversations found. Apply to a job first to create a conversation.</p>
        <?php endif; ?>
    </div>

    <div class="test-box">
        <h2>3. Pusher Configuration</h2>
        <pre>
PUSHER_KEY:      <?php echo Config::get('broadcasting.connections.pusher.key'); ?>
PUSHER_SECRET:   <?php echo substr(Config::get('broadcasting.connections.pusher.secret'), 0, 10); ?>...
PUSHER_APP_ID:   <?php echo Config::get('broadcasting.connections.pusher.app_id'); ?>
PUSHER_CLUSTER:  <?php echo Config::get('broadcasting.connections.pusher.options.cluster') ?? 'mt1'; ?>
BROADCAST_DRIVER: <?php echo Config::get('broadcasting.default'); ?>
        </pre>
    </div>

    <div class="test-box">
        <h2>4. Real-time Connection Test</h2>
        <p>Click "Start Test" to test real-time messaging connection:</p>
        <button class="btn-primary" onclick="startTest()">Start Connection Test</button>
        <button class="btn-danger" onclick="clearLog()">Clear Log</button>
        <div id="log"></div>
    </div>

    <div class="test-box">
        <h2>5. Manual Test Steps</h2>
        <ol>
            <li>Open this page while logged in as nurse</li>
            <li>Click "Start Connection Test"</li>
            <li>Open one of your conversations in another tab</li>
            <li>In another browser, login as healthcare and open the same conversation</li>
            <li>Send a message from healthcare side</li>
            <li>Check if message appears in real-time on nurse side</li>
        </ol>
    </div>

    <?php endif; ?>

    <script src="https://js.pusher.com/8.4/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
    <script>
    let echo = null;
    let testChannel = null;

    function log(message, type = 'info') {
        const logDiv = document.getElementById('log');
        const timestamp = new Date().toLocaleTimeString();
        const className = type === 'error' ? 'log-error' : (type === 'warn' ? 'log-warn' : 'log-info');
        logDiv.innerHTML += `<div class="log-entry ${className}">[${timestamp}] ${message}</div>`;
        logDiv.scrollTop = logDiv.scrollHeight;
    }

    function clearLog() {
        document.getElementById('log').innerHTML = '';
    }

    function startTest() {
        clearLog();
        log('Initializing Pusher...', 'info');

        try {
            window.Pusher = Pusher;
            
            echo = new Echo({
                broadcaster: 'pusher',
                key: '<?php echo Config::get('broadcasting.connections.pusher.key'); ?>',
                cluster: '<?php echo Config::get('broadcasting.connections.pusher.options.cluster') ?? 'mt1'; ?>',
                forceTLS: true,
                encrypted: true,
                authEndpoint: '<?php echo url('/broadcasting/auth'); ?>',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    }
                },
                disableStats: false,
                enabledTransports: ['ws', 'wss', 'xhr_streaming', 'xhr_polling'],
            });

            log('Pusher initialized successfully', 'success');
            log('Pusher Key: <?php echo Config::get('broadcasting.connections.pusher.key'); ?>', 'info');
            log('Cluster: <?php echo Config::get('broadcasting.connections.pusher.options.cluster') ?? 'mt1'; ?>', 'info');

            // Subscribe to a test channel
            const testConvId = <?php echo $conversations->first()->id ?? 1; ?>;
            testChannel = echo.private('conversation.' + testConvId);

            log('Subscribing to channel: conversation.' + testConvId, 'info');

            testChannel.subscriptionPending(() => {
                log('Subscription pending...', 'warn');
            });

            testChannel.subscription_cancelled(() => {
                log('Subscription cancelled!', 'error');
            });

            testChannel.subscription_error((error) => {
                log('Subscription ERROR: ' + JSON.stringify(error), 'error');
            });

            testChannel.subscription_succeeded(() => {
                log('✓ Subscription SUCCEEDED!', 'success');
                log('Channel members: ' + JSON.stringify(testChannel.members), 'info');
            });

            // Listen for messages
            testChannel.listen('.message.sent', (data) => {
                log('📨 MESSAGE RECEIVED!', 'success');
                log('From: ' + data.sender_name, 'info');
                log('Message: ' + data.message, 'info');
                log('Full data: ' + JSON.stringify(data, null, 2), 'info');
            });

            // Listen for typing
            testChannel.listen('.UserTyping', (data) => {
                log('⌨️ Typing event: ' + (data.is_typing ? 'typing...' : 'stopped typing'), 'info');
            });

            // Join presence channel
            log('Joining presence channel...', 'info');
            const presenceChannel = echo.join('conversation.' + testConvId + '.presence');
            
            presenceChannel.here((users) => {
                log('👥 Users in channel: ' + users.length, 'info');
                users.forEach(u => log('  - ' + u.name + ' (ID: ' + u.id + ')', 'info'));
            });

            presenceChannel.joining((user) => {
                log('➕ User joined: ' + user.name, 'success');
            });

            presenceChannel.leaving((user) => {
                log('➖ User left: ' + user.name, 'warn');
            });

            presenceChannel.error((error) => {
                log('Presence channel ERROR: ' + JSON.stringify(error), 'error');
            });

            // Test sending a message
            setTimeout(() => {
                testSendMessage(testConvId);
            }, 3000);

        } catch (e) {
            log('ERROR initializing: ' + e.message, 'error');
            console.error(e);
        }
    }

    function testSendMessage(conversationId) {
        log('📤 Testing message send...', 'info');
        
        fetch('/mediqa_nurse/nurse/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: 'Test message from nurse chat test page - ' + new Date().toLocaleTimeString()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                log('✓ Message sent successfully!', 'success');
                log('Message ID: ' + data.message.id, 'info');
            } else {
                log('✗ Send failed: ' + (data.error || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            log('✗ Send ERROR: ' + error.message, 'error');
        });
    }

    // Log connection state changes
    document.addEventListener('DOMContentLoaded', function() {
        log('Page loaded. Click "Start Connection Test" to begin.', 'info');
        
        // Check if Pusher is available
        if (typeof Pusher === 'undefined') {
            log('ERROR: Pusher library not loaded!', 'error');
        } else {
            log('Pusher library loaded successfully', 'success');
        }
    });

    // Handle page unload
    window.addEventListener('beforeunload', function() {
        if (echo) {
            echo.disconnect();
        }
    });
    </script>
</body>
</html>

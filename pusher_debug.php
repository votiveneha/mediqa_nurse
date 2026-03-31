<!DOCTYPE html>
<html>
<head>
    <title>Pusher Connection Debug</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        .log { background: #000; padding: 15px; border-radius: 5px; margin: 10px 0; height: 400px; overflow-y: auto; }
        .entry { margin: 5px 0; padding: 5px; border-left: 3px solid #0f0; }
        .error { border-left-color: #f00; color: #f00; }
        .warn { border-left-color: #ffc107; color: #ffc107; }
        .info { border-left-color: #00f; color: #0ff; }
        .success { border-left-color: #28a745; color: #28a745; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 3px; }
        button:hover { background: #0056b3; }
        #status { padding: 10px; margin: 10px 0; border-radius: 5px; font-weight: bold; }
        .connected { background: #28a745; color: white; }
        .disconnected { background: #dc3545; color: white; }
        .connecting { background: #ffc107; color: black; }
    </style>
</head>
<body>
    <h1>🔍 Pusher Connection Debug Tool</h1>
    <div id="status" class="disconnected">Status: Disconnected</div>
    <button onclick="connect()">Connect to Pusher</button>
    <button onclick="disconnect()">Disconnect</button>
    <button onclick="clearLog()">Clear Log</button>
    <button onclick="testMessage()">Test Send Message</button>
    <div class="log" id="log"></div>

    <script src="https://js.pusher.com/8.4/pusher.min.js"></script>
    <script>
    let pusher = null;
    let channel = null;

    function log(msg, type = 'info') {
        const logDiv = document.getElementById('log');
        const time = new Date().toLocaleTimeString();
        logDiv.innerHTML += `<div class="entry ${type}">[${time}] ${msg}</div>`;
        logDiv.scrollTop = logDiv.scrollHeight;
    }

    function clearLog() {
        document.getElementById('log').innerHTML = '';
    }

    function updateStatus(status) {
        const statusDiv = document.getElementById('status');
        statusDiv.className = status;
        statusDiv.textContent = 'Status: ' + status.charAt(0).toUpperCase() + status.slice(1);
    }

    function connect() {
        clearLog();
        log('Initializing Pusher...', 'info');

        pusher = new Pusher('<?php echo config('broadcasting.connections.pusher.key'); ?>', {
            cluster: '<?php echo config('broadcasting.connections.pusher.options.cluster') ?? 'mt1'; ?>',
            encrypted: true,
            authEndpoint: '<?php echo url('/broadcasting/auth'); ?>',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                }
            }
        });

        pusher.connection.bind('state_change', function(states) {
            log('Connection state: ' + states.previous + ' -> ' + states.current, 'info');
            updateStatus(states.current);
        });

        pusher.connection.bind('connected', function() {
            log('✓ Connected to Pusher!', 'success');
            subscribeToChannel();
        });

        pusher.connection.bind('error', function(err) {
            log('✗ Connection ERROR: ' + JSON.stringify(err), 'error');
        });

        pusher.connection.bind('disconnected', function() {
            log('Disconnected from Pusher', 'warn');
        });
    }

    function disconnect() {
        if (pusher) {
            pusher.disconnect();
            log('Disconnected manually', 'warn');
        }
    }

    function subscribeToChannel() {
        const convId = prompt('Enter conversation ID to test:', '1');
        if (!convId) return;

        log('Subscribing to private-channel: conversation.' + convId, 'info');

        channel = pusher.subscribe('private-conversation.' + convId);

        channel.bind('pusher:subscription_succeeded', function() {
            log('✓ Subscription SUCCEEDED!', 'success');
            log('Channel: ' + channel.name, 'info');
        });

        channel.bind('pusher:subscription_error', function(status) {
            log('✗ Subscription ERROR: ' + JSON.stringify(status), 'error');
        });

        channel.bind('pusher:subscription_pending', function() {
            log('Subscription pending...', 'warn');
        });

        channel.bind('message.sent', function(data) {
            log('📨 MESSAGE RECEIVED!', 'success');
            log('Sender: ' + data.sender_name, 'info');
            log('Message: ' + data.message, 'info');
            log('Full data: ' + JSON.stringify(data, null, 2), 'info');
            alert('MESSAGE RECEIVED!\nFrom: ' + data.sender_name + '\nMessage: ' + data.message);
        });

        // Also try presence channel
        log('Joining presence channel: presence-conversation.' + convId, 'info');
        const presenceChannel = pusher.subscribe('presence-conversation.' + convId);
        
        presenceChannel.bind('pusher:subscription_succeeded', function(members) {
            log('✓ Presence channel joined! Members: ' + members.count, 'success');
            members.each(function(member) {
                log('  - ' + member.info.name + ' (ID: ' + member.id + ')', 'info');
            });
        });

        presenceChannel.bind('pusher:member_added', function(member) {
            log('➕ User joined: ' + member.info.name, 'success');
        });

        presenceChannel.bind('pusher:member_removed', function(member) {
            log('➖ User left: ' + member.info.name, 'warn');
        });
    }

    function testMessage() {
        const convId = prompt('Enter conversation ID:');
        if (!convId) return;

        log('📤 Sending test message...', 'info');

        fetch('/mediqa_nurse/nurse/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                conversation_id: convId,
                message: 'Test message from debug page - ' + new Date().toLocaleTimeString()
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                log('✓ Message sent! ID: ' + data.message.id, 'success');
            } else {
                log('✗ Send failed: ' + (data.error || 'Unknown'), 'error');
            }
        })
        .catch(err => {
            log('✗ Send ERROR: ' + err.message, 'error');
        });
    }

    // Auto-connect on load
    window.addEventListener('load', function() {
        log('Page loaded. Click "Connect to Pusher" to start.', 'info');
        log('Pusher Key: <?php echo config('broadcasting.connections.pusher.key'); ?>', 'info');
        log('Cluster: <?php echo config('broadcasting.connections.pusher.options.cluster') ?? 'mt1'; ?>', 'info');
    });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusher Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .log { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #0056b3; }
        .config-item { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px; }
        .config-label { font-weight: bold; color: #555; }
        .config-value { font-family: monospace; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Pusher Connection Test</h1>
        
        <h2>Configuration</h2>
        <div class="config-item">
            <div class="config-label">Pusher App ID:</div>
            <div class="config-value" id="appId">Loading...</div>
        </div>
        <div class="config-item">
            <div class="config-label">Pusher Key:</div>
            <div class="config-value" id="appKey">Loading...</div>
        </div>
        <div class="config-item">
            <div class="config-label">Cluster:</div>
            <div class="config-value" id="cluster">Loading...</div>
        </div>
        
        <h2>Connection Status</h2>
        <div id="status" class="status info">Initializing...</div>
        
        <h2>Actions</h2>
        <button onclick="testConnection()">Test Connection</button>
        <button onclick="testChannel()">Test Private Channel</button>
        <button onclick="clearLog()">Clear Log</button>
        
        <h2>Event Log</h2>
        <div class="log" id="log"></div>
    </div>

    <script src="https://js.pusher.com/8.4/pusher.min.js"></script>
    <script>
        // Configuration from server
        const PUSHER_CONFIG = {
            key: '<?php echo $_ENV['PUSHER_APP_KEY'] ?? 'not_set'; ?>',
            cluster: '<?php echo $_ENV['PUSHER_APP_CLUSTER'] ?? 'not_set'; ?>',
            appId: '<?php echo $_ENV['PUSHER_APP_ID'] ?? 'not_set'; ?>',
        };

        // Display config
        document.getElementById('appId').textContent = PUSHER_CONFIG.appId;
        document.getElementById('appKey').textContent = PUSHER_CONFIG.key;
        document.getElementById('cluster').textContent = PUSHER_CONFIG.cluster;

        let pusher = null;

        function log(message, type = 'info') {
            const logDiv = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                info: '#007bff',
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107'
            };
            logDiv.innerHTML += `<div style="color: ${colors[type] || colors.info};">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
        }

        function clearLog() {
            document.getElementById('log').innerHTML = '';
        }

        function updateStatus(message, type) {
            const statusDiv = document.getElementById('status');
            statusDiv.textContent = message;
            statusDiv.className = `status ${type}`;
        }

        function initPusher() {
            try {
                pusher = new Pusher(PUSHER_CONFIG.key, {
                    cluster: PUSHER_CONFIG.cluster,
                    encrypted: true,
                    forceTLS: true,
                    disableStats: true,
                });

                pusher.connection.bind('state_change', function(states) {
                    log(`Connection state: ${states.previous} → ${states.current}`, 'info');
                });

                pusher.connection.bind('connected', function() {
                    log('✅ Connected to Pusher!', 'success');
                    updateStatus('Connected to Pusher', 'success');
                });

                pusher.connection.bind('disconnected', function() {
                    log('❌ Disconnected from Pusher', 'error');
                    updateStatus('Disconnected from Pusher', 'error');
                });

                pusher.connection.bind('error', function(err) {
                    log('❌ Pusher error: ' + JSON.stringify(err), 'error');
                    updateStatus('Pusher Error: ' + JSON.stringify(err), 'error');
                });

                log('Pusher initialized', 'info');
                return pusher;
            } catch (error) {
                log('Failed to initialize Pusher: ' + error.message, 'error');
                updateStatus('Initialization Error: ' + error.message, 'error');
                return null;
            }
        }

        function testConnection() {
            clearLog();
            log('Testing Pusher connection...', 'info');
            updateStatus('Connecting...', 'info');
            
            if (pusher) {
                pusher.disconnect();
            }
            
            pusher = initPusher();
            
            setTimeout(() => {
                if (pusher && pusher.connection.state === 'connected') {
                    log('Connection test successful!', 'success');
                    updateStatus('Connection test successful!', 'success');
                } else if (pusher) {
                    log('Connection test failed. Current state: ' + pusher.connection.state, 'warning');
                    updateStatus('Connection test failed. State: ' + pusher.connection.state, 'warning');
                }
            }, 5000);
        }

        function testChannel() {
            if (!pusher || pusher.connection.state !== 'connected') {
                log('Not connected. Please test connection first.', 'error');
                return;
            }

            log('Subscribing to test channel...', 'info');
            
            const channel = pusher.subscribe('test-channel');
            
            channel.bind('pusher:subscription_succeeded', function() {
                log('✅ Successfully subscribed to test-channel!', 'success');
                
                // Trigger a test event (client-side only)
                channel.trigger('client-test-event', {
                    message: 'Hello from test page!',
                    timestamp: new Date().toISOString()
                });
                log('Triggered client-test-event', 'info');
            });

            channel.bind('pusher:subscription_error', function(err) {
                log('❌ Subscription error: ' + err, 'error');
            });

            channel.bind('test-event', function(data) {
                log('📨 Received test-event: ' + JSON.stringify(data), 'success');
            });
        }

        // Initialize on page load
        window.addEventListener('DOMContentLoaded', function() {
            log('Page loaded, initializing Pusher...', 'info');
            initPusher();
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Online Status Test</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .card { background: white; color: #333; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .status-display { text-align: center; padding: 30px; border-radius: 10px; margin: 20px 0; }
        .status-online { background: #28a745; color: white; }
        .status-offline { background: #6c757d; color: white; }
        .status-dot { width: 20px; height: 20px; border-radius: 50%; display: inline-block; margin-right: 10px; }
        .dot-green { background: #28a745; box-shadow: 0 0 10px #28a745; }
        .dot-gray { background: #6c757d; }
        button { padding: 12px 24px; margin: 10px; cursor: pointer; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .log { background: #000; color: #0f0; padding: 15px; border-radius: 5px; height: 300px; overflow-y: auto; font-family: monospace; }
        .log-entry { margin: 5px 0; padding: 5px; border-left: 3px solid #0f0; }
        .log-error { border-left-color: #f00; color: #f00; }
        .log-warn { border-left-color: #ffc107; color: #ffc107; }
        .log-info { border-left-color: #00f; color: #00f; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; border: 1px solid #dee2e6; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
    </style>
</head>
<body>
    <h1>🟢 Online Status Testing Tool</h1>
    <p>Test the online/offline status system with real-time updates</p>

    <?php
    $nurseAuth = \Illuminate\Support\Facades\Auth::guard('nurse_middle')->check();
    $healthcareAuth = \Illuminate\Support\Facades\Auth::guard('healthcare_facilities')->check();
    ?>

    <div class="card">
        <h2>📊 Current Status</h2>
        <table>
            <tr>
                <th>Guard</th>
                <th>Status</th>
                <th>User</th>
                <th>ID</th>
            </tr>
            <tr>
                <td>Nurse</td>
                <td>
                    <?php if ($nurseAuth): ?>
                        <span class="badge badge-success">✓ Logged In</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Not Logged In</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $nurseAuth ? \Illuminate\Support\Facades\Auth::guard('nurse_middle')->user()->name : 'N/A'; ?></td>
                <td><?php echo $nurseAuth ? \Illuminate\Support\Facades\Auth::guard('nurse_middle')->id() : 'N/A'; ?></td>
            </tr>
            <tr>
                <td>Healthcare</td>
                <td>
                    <?php if ($healthcareAuth): ?>
                        <span class="badge badge-success">✓ Logged In</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Not Logged In</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $healthcareAuth ? \Illuminate\Support\Facades\Auth::guard('healthcare_facilities')->user()->name : 'N/A'; ?></td>
                <td><?php echo $healthcareAuth ? \Illuminate\Support\Facades\Auth::guard('healthcare_facilities')->id() : 'N/A'; ?></td>
            </tr>
        </table>
    </div>

    <?php if ($nurseAuth || $healthcareAuth): 
        $userId = $nurseAuth ? \Illuminate\Support\Facades\Auth::guard('nurse_middle')->id() : \Illuminate\Support\Facades\Auth::guard('healthcare_facilities')->id();
        $userRole = $nurseAuth ? 'nurse' : 'healthcare';
    ?>

    <div class="card">
        <h2>🎯 Test Online Status</h2>
        
        <div id="statusDisplay" class="status-display status-offline">
            <span id="statusDot" class="status-dot dot-gray"></span>
            <span id="statusText">Checking...</span>
        </div>

        <div style="text-align: center;">
            <button class="btn-success" onclick="setOnline(true)">🟢 Set Online</button>
            <button class="btn-danger" onclick="setOnline(false)">🔴 Set Offline</button>
            <button class="btn-info" onclick="checkStatus()">🔄 Check Now</button>
            <button class="btn-primary" onclick="startAutoCheck()">⏱ Start Auto-Check</button>
            <button class="btn-danger" onclick="stopAutoCheck()">⏹ Stop Auto-Check</button>
        </div>

        <h3>📋 Configuration</h3>
        <pre>
User ID: <?php echo $userId; ?>
Role: <?php echo $userRole; ?>
Status URL: <?php echo url('/nurse/chat/online-status'); ?>
Check URL: <?php echo url('/nurse/chat/check-status/' . $userId); ?>
        </pre>
    </div>

    <div class="card">
        <h2>📝 Activity Log</h2>
        <button class="btn-danger" onclick="clearLog()">Clear Log</button>
        <div class="log" id="log"></div>
    </div>

    <div class="card">
        <h2>🧪 Test Instructions</h2>
        <ol>
            <li><strong>Open this page in two browsers</strong> - one as nurse, one as healthcare</li>
            <li><strong>Click "Set Online"</strong> in both browsers</li>
            <li><strong>Both should show green status</strong></li>
            <li><strong>Click "Set Offline" in one browser</strong></li>
            <li><strong>Other browser should show gray within 5 seconds</strong> (if auto-check is on)</li>
            <li><strong>Click "Check Now"</strong> to manually verify status</li>
        </ol>
    </div>

    <?php else: ?>
    <div class="card">
        <h2>⚠️ Not Logged In</h2>
        <p>Please login as either nurse or healthcare to test the online status system.</p>
        <ul>
            <li><a href="/mediqa_nurse/nurse/login">Nurse Login</a></li>
            <li><a href="/mediqa_nurse/healthcare-facilities/login">Healthcare Login</a></li>
        </ul>
    </div>
    <?php endif; ?>

    <script>
    let autoCheckInterval = null;
    const USER_ID = <?php echo $userId ?? 0; ?>;
    const STATUS_URL = '<?php echo url('/nurse/chat/online-status'); ?>';
    const CHECK_URL = '<?php echo url('/nurse/chat/check-status/' . $userId); ?>';

    function log(message, type = 'info') {
        const logDiv = document.getElementById('log');
        const time = new Date().toLocaleTimeString();
        const className = type === 'error' ? 'log-error' : (type === 'warn' ? 'log-warn' : 'log-info');
        logDiv.innerHTML += `<div class="log-entry ${className}">[${time}] ${message}</div>`;
        logDiv.scrollTop = logDiv.scrollHeight;
    }

    function clearLog() {
        document.getElementById('log').innerHTML = '';
        log('Log cleared', 'info');
    }

    function updateStatusDisplay(isOnline) {
        const display = document.getElementById('statusDisplay');
        const dot = document.getElementById('statusDot');
        const text = document.getElementById('statusText');

        if (isOnline) {
            display.className = 'status-display status-online';
            dot.className = 'status-dot dot-green';
            text.textContent = 'Online';
            log('Status: ONLINE ✓', 'success');
        } else {
            display.className = 'status-display status-offline';
            dot.className = 'status-dot dot-gray';
            const now = new Date();
            const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            text.textContent = 'Last seen ' + timeString;
            log('Status: OFFLINE / Last seen ' + timeString, 'warn');
        }
    }

    function setOnline(isOnline) {
        log('Setting status to: ' + (isOnline ? 'ONLINE' : 'OFFLINE'), 'info');
        
        fetch(STATUS_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ is_online: isOnline })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                log('Status updated successfully!', 'success');
                updateStatusDisplay(isOnline);
            } else {
                log('Failed to update status: ' + (data.error || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            log('Error: ' + error.message, 'error');
        });
    }

    function checkStatus() {
        log('Checking status...', 'info');
        
        fetch(CHECK_URL, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => response.json())
        .then(data => {
            log('Status response: ' + JSON.stringify(data), 'info');
            updateStatusDisplay(data.is_online);
        })
        .catch(error => {
            log('Check failed: ' + error.message, 'error');
        });
    }

    function startAutoCheck() {
        if (autoCheckInterval) {
            log('Auto-check already running', 'warn');
            return;
        }
        
        log('Starting auto-check (every 5 seconds)...', 'success');
        checkStatus(); // Check immediately
        autoCheckInterval = setInterval(checkStatus, 5000);
    }

    function stopAutoCheck() {
        if (autoCheckInterval) {
            clearInterval(autoCheckInterval);
            autoCheckInterval = null;
            log('Auto-check stopped', 'warn');
        } else {
            log('Auto-check not running', 'info');
        }
    }

    // Initialize
    window.addEventListener('load', function() {
        log('Page loaded. User ID: ' + USER_ID, 'info');
        log('Click "Set Online" to start testing', 'info');
        
        // Initial status check
        setTimeout(checkStatus, 1000);
    });

    // Warn before leaving
    window.addEventListener('beforeunload', function() {
        setOnline(false); // Set offline when leaving
        stopAutoCheck();
    });
    </script>
</body>
</html>

# Online/Offline Status Implementation

## Overview
This document describes the implementation of real-time online/offline status detection in the MediQa chat system.

## Features

### 1. Real-time Status Detection
- Users can see if the other person in a conversation is **Online** or **Offline**
- Status updates in real-time using Pusher presence channels
- Shows "Last seen" timestamp when user goes offline

### 2. Visual Indicators
- **Green dot** (#28a745) with "Online" text when user is online
- **Gray dot** (#888) with "Last seen HH:MM AM/PM" when user is offline
- Toast notification when user comes online

### 3. Heartbeat System
- Sends heartbeat every 30 seconds to mark user as online
- Automatically marks user as offline when page is closed
- Uses `navigator.sendBeacon()` for reliable offline status on page unload

## Implementation Details

### Backend Components

#### 1. Route (`routes/web.php`)
```php
Route::post('/online-status', 'App\Http\Controllers\ChatController@updateOnlineStatus')->name('online_status');
```

#### 2. Controller Method (`app/Http/Controllers/ChatController.php`)
```php
public function updateOnlineStatus(Request $request)
{
    $request->validate(['is_online' => 'boolean']);
    
    $user = Auth::guard('nurse_middle')->user();
    $isOnline = $request->is_online ?? true;
    
    // Update cache with user's online status
    if ($isOnline) {
        cache()->set("user_{$user->id}_online", true, now()->addMinutes(5));
    } else {
        cache()->forget("user_{$user->id}_online");
    }
    
    // Broadcast status
    broadcast(new UserOnlineStatus($user->id, $isOnline, $isOnline ? null : now()))->toOthers();
    
    return response()->json(['success' => true, 'is_online' => $isOnline]);
}
```

#### 3. Event (`app/Events/UserOnlineStatus.php`)
Already exists - broadcasts user status changes on presence channel.

#### 4. Presence Channel (`routes/channels.php`)
```php
Broadcast::channel('conversation.{conversationId}.presence', function ($user, $conversationId) {
    // Authorization logic - only conversation participants
    return [
        'id' => $authenticatedUser->id,
        'name' => $authenticatedUser->name . ' ' . ($authenticatedUser->lastname ?? ''),
        'avatar' => $authenticatedUser->profile_img,
        'role' => $authenticatedUser->role,
    ];
});
```

### Frontend Components

#### 1. Nurse Chat View (`resources/views/nurse/chat/conversation.blade.php`)

**Heartbeat Implementation:**
```javascript
// Send heartbeat to mark user as online
function sendHeartbeat() {
    fetch('{{ route("nurse.chat.online_status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ is_online: true })
    }).catch(err => console.error('Heartbeat failed:', err));
}

// Send initial heartbeat
sendHeartbeat();

// Send heartbeat every 30 seconds
const heartbeatInterval = setInterval(sendHeartbeat, 30000);

// Send offline status when leaving page
window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('{{ route("nurse.chat.online_status") }}', JSON.stringify({ is_online: false }));
    clearInterval(heartbeatInterval);
});
```

**Presence Channel Handling:**
```javascript
Echo.join('conversation.' + window.Laravel.conversationId + '.presence')
    .here((users) => {
        const isOtherOnline = users.some(u => u.id == window.Laravel.otherParticipantId);
        updateUserStatus(isOtherOnline);
    })
    .joining((user) => {
        if (user.id == window.Laravel.otherParticipantId) {
            updateUserStatus(true);
            showNotification(user.name + ' is now online');
        }
    })
    .leaving((user) => {
        if (user.id == window.Laravel.otherParticipantId) {
            updateUserStatus(false);
        }
    })
    .error((error) => {
        console.error('Presence channel error:', error);
        updateUserStatus(false);
    });
```

**Status Update Function:**
```javascript
function updateUserStatus(isOnline) {
    const statusIcon = document.getElementById('status-icon');
    const statusText = document.getElementById('status-text');
    
    if (statusIcon && statusText) {
        if (isOnline) {
            statusIcon.style.color = '#28a745';
            statusText.textContent = 'Online';
            statusText.style.color = '#28a745';
        } else {
            statusIcon.style.color = '#888';
            statusText.textContent = 'Last seen ' + new Date().toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            statusText.style.color = '#888';
        }
    }
}
```

#### 2. Healthcare Chat (`resources/js/chat.js`)

The ChatManager class handles online status for healthcare facilities:

```javascript
class ChatManager {
    startHeartbeat() {
        const self = this;
        
        // Send initial heartbeat
        this.sendHeartbeat();
        
        // Send heartbeat every 30 seconds
        const heartbeatInterval = setInterval(() => {
            this.sendHeartbeat();
        }, 30000);
        
        // Send offline status when leaving page
        window.addEventListener('beforeunload', function() {
            self.sendOfflineStatus();
            clearInterval(heartbeatInterval);
        });
    }
    
    sendHeartbeat() {
        const url = window.Laravel.userRole === 1
            ? '/nurse/chat/online-status'
            : '/healthcare-facilities/chat/online-status';
            
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || window.Laravel.csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ is_online: true })
        }).catch(err => console.error('Heartbeat failed:', err));
    }
    
    sendOfflineStatus() {
        const url = window.Laravel.userRole === 1
            ? '/nurse/chat/online-status'
            : '/healthcare-facilities/chat/online-status';
            
        navigator.sendBeacon(url, JSON.stringify({ is_online: false }));
    }
}
```

## HTML Structure

The status display uses this HTML structure in both views:

```html
<div class="chat-header-subtitle" id="userStatusContainer">
    <i class="fas fa-circle" id="status-icon" style="font-size: 8px; color: #888;"></i> 
    <span id="status-text">Offline</span>
</div>
```

## Configuration Requirements

### Pusher Configuration (`.env`)
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

### Broadcasting Configuration (`config/broadcasting.php`)
```php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ],
    ],
],
```

## How It Works

1. **User Opens Chat:**
   - Heartbeat sends `is_online: true` to server
   - User is added to presence channel
   - Cache stores `user_{id}_online = true` for 5 minutes

2. **Other User Sees Status:**
   - Presence channel's `.here()` callback fires
   - Checks if other participant is in the channel
   - Updates UI to show "Online" with green indicator

3. **User Closes Browser:**
   - `beforeunload` event fires
   - `navigator.sendBeacon()` sends `is_online: false`
   - Cache entry is removed
   - Other user sees "Last seen HH:MM AM/PM"

4. **Heartbeat Renewal:**
   - Every 30 seconds, heartbeat renews online status
   - If heartbeat stops (crash, network loss), cache expires after 5 minutes
   - Other user sees offline status

## Testing

### Test Online Status Detection
1. Open chat conversation as Nurse
2. Open same conversation as Healthcare Facility in another browser
3. Verify green dot and "Online" text appears
4. Close one browser
5. Verify gray dot and "Last seen" time appears

### Test Real-time Updates
1. Both users online in conversation
2. One user closes browser
3. Other user should see status change within 1-2 seconds
4. Reopen browser
5. Status should change back to "Online"

### Test Notification
1. User A is offline
2. User A opens chat
3. User B should see toast notification: "{User A Name} is now online"

## Troubleshooting

### Status Not Updating
1. Check Pusher configuration in `.env`
2. Verify broadcasting is enabled: `BROADCAST_DRIVER=pusher`
3. Check browser console for Pusher connection errors
4. Verify presence channel authorization is working

### Heartbeat Not Sending
1. Check browser console for network errors
2. Verify CSRF token is present in meta tag
3. Check route URLs are correct

### Offline Status Not Showing
1. Verify `navigator.sendBeacon()` is supported in browser
2. Check cache is properly configured
3. Verify `beforeunload` event is firing

## Browser Compatibility

- **Heartbeat**: All modern browsers
- **sendBeacon**: Chrome 39+, Firefox 31+, Safari 11.1+, Edge 14+
- **Presence Channels**: Requires Pusher account and configuration

## Performance Considerations

- Heartbeat interval: 30 seconds (balance between accuracy and server load)
- Cache TTL: 5 minutes (prevents stale online status)
- Presence channel: Only joins when conversation is open
- Toast notifications: Auto-dismiss after 3 seconds

## Security

- Presence channel authorization ensures only conversation participants can see status
- CSRF protection on all status update endpoints
- Authentication required via appropriate guard (nurse_middle or healthcare_facilities)

## Future Enhancements

1. Add typing indicator integration with online status
2. Show "Away" status after period of inactivity
3. Add user preferences to appear offline
4. Store last seen timestamp in database for persistence
5. Add mobile push notifications when offline user comes online

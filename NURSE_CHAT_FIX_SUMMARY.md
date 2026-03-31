# Nurse Chat Fix - Online Status & Message Reception

## Problem Identified

The nurse chat at `http://localhost/mediqa_nurse/nurse/chat/conversation/2` had two main issues:

1. **Online/Offline status not working** - Status indicator wasn't updating properly
2. **Messages not being received** - Real-time messages from healthcare weren't appearing

## Root Cause

The `ChatController` methods were hardcoded to use `Auth::guard('nurse_middle')` instead of detecting which guard (nurse or healthcare) was authenticated. This caused:
- Authentication failures in multi-guard environment
- Broadcasting issues because user couldn't be properly identified
- Presence channel authorization failures

## Solution Applied

### 1. Added Multi-Guard Authentication Helper

**File: `app/Http/Controllers/ChatController.php`**

Added a helper method to detect authenticated user from any guard:

```php
/**
 * Get authenticated user from any guard
 */
private function getAuthenticatedUser()
{
    return Auth::guard('nurse_middle')->check() ? Auth::guard('nurse_middle')->user() :
           (Auth::guard('healthcare_facilities')->check() ? Auth::guard('healthcare_facilities')->user() :
           (Auth::check() ? Auth::user() : null));
}
```

### 2. Updated All Controller Methods

Modified these methods to use `getAuthenticatedUser()` instead of hardcoded guard:

- ✅ `markAsRead()`
- ✅ `typingStatus()`
- ✅ `updateOnlineStatus()`
- ✅ `unreadCount()`
- ✅ `deleteConversation()`
- ✅ `archiveConversation()`

### 3. Enhanced Frontend Presence Handling

**File: `resources/views/nurse/chat/conversation.blade.php`**

Added comprehensive presence channel handling:

```javascript
// Heartbeat system
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

// Send heartbeat every 30 seconds
const heartbeatInterval = setInterval(sendHeartbeat, 30000);

// Send offline status when leaving page
window.addEventListener('beforeunload', function() {
    navigator.sendBeacon('{{ route("nurse.chat.online_status") }}', JSON.stringify({ is_online: false }));
    clearInterval(heartbeatInterval);
});
```

Enhanced presence channel with error handling:

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

### 4. Updated Healthcare Chat (resources/js/chat.js)

Enhanced the ChatManager class with:

```javascript
// Improved heartbeat system
startHeartbeat() {
    const self = this;
    this.sendHeartbeat();
    
    const heartbeatInterval = setInterval(() => {
        this.sendHeartbeat();
    }, 30000);
    
    window.addEventListener('beforeunload', function() {
        self.sendOfflineStatus();
        clearInterval(heartbeatInterval);
    });
}

// Show online status with "Last seen" timestamp
showUserOnline(isOnline) {
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
```

## Files Modified

1. ✅ `app/Http/Controllers/ChatController.php` - Multi-guard authentication
2. ✅ `resources/views/nurse/chat/conversation.blade.php` - Enhanced presence handling
3. ✅ `resources/js/chat.js` - Improved ChatManager class
4. ✅ `routes/web.php` - Added online-status route

## Testing Steps

### Test 1: Online Status Detection
1. Login as **Nurse** and open conversation with healthcare
2. Login as **Healthcare** in another browser/incognito window
3. Open the same conversation
4. **Expected:** Nurse should see healthcare as "Online" (green dot)
5. **Expected:** Healthcare should see nurse as "Online" (green dot)

### Test 2: Message Reception
1. Both users online in conversation
2. Healthcare sends a message
3. **Expected:** Nurse receives message in real-time (within 1-2 seconds)
4. Nurse sends a message
5. **Expected:** Healthcare receives message in real-time

### Test 3: Offline Status
1. Both users online
2. Healthcare closes browser
3. **Expected:** Nurse sees "Last seen HH:MM AM/PM" within 1-2 seconds
4. Healthcare reopens browser
5. **Expected:** Nurse sees toast notification "{Name} is now online"

### Test 4: Typing Indicator
1. Both users online
2. Healthcare starts typing
3. **Expected:** Nurse sees "{Name} is typing..." indicator
4. Healthcare stops typing
5. **Expected:** Indicator disappears after 3 seconds

## Debug Tool

A debug page has been created at:
**`http://localhost/mediqa_nurse/debug_nurse_chat.php`**

This page shows:
- Pusher configuration status
- Route URLs
- Authentication status
- Recent conversations
- JavaScript test code
- Environment variables

## Cache Cleared

All Laravel caches have been cleared:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Configuration Checklist

### .env File
Ensure these are set correctly:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### Pusher Dashboard
1. App created at https://pusher.com
2. Keys match `.env` file
3. Cluster is correct (e.g., `mt1` for US)
4. App is not in "Disabled" state

### Broadcasting Auth Route
The custom broadcasting auth route handles multiple guards:
```php
Route::post('/broadcasting/auth', function (Request $request) {
    if (Auth::guard('nurse_middle')->check()) {
        $user = Auth::guard('nurse_middle')->user();
    } elseif (Auth::guard('healthcare_facilities')->check()) {
        $user = Auth::guard('healthcare_facilities')->user();
    } elseif (Auth::check()) {
        $user = Auth::user();
    } else {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    $request->setUserResolver(fn() => $user);
    return Broadcast::auth($request);
})->middleware(['web']);
```

## Browser Console Debugging

Open browser console (F12) on nurse chat page and run:

```javascript
// Check Pusher connection
console.log("Pusher Key:", window.Echo?.options?.pusher?.key);
console.log("Echo loaded:", typeof window.Echo !== "undefined");

// Check Laravel data
console.log("Laravel data:", window.Laravel);

// Test channel subscription
if (typeof window.Echo !== "undefined") {
    const channel = window.Echo.private("conversation." + window.Laravel.conversationId);
    
    channel.listen(".message.sent", (data) => {
        console.log("✓ Message received!", data);
    });
    
    channel.error((error) => {
        console.error("✗ Channel error:", error);
    });
}
```

## Common Issues & Solutions

### Issue: "401 Unauthorized" on broadcasting/auth
**Solution:** Check that you're logged in as nurse or healthcare

### Issue: Messages send but don't appear in real-time
**Solution:** 
1. Check browser console for Pusher errors
2. Verify Pusher keys in `.env`
3. Run `php artisan config:clear`

### Issue: Online status always shows "Offline"
**Solution:**
1. Check presence channel is joining (console logs)
2. Verify heartbeat is sending (check Network tab)
3. Check cache is working: `php artisan cache:clear`

### Issue: Typing indicator not working
**Solution:**
1. Check typing route is accessible
2. Verify UserTyping event is broadcasting
3. Check presence channel authorization

## Performance Notes

- **Heartbeat Interval:** 30 seconds (balance between accuracy and server load)
- **Cache TTL:** 5 minutes (prevents stale online status)
- **Presence Channel:** Only joins when conversation view is open
- **Toast Notifications:** Auto-dismiss after 3 seconds

## Security

- ✅ Presence channel authorization ensures only conversation participants
- ✅ CSRF protection on all endpoints
- ✅ Authentication required via appropriate guard
- ✅ Participant verification before allowing actions

## Next Steps

1. **Test thoroughly** with both nurse and healthcare accounts
2. **Monitor Pusher dashboard** for connection statistics
3. **Check server logs** for any broadcasting errors
4. **Consider Redis** for production cache (more reliable than file cache)

## Additional Documentation

- `ONLINE_OFFLINE_STATUS_IMPLEMENTATION.md` - Complete implementation guide
- `debug_nurse_chat.php` - Debug tool for troubleshooting

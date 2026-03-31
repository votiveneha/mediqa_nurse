# Real-Time Online/Offline Status Implementation

## Overview
This document describes the implementation of real-time online/offline status tracking for the chat system. When one user views the chat, they will see the other user's status (online/offline) update in real-time based on their actual activity.

## Components

### 1. Middleware (`UserOnlineMiddleware.php`)
**Location:** `app/Http/Middleware/UserOnlineMiddleware.php`

Automatically marks users as online when they visit any chat page:
- Sets cache key `user_{id}_online` to `true` with 5-minute expiry
- Broadcasts `UserOnlineStatus` event to notify all subscribers

### 2. Event (`UserOnlineStatus.php`)
**Location:** `app/Events/UserOnlineStatus.php`

Broadcasts user status changes on two channels:
- **Presence Channel:** `user.{userId}.online` - For specific user status
- **Public Channel:** `users.online.global` - For global status updates

Uses `ShouldBroadcastNow` for immediate delivery.

### 3. Frontend Implementation

#### Nurse Chat (`resources/views/nurse/chat/conversation.blade.php`)
- Listens to `users.online.global` channel for status updates
- Joins specific user's presence channel: `user.{otherParticipantId}.online`
- Updates UI in real-time when status changes
- Sends heartbeat every 2 minutes to keep status active
- Sends offline status when page is closed (using `navigator.sendBeacon`)

#### Healthcare Chat (`resources/js/chat.js`)
- `ChatManager` class handles all real-time functionality
- Listens to both presence and global channels
- Triple-layer tracking:
  1. Global presence channel (`users.online`)
  2. Global status channel (`users.online.global`)
  3. Specific user presence channel (`user.{otherParticipantId}.online`)
- Sends heartbeat every 30 seconds
- Automatically updates status icon and text

### 4. Routes (`routes/web.php`)

#### Nurse Routes:
```php
Route::prefix('nurse/chat')
    ->name('nurse.chat.')
    ->middleware(['auth:nurse_middle', 'user.online'])
    ->group(function () {
        Route::post('/online-status', 'ChatController@updateOnlineStatus')->name('online_status');
        Route::get('/check-status/{userId}', 'ChatController@checkUserStatus')->name('check_status');
        // ... other routes
    });
```

#### Healthcare Routes:
```php
Route::prefix('healthcare-facilities/chat')
    ->name('healthcare.chat.')
    ->middleware(['auth:healthcare_facilities', 'user.online'])
    ->group(function () {
        Route::post('/online-status', 'ChatController@updateOnlineStatus')->name('online_status');
        Route::get('/check-status/{userId}', 'ChatController@checkUserStatus')->name('check_status');
        // ... other routes
    });
```

### 5. Middleware Registration (`app/Http/Kernel.php`)
```php
protected $middlewareAliases = [
    // ...
    'user.online' => \App\Http\Middleware\UserOnlineMiddleware::class,
];
```

## How It Works

### User Flow:

1. **User Opens Chat Page**
   - `UserOnlineMiddleware` runs automatically
   - Sets user status to online in cache
   - Broadcasts `UserOnlineStatus` event

2. **Other User's Browser Receives Update**
   - JavaScript listening to channels receives the event
   - Updates status icon (green for online, gray for offline)
   - Changes text from "Offline" to "Online"

3. **Heartbeat Mechanism**
   - Nurse chat: Sends heartbeat every 2 minutes
   - Healthcare chat: Sends heartbeat every 30 seconds
   - Cache expires after 5 minutes of inactivity
   - If heartbeat stops, user is automatically marked offline

4. **User Leaves Page**
   - `beforeunload` event triggers
   - `navigator.sendBeacon` sends offline status
   - Other user sees status change to "Offline"

## UI Updates

### Online Status:
```html
<div class="chat-header-subtitle online-status" id="userStatusContainer">
    <i class="fas fa-circle" id="status-icon" style="color: #28a745;"></i>
    <span id="status-text">Online</span>
</div>
```

### Offline Status:
```html
<div class="chat-header-subtitle offline">
    <i class="fas fa-circle" style="color: #888;"></i>
    <span>Offline</span>
</div>
```

## Configuration Requirements

### Pusher Configuration (`.env`)
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_KEY=your-pusher-key
PUSHER_APP_SECRET=your-pusher-secret
PUSHER_APP_ID=your-pusher-app-id
PUSHER_APP_CLUSTER=mt1
```

### Broadcasting Configuration (`config/broadcasting.php`)
Already configured for Pusher with TLS support.

## Testing

1. **Open two different browsers** (or incognito window)
2. **Login as nurse in one browser** and healthcare in another
3. **Start a chat conversation** between the two users
4. **Observe the status indicator** in the chat header:
   - Should show "Online" (green) when both are active
   - Should show "Offline" (gray) when one closes the browser
5. **Test real-time updates:**
   - Close one browser - other should see "Offline" within seconds
   - Reopen browser - other should see "Online" within seconds

## Troubleshooting

### Status Not Updating:
1. Check Pusher configuration in `.env`
2. Verify broadcasting is enabled: `config('broadcasting.default') !== 'null'`
3. Check browser console for Pusher connection errors
4. Ensure routes have `user.online` middleware

### Status Stuck on "Online":
1. Cache might not be expiring - check Redis/cache configuration
2. Heartbeat might not be sending - check browser console
3. Middleware might not be running - verify route configuration

### Console Debugging:
```javascript
// Check if Echo is initialized
console.log(window.Echo);

// Check current user data
console.log(window.Laravel);

// Manually check user status
fetch('/nurse/chat/check-status/{userId}')
    .then(res => res.json())
    .then(data => console.log(data));
```

## Files Modified/Created

### Created:
- `app/Http/Middleware/UserOnlineMiddleware.php`

### Modified:
- `app/Events/UserOnlineStatus.php` - Added global channel broadcast
- `app/Http/Kernel.php` - Added middleware alias
- `routes/web.php` - Added online status routes and middleware
- `resources/views/nurse/chat/conversation.blade.php` - Added real-time status tracking
- `resources/js/chat.js` - Enhanced presence tracking for healthcare

## Security Considerations

1. **Authentication Required:** All status routes require authentication
2. **CSRF Protection:** All AJAX requests include CSRF token
3. **Authorization:** Users can only access their own conversations
4. **Rate Limiting:** Heartbeat has built-in throttling (30s-2min intervals)

## Performance Optimization

1. **Cache-based Status:** Uses Laravel cache (5-minute expiry)
2. **Efficient Broadcasting:** Uses `ShouldBroadcastNow` for immediate delivery
3. **Minimal UI Updates:** Only updates when status actually changes
4. **Beacon API:** Uses `navigator.sendBeacon` for reliable offline notification

## Future Enhancements

1. Add "Last seen" timestamp for offline users
2. Show typing indicator with user name
3. Add push notifications for new messages
4. Implement user activity detection (mouse/keyboard)
5. Add status preferences (appear offline mode)

# 🔔 Message Notification Count - Implementation Guide

## ✅ Feature Implemented

Real-time notification count for new messages displayed on the header bell icon with:
- **Red badge** showing unread message count
- **Real-time updates** via Pusher
- **Browser notifications** (optional)
- **Page title updates** with count
- **Dropdown menu** with message count

---

## 📋 What Was Created

### **1. Event Class**
**File:** `app/Events/NewMessageNotification.php`
- Broadcasts to user's private channel when new message arrives
- Contains message details for notification

### **2. JavaScript Counter**
**File:** `resources/js/chat-notification-counter.js`
- Initializes on page load
- Listens for real-time events via Pusher
- Updates badge display dynamically
- Polls server every 30s as backup

### **3. Controller Update**
**File:** `app/Http/Controllers/ChatController.php`
- Broadcasts notification to recipient when message is sent
- Sends to user's private channel `user.{userId}`

### **4. Layout Updates**
**Files Updated:**
- `resources/views/nurse/layouts/header.blade.php` - Added user ID and badge styling
- `resources/views/nurse/layouts/js.blade.php` - Added notification script
- `vite.config.js` - Added notification counter to build

---

## 🎯 How It Works

### **Flow:**
1. **User A** sends message to **User B**
2. Backend broadcasts `NewMessageNotification` event
3. Event sent to `user.{UserB_ID}` private channel
4. **User B's** browser receives event via Pusher
5. Notification counter increments
6. Red badge appears on bell icon
7. Page title updates with count

### **Display Features:**
- **Badge**: Red circle with count (max shows "99+")
- **Pulse Animation**: Attracts attention
- **Dropdown**: Shows "X messages"
- **Title**: "(X) Page Title"

---

## 🚀 Test It Now

### **Step 1: Access Chat**
Open browser and login as Nurse user:
```
http://localhost/mediqa_nurse/nurse/chat
```

### **Step 2: Check Console**
Open browser console (F12) and look for:
```
🔔 Initializing Chat Notification Counter for user: X
✅ ChatNotificationCounter initialized
📊 Unread count loaded: 0
✅ Real-time notification listener setup complete
```

### **Step 3: Test Real-Time Notification**

**Setup:**
1. Open **two browsers** (Chrome and Edge)
2. Login as **different users**
3. Both should have chat pages open

**Test:**
1. **Browser 1 (Sender)**: Send a message
2. **Browser 2 (Receiver)**: Watch for notification

**Expected Result on Receiver's Browser:**
```
📨 New message notification received: {...}
```

**Visual Changes:**
- ✅ Red badge appears on bell icon with count
- ✅ Badge pulses to attract attention
- ✅ Dropdown text updates to "1 message"
- ✅ Page title shows "(1) MediQa..."

---

## 🔧 Features Breakdown

### **1. Badge Display**
```html
<a class="btn btn-notify position-relative" id="dropdownNotify">
  <i class="fa-regular fa-bell"></i>
  <span class="notify-badge">3</span>  <!-- Red badge -->
</a>
```

### **2. Real-Time Listener**
```javascript
const channel = echo.private(`user.${userId}`);
channel.bind('new.message', (data) => {
    this.incrementCount(); // Increment badge
});
```

### **3. Server Broadcast**
```php
broadcast(new NewMessageNotification(
    $recipientUserId,
    $conversationId,
    $message
));
```

---

## 📊 API Endpoints Used

### **Get Unread Count**
```
GET /nurse/chat/unread-count
Response: { "unread_count": 5 }
```

---

## 🎨 Styling

### **Badge CSS:**
```css
.notify-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;  /* Red */
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    animation: notifyPulse 2s infinite;
}
```

### **Pulse Animation:**
```css
@keyframes notifyPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}
```

---

## ⚠️ Important Notes

### **Queue Worker Required**
Keep queue worker running for real-time broadcasts:
```bash
php artisan queue:work
```

### **Pusher Configuration**
Make sure Pusher is configured correctly:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=1362561
PUSHER_APP_KEY=eccb46d7d4565e48b9cc
PUSHER_APP_SECRET=0cd99508572c7b9dfa35
PUSHER_APP_CLUSTER=ap2
```

### **Browser Notifications**
For browser notifications to work:
1. User must grant notification permission
2. Browser tab must be in background (`document.hidden`)
3. HTTPS required in production

---

## 🐛 Troubleshooting

### **Issue: Badge not appearing**

**Check Console:**
```javascript
// Should see these logs:
🔔 Initializing Chat Notification Counter for user: X
✅ ChatNotificationCounter initialized
```

**If not seeing logs:**
- Check if script loaded: View page source, search for `chat-notification-counter`
- Check if user ID is set: Inspect header element, look for `data-user-id`
- Check for JavaScript errors in console

### **Issue: Count not updating in real-time**

**Check Console:**
```
// Should see when message arrives:
📨 New message notification received: {...}
```

**If not receiving:**
1. Check if Pusher is connected:
   ```javascript
   console.log(window.Echo.connector.pusher.connection.state);
   // Should be "connected"
   ```
2. Check if subscribed to user channel:
   ```javascript
   console.log(window.Echo.channels());
   // Should include "private-user.X"
   ```
3. Check queue worker is running
4. Check Laravel logs for broadcast errors

### **Issue: Count shows 0 but should have messages**

**Manual refresh:**
```javascript
window.ChatNotificationCounter.loadUnreadCount();
```

**Reset count:**
```javascript
window.ChatNotificationCounter.resetCount();
```

**Check current count:**
```javascript
console.log(window.ChatNotificationCounter.unreadCount);
```

---

## 🔍 Debug Commands

### **In Browser Console:**

```javascript
// Check if counter is initialized
console.log(window.ChatNotificationCounter);

// Check current unread count
console.log(window.ChatNotificationCounter.unreadCount);

// Manually load count
window.ChatNotificationCounter.loadUnreadCount();

// Manually increment (for testing)
window.ChatNotificationCounter.incrementCount();

// Reset count
window.ChatNotificationCounter.resetCount();

// Check Pusher connection
console.log(window.Echo.connector.pusher.connection.state);

// Check subscribed channels
console.log(window.Echo.channels());
```

### **In Laravel:**

```bash
# Check queue worker status
php artisan queue:monitor database

# Check failed jobs
php artisan queue:failed

# View logs
tail -f storage/logs/laravel.log
```

---

## 📱 Browser Notification Permission

To request notification permission from users:

```javascript
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
```

---

## ✅ Success Indicators

You'll know it's working when:

1. ✅ Badge appears on bell icon when new message arrives
2. ✅ Badge shows correct count
3. ✅ Badge pulses to attract attention
4. ✅ Dropdown text updates to "X messages"
5. ✅ Page title shows "(X) MediQa..."
6. ✅ Console shows "📨 New message notification received"
7. ✅ Badge disappears when user views messages

---

## 🎉 Next Steps (Optional Enhancements)

### **1. Click to Clear**
Clear count when user clicks bell icon:
```javascript
document.getElementById('dropdownNotify').addEventListener('click', function() {
    window.ChatNotificationCounter.resetCount();
});
```

### **2. Mark as Read**
Mark messages as read when viewing conversation:
```javascript
// In conversation view
window.ChatNotificationCounter.resetCount();
```

### **3. Notification Sound**
Play sound when new message arrives (already implemented in chat)

### **4. Desktop Notifications**
Show browser notification (already implemented, needs permission)

---

## 📝 Files Modified/Created

### **Created:**
- ✅ `app/Events/NewMessageNotification.php`
- ✅ `resources/js/chat-notification-counter.js`
- ✅ `NOTIFICATION_COUNT_GUIDE.md` (this file)

### **Modified:**
- ✅ `app/Http/Controllers/ChatController.php`
- ✅ `resources/views/nurse/layouts/header.blade.php`
- ✅ `resources/views/nurse/layouts/js.blade.php`
- ✅ `vite.config.js`

---

## 🚀 Test Summary

**Quick Test:**
1. Open two browsers
2. Login as different users
3. Send message from Browser 1
4. Check Browser 2 for:
   - Red badge on bell icon ✅
   - Console log "📨 New message notification received" ✅
   - Page title updated with count ✅

---

**Need help? Check the Troubleshooting section or open browser console for debug info!** 🔍

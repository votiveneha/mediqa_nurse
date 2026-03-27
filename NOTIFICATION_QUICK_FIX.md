# 🔔 Notification Counter - Quick Fix & Test

## Issue
Notification counter script might not be loading properly.

## Quick Test - Paste in Browser Console

Go to: `http://localhost/mediqa_nurse/nurse/chat`

**Paste this in console (F12):**

```javascript
// Check if script loaded
console.log('=== Notification Test ===');
console.log('1. Counter exists:', typeof window.ChatNotificationCounter !== 'undefined');
console.log('2. User ID:', window.ChatNotificationCounter?.userId);
console.log('3. Echo exists:', typeof window.Echo !== 'undefined');

// Check dropdown
const dropdown = document.querySelector('a.dropdown-item[href*="messages"]');
console.log('4. Dropdown exists:', !!dropdown);
console.log('5. Dropdown text:', dropdown?.textContent);

// Check badge
const bell = document.querySelector('#dropdownNotify');
const badge = bell?.querySelector('.notify-badge');
console.log('6. Badge exists:', !!badge);
console.log('7. Badge text:', badge?.textContent);

// Test increment
if (window.ChatNotificationCounter) {
    window.ChatNotificationCounter.incrementCount();
    console.log('8. After increment - Count:', window.ChatNotificationCounter.unreadCount);
    console.log('9. After increment - Dropdown:', document.querySelector('a.dropdown-item[href*="messages"]')?.textContent);
}
```

---

## Expected Output

**If working:**
```
=== Notification Test ===
1. Counter exists: true
2. User ID: 123
3. Echo exists: true
4. Dropdown exists: true
5. Dropdown text: "0 messages"
6. Badge exists: false (or true if already has messages)
7. Badge text: (none or number)
8. After increment - Count: 1
9. After increment - Dropdown: "1 message"
```

**If NOT working:**
```
1. Counter exists: false  ❌ Script not loaded
3. Echo exists: false     ❌ Pusher not initialized
4. Dropdown exists: false ❌ Dropdown element missing
```

---

## Fix Steps

### If Counter NOT exists (script not loaded):

**Check if Vite asset is in page:**
1. Right-click page → View Page Source
2. Search for: `chat-notification-counter`
3. Should see: `<script type="module" src="http://localhost/mediqa_nurse/build/assets/chat-notification-counter-xxxx.js"></script>`

**If NOT there:**
- The `@vite()` directive isn't working
- Need to manually add script tag

### Manual Fix (if Vite not working):

Add this to `resources/views/nurse/layouts/js.blade.php` at the very end:

```html
<script>
// Inline notification counter
(function() {
    window.ChatNotificationCounter = {
        userId: {{ Auth::guard('nurse_middle')->id() ?? 0 }},
        unreadCount: 0,
        
        init: function() {
            console.log('🔔 Notification counter initialized for user:', this.userId);
            this.loadUnreadCount();
        },
        
        loadUnreadCount: function() {
            fetch('/nurse/chat/unread-count')
                .then(r => r.json())
                .then(data => {
                    this.updateCount(data.unread_count || 0);
                    console.log('📊 Unread count:', data.unread_count);
                })
                .catch(e => console.error('❌ Error:', e));
        },
        
        updateCount: function(count) {
            this.unreadCount = count;
            this.updateDisplay();
        },
        
        incrementCount: function() {
            this.unreadCount++;
            this.updateDisplay();
        },
        
        updateDisplay: function() {
            // Update dropdown text
            const msgLink = document.querySelector('a.dropdown-item[href*="messages"]');
            if (msgLink) {
                msgLink.textContent = this.unreadCount + ' message' + (this.unreadCount !== 1 ? 's' : '');
            }
            
            // Update/add badge
            const bell = document.querySelector('#dropdownNotify');
            if (bell) {
                let badge = bell.querySelector('.notify-badge');
                if (this.unreadCount > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'notify-badge';
                        badge.style.cssText = 'position:absolute;top:-5px;right:-5px;background:#dc3545;color:white;font-size:10px;font-weight:bold;padding:2px 6px;border-radius:10px;min-width:18px;text-align:center;';
                        bell.style.position = 'relative';
                        bell.appendChild(badge);
                    }
                    badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                } else if (badge) {
                    badge.remove();
                }
            }
        }
    };
    
    // Auto-init
    setTimeout(() => window.ChatNotificationCounter.init(), 1000);
})();
</script>
```

---

## Test Real-Time

**Two browsers test:**

1. **Browser 1**: Login as Nurse A
2. **Browser 2**: Login as Healthcare B  
3. **Browser 1**: Send message to B
4. **Browser 2**: Check console for:
   ```
   📨 New message notification received
   ```
5. **Check dropdown**: Should show "1 message"

---

## Common Issues

### 1. Script Not Loading
**Symptom:** `Counter exists: false`
**Fix:** Add inline script (see above)

### 2. Dropdown Not Found
**Symptom:** `Dropdown exists: false`
**Fix:** Check if header has the dropdown element

### 3. Echo Not Available
**Symptom:** `Echo exists: false`
**Fix:** Make sure Pusher is initialized in chat page

### 4. Count Not Updating
**Symptom:** Increment works but display doesn't change
**Fix:** Check if dropdown selector matches: `a.dropdown-item[href*="messages"]`

---

## Quick Debug Commands

```javascript
// Reload counter script location
console.log(document.querySelector('script[src*="chat-notification"]')?.src);

// Manually set count
window.ChatNotificationCounter.updateCount(5);

// Force display update
window.ChatNotificationCounter.updateDisplay();

// Check all dropdown items
document.querySelectorAll('.dropdown-item').forEach(el => {
    console.log('Dropdown item:', el.textContent.trim());
});
```

---

**Try the console test first and share the output!** 🔍

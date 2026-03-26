# Pusher Real-Time Chat Fix - MediQa

## Problem
The chat system requires a page refresh to show new messages instead of working in real-time, even though Pusher is configured.

## Root Causes Identified

### 1. **BroadcastServiceProvider Disabled**
The `BroadcastServiceProvider` was commented out in `config/app.php`, preventing Laravel from registering broadcasting routes.

**Fixed:** Uncommented the line in `config/app.php`:
```php
App\Providers\BroadcastServiceProvider::class,
```

### 2. **Pusher Configuration Issues**
The broadcasting configuration needed SSL verification disabled for localhost development.

**Fixed:** Updated `config/broadcasting.php` to add curl options:
```php
'curl_options' => [
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
],
```

### 3. **Frontend Echo Initialization**
The conversation view was using outdated CDN versions and incorrect configuration access.

**Issue:** Using `env()` helper in views which doesn't work in production cache.

**Fixed:** Changed from:
```javascript
key: '{{ env("PUSHER_APP_KEY") }}'
```

To:
```javascript
key: '{{ config('broadcasting.connections.pusher.key') }}'
```

## Files Modified

1. ✅ `config/app.php` - Uncommented BroadcastServiceProvider
2. ✅ `config/broadcasting.php` - Added curl SSL options for localhost
3. ⚠️ `resources/views/nurse/chat/conversation.blade.php` - Needs manual update (see below)

## Manual Steps Required

### Update the conversation.blade.php file

Replace the script section at the end of the file with this updated version:

```blade
@endsection

{{-- Load Pusher and Laravel Echo from CDN --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
<script>
(function() {
    'use strict';

    console.log('=== Initializing Pusher & Laravel Echo ===');

    // Setup Laravel Echo with Pusher
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
        forceTLS: true,
        encrypted: true,
        authEndpoint: '{{ url('/broadcasting/auth') }}',
        auth: {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        },
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });

    // Setup Laravel data
    window.Laravel = {
        userId: {{ Auth::guard('nurse_middle')->id() }},
        userName: '{{ Auth::guard('nurse_middle')->user()->name }} {{ Auth::guard('nurse_middle')->user()->lastname ?? '' }}',
        userRole: {{ Auth::guard('nurse_middle')->user()->role }},
        csrfToken: '{{ csrf_token() }}',
        conversationId: {{ $conversation->id }},
        userAvatar: '{{ Auth::guard('nurse_middle')->user()->profile_img ?? 'nurse/assets/imgs/nurse06.png' }}'
    };

    console.log('Echo initialized, subscribing to channel...');

    // Listen for real-time messages on private channel
    const channel = Echo.private('conversation.' + window.Laravel.conversationId);

    channel.error(function(error) {
        console.error('=== Pusher Channel Error ===', error);
    });

    channel.listen('.message.sent', function(data) {
        console.log('=== Real-time Message Received ===', data);

        const messagesContainer = document.getElementById('chatMessages');
        if (!messagesContainer) {
            console.error('Chat messages container not found!');
            return;
        }

        const isSentByMe = data.sender_id == window.Laravel.userId;

        // Don't display if it's our own message (already shown in UI)
        if (isSentByMe) {
            console.log('Skipping own message');
            return;
        }

        const messageHtml = `
            <div class="message ${isSentByMe ? 'sent' : 'received'}" data-message-id="${data.id}">
                ${!isSentByMe ? `
                <img src="${data.sender_avatar || window.Laravel.userAvatar}" alt="${data.sender_name}" class="message-avatar">
                ` : ''}
                <div class="message-content">
                    <p class="message-text">${escapeHtml(data.message)}</p>
                    <span class="message-time">${formatTime(data.created_at)}</span>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Play notification sound
        playNotificationSound();
    });

    console.log('=== Message listener attached ===');

    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(isoString) {
        const date = new Date(isoString);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQQAKZXZ8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBo=');
        audio.play().catch(() => {});
    }

    // Attach form handler when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const submitBtn = document.querySelector('.btn-send');
        const messagesContainer = document.getElementById('chatMessages');

        if (messageForm && messageInput && submitBtn && messagesContainer) {
            messageForm.onsubmit = function(e) {
                e.preventDefault();
                e.stopPropagation();

                const formData = new FormData(this);
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch('{{ route('nurse.chat.send') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.message) {
                        const messageHtml = `
                            <div class="message sent" data-message-id="${data.message.id}">
                                <img src="${window.Laravel.userAvatar}" alt="${data.message.sender.name}" class="message-avatar">
                                <div class="message-content">
                                    <p class="message-text">${escapeHtml(data.message.message)}</p>
                                    <span class="message-time">${formatTime(data.message.created_at)}</span>
                                </div>
                            </div>
                        `;

                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        messageInput.value = '';
                    } else {
                        alert(data.error || 'Failed to send message');
                    }
                })
                .catch(error => {
                    alert('Failed to send message: ' + error.message);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Send';
                });
            };

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
})();
</script>
```

## Testing

### 1. Test Pusher Connection
Visit: `http://localhost/mediqa_nurse/test-pusher.html`

This will show:
- Your Pusher configuration (App ID: 1362561, Key: eccb46d7d4565e48b9cc, Cluster: ap2)
- Connection status
- Real-time event logs

**What to look for:**
- ✅ "Connected to Pusher!" - Success!
- ❌ "Connection failed" - Check your internet and Pusher credentials

### 2. Test Chat
1. Open two different browsers (e.g., Chrome and Firefox)
2. Login as different users in each browser
3. Start a conversation between them
4. Send a message from one browser
5. The message should appear instantly in the other browser WITHOUT refreshing

## Troubleshooting

### Check Logs
Open browser console (F12) and look for:
- "=== Initializing Pusher & Laravel Echo ==="
- "Echo initialized, subscribing to channel..."
- "=== Message listener attached ==="
- "=== Real-time Message Received ===" (when message arrives)

### Common Issues

**1. "Pusher key not configured"**
- Check your `.env` file has correct Pusher credentials
- Run `php artisan config:clear`

**2. "Authorization error"**
- Check `/broadcasting/auth` route is working
- Verify you're logged in with the correct guard

**3. Messages not appearing**
- Check browser console for errors
- Verify the channel name matches: `conversation.{conversationId}`
- Check `routes/channels.php` authorization logic

**4. Connection state: disconnected**
- Check firewall allows WebSocket connections
- Try changing `forceTLS` to `false` for local testing
- Verify Pusher App credentials are correct

## Clear Config Cache
After making changes, always run:
```bash
php artisan config:clear
php artisan cache:clear
```

## Next Steps

1. ✅ Apply the manual update to `resources/views/nurse/chat/conversation.blade.php`
2. ✅ Test Pusher connection at `/test-pusher.html`
3. ✅ Test real-time chat between two browsers
4. ✅ Apply same fix to healthcare facility chat view if needed

## Additional Notes

- The healthcare facilities chat view may need the same update
- Check `resources/views/healthcare/chat/conversation.blade.php` for similar issues
- Consider using Laravel Mix/Vite to bundle Pusher/Echo instead of CDN

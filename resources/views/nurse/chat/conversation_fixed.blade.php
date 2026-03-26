@endsection

{{-- Load Pusher and Laravel Echo from CDN --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
<script>
(function() {
    'use strict';

    console.log('=== Initializing Pusher & Laravel Echo ===');
    console.log('Pusher Key:', '{{ config('broadcasting.connections.pusher.key') }}');
    console.log('Cluster:', '{{ env("PUSHER_APP_CLUSTER") }}');
    console.log('Conversation ID:', {{ $conversation->id }});

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

    console.log('Laravel data:', window.Laravel);
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
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQQAKZXZ8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBo=');
        audio.play().catch(() => {});
    }

    // Attach form handler when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const submitBtn = document.querySelector('.btn-send');
        const messagesContainer = document.getElementById('chatMessages');

        console.log('Chat elements found:', {
            form: !!messageForm,
            input: !!messageInput,
            btn: !!submitBtn,
            container: !!messagesContainer
        });

        if (messageForm && messageInput && submitBtn && messagesContainer) {
            messageForm.onsubmit = function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Form submitted');

                const formData = new FormData(this);
                console.log('Form data:', Object.fromEntries(formData));

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
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);

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
                        console.error('Error from server:', data);
                        alert(data.error || 'Failed to send message');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Failed to send message: ' + error.message);
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Send';
                });
            };

            console.log('Chat form handler attached');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else {
            console.error('Chat elements not found');
        }
    });
})();
</script>

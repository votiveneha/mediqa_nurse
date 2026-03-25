@extends('nurse.layouts.layout')

@section('title', 'Chat - ' . ($otherParticipant->name ?? 'Conversation'))

@section('content')
    <style>
        .chat-wrapper {
            display: flex;
            height: calc(100vh - 155px);
            background: #f5f7fa;
        }

        .chat-sidebar {
            width: 320px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-back {
            background: none;
            border: none;
            color: #000;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /*
    .btn-back:hover {
        color: #0056b3;
    } */

        .chat-search {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 13px;
        }

        .chat-search:focus {
            outline: none;
            border-color: #007bff;
        }

        .conversation-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item-compact {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f5f5f5;
        }

        .conversation-item-compact:hover {
            background: #f8f9fa;
        }

        .conversation-item-compact.active {
            background: #007bff;
            color: #fff;
        }

        .conversation-avatar-compact {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .conversation-info-compact {
            flex: 1;
            overflow: hidden;
        }

        .conversation-name-compact {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .conversation-last-message-compact {
            color: #888;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-item-compact.active .conversation-last-message-compact {
            color: #e0e0e0;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .chat-header {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-header-title {
            font-size: 16px;
            font-weight: 600;
        }

        .chat-header-subtitle {
            font-size: 13px;
            color: #dc3545;
        }

        .chat-messages {
            flex: 1;
            padding: 14px 10px;
            overflow-y: auto;
            background: #fff;
        }

        .message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .message.sent {
            flex-direction: row-reverse;
        }

        .message.sent .message-avatar {
            margin-right: 0;
            margin-left: 12px;
        }

        .message.sent .message-content {
            background: #f1f3f4;
            color: #fff;
        }

        .message.received .message-content {
            background: #f1f3f4;
            color: #333;
        }

        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px;
            max-width: 60%;
        }

        .message-text {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .chat-input-area {
            padding: 10px 12px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .chat-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .chat-input:focus {
            outline: none;
            border-color: #007bff;
        }

        .btn-send {
            background: #28a745;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-send:hover {
            background: #218838;
        }

        .typing-indicator {
            display: none;
            padding: 10px 30px;
            font-size: 13px;
            color: #888;
            font-style: italic;
        }

        .chat-btn {
            background: #000;
            color: #fff;
            display: inline-flex;
            transition: all ease-in-out .3s;
            font-size: 13px;
            padding: 5px 12px;
            height: fit-content;
            border: 1px solid #000;
        }

        .chat-btn:hover {
            background: #fff;
            border: 1px solid #000;
            color: #000;
        }

        .chat__input {
            display: flex;
            gap: 15px;
            width: 100%;
            align-items: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>

    <div class="chat-wrapper">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <button class="btn-back"
                    onclick="window.location.href='{{ Auth::guard('healthcare_facilities')->user()->role === 2 ? route('healthcare.chat.index') : route('nurse.chat.index') }}'">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>

            <div class="conversation-list-compact" id="conversationList">
                @php
                    $allConversations = \App\Models\Conversation::with(['nurse', 'latestMessage'])
                        ->where('healthcare_id', Auth::guard('healthcare_facilities')->id())
                        ->where('healthcare_deleted', 0)
                        ->orderBy('last_message_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp

                @foreach($allConversations as $conv)
                    @if($conv->id !== $conversation->id)
                        @php
                            $other = $conv->getOtherParticipant(Auth::guard('healthcare_facilities')->id());
                            $unread = $conv->unreadCount(Auth::guard('healthcare_facilities')->id());
                        @endphp
                        <div class="conversation-item-compact {{ $conv->id == $conversation->id ? 'active' : '' }}"
                            onclick="window.location.href='{{ route('healthcare.chat.show', $conv->id) }}'">
                            <img src="{{ asset($other->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" alt="{{ $other->name }}"
                                class="conversation-avatar-compact">
                            <div class="conversation-info-compact">
                                <div class="conversation-name-compact">{{ $other->name }} {{ $other->lastname ?? '' }}</div>
                                @if($conv->latestMessage)
                                    <div class="conversation-last-message-compact">
                                        {{ Str::limit($conv->latestMessage->message, 30) }}
                                    </div>
                                @endif
                            </div>
                            @if($unread > 0)
                                <span
                                    style="background: #007bff; color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 10px;">{{ $unread }}</span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-user-info">
                    <img src="{{ asset($otherParticipant->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}"
                        alt="{{ $otherParticipant->name }}" class="chat-user-avatar">
                    <div>
                        <div class="chat-header-title">{{ $otherParticipant->name }} {{ $otherParticipant->lastname ?? '' }}
                        </div>
                        <div class="chat-header-subtitle">
                            <i class="fas fa-circle" style="font-size: 8px;"></i> {{ $isOnline ? 'Online' : 'Offline' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatMessages">
                @foreach($conversation->messages as $message)
                    @if(!$message->deleted_by_sender && !$message->deleted_by_receiver)
                        @php
                            $isSent = $message->sender_id == Auth::id();
                        @endphp
                        <div class="message {{ $isSent ? 'sent' : 'received' }}">
                            @if(!$isSent)
                                <img src="{{ asset($message->sender->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}"
                                    alt="{{ $message->sender->name }}" class="message-avatar">
                            @endif
                            <div class="message-content">
                                <p class="message-text">{{ nl2br(e($message->message)) }}</p>
                            </div>
                            @if($isSent)
                                <img src="{{ asset(Auth::user()->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}"
                                    alt="{{ Auth::user()->name }}" class="message-avatar">
                            @endif
                        </div>
                    @endif
                @endforeach

                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator">
                    <i class="fas fa-circle"></i> {{ $otherParticipant->name }} is typing...
                </div>
            </div>

            <!-- Chat Input -->
            <div class="chat-input-area">
                <form id="messageForm" style="" class="chat__input">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <input type="text" name="message" class="chat-input" placeholder="Type message" id="messageInput"
                        autocomplete="off">
                    <button type="submit" class="chat-btn">Send</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.10.0/dist/echo.iife.js"></script>
        <script>
            (function () {
                'use strict';

                // Setup Laravel Echo with Pusher
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '{{ env("PUSHER_APP_KEY") }}',
                    cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
                    forceTLS: true,
                    encrypted: true,
                    authEndpoint: '{{ url('/broadcasting/auth') }}',
                    auth: {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                });

                window.Laravel = {
                    userId: {{ Auth::guard('healthcare_facilities')->id() }},
                    userName: '{{ Auth::guard('healthcare_facilities')->user()->name }}',
                    userRole: {{ Auth::guard('healthcare_facilities')->user()->role }},
                    csrfToken: '{{ csrf_token() }}',
                    conversationId: {{ $conversation->id }}
            };

                // Listen for real-time messages
                Echo.private('conversation.' + window.Laravel.conversationId)
                    .listen('.message.sent', function(data) {
                        console.log('Real-time message received:', data);

                        const messagesContainer = document.getElementById('chatMessages');
                        const isSentByMe = data.sender_id == window.Laravel.userId;

                        const messageHtml = `
                            <div class="message ${isSentByMe ? 'sent' : 'received'}" data-message-id="${data.id}">
                                ${!isSentByMe ? `
                                <img src="${data.sender_avatar || '/nurse/assets/imgs/nurse06.png'}" alt="${data.sender_name}" class="message-avatar">
                                ` : ''}
                                <div class="message-content">
                                    <p class="message-text">${data.message}</p>
                                </div>
                                ${isSentByMe ? `
                                <img src="${data.sender_avatar || '/nurse/assets/imgs/nurse06.png'}" alt="${data.sender_name}" class="message-avatar">
                                ` : ''}
                            </div>
                        `;

                        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;

                        // Play notification sound if message is from someone else
                        if (!isSentByMe) {
                            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQQAKZXZ8NOmdhoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBo=');
                            audio.play().catch(() => {});
                        }
                    });

                // Wait for everything to load
                setTimeout(function () {
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
                        messageForm.addEventListener('submit', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            console.log('Form submitted');

                            const formData = new FormData(this);

                            // Disable button while sending
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                            fetch('{{ route('healthcare.chat.send') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Response:', data);

                                    if (data.success && data.message) {
                                        const userAvatar = '{{ Auth::user()->profile_img ?? 'nurse/assets/imgs/nurse06.png' }}';
                                        const messageHtml = `
                                    <div class="message sent" data-message-id="${data.message.id}">
                                        <img src="${userAvatar}" alt="${data.message.sender.name}" class="message-avatar">
                                        <div class="message-content">
                                            <p class="message-text">${data.message.message}</p>
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
                                    console.error('Error:', error);
                                    alert('Failed to send message');
                                })
                                .finally(() => {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Send';
                                });
                        });

                        console.log('Chat form handler attached');
                    } else {
                        console.error('Chat elements not found');
                    }
                }, 500);
            })();
        </script>
    @endpush
@endsection

<script>
window.addEventListener('load', function() {
    console.log('Page loaded, initializing chat...');

    setTimeout(function() {
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const submitBtn = document.querySelector('.chat-btn');
        const messagesContainer = document.getElementById('chatMessages');

        console.log('Elements found:', {
            form: messageForm ? 'YES' : 'NO',
            input: messageInput ? 'YES' : 'NO',
            btn: submitBtn ? 'YES' : 'NO',
            container: messagesContainer ? 'YES' : 'NO'
        });

        if (messageForm && messageInput && submitBtn && messagesContainer) {
            messageForm.onsubmit = function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Sending message...');

                const formData = new FormData(this);
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Sending...';

                fetch('{{ route('healthcare.chat.send') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(r => r.json())
                .then(data => {
                    console.log('Response:', data);
                    if (data.success && data.message) {
                        const userAvatar = '{{ Auth::user()->profile_img ?? 'nurse/assets/imgs/nurse06.png' }}';
                        const msgHtml = '<div class="message sent"><img src="' + userAvatar + '" class="message-avatar"><div class="message-content"><p class="message-text">' + data.message.message + '</p></div></div>';
                        messagesContainer.insertAdjacentHTML('beforeend', msgHtml);
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        messageInput.value = '';
                    } else {
                        alert(data.error || 'Failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error sending message');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Send';
                });
                return false;
            };
            console.log('Chat handler attached!');
        } else {
            console.error('Some elements not found');
        }
    }, 500);
});
</script>
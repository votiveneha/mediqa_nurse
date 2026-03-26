@extends('nurse.layouts.layout')

@section('title', 'Chat - ' . ($otherParticipant->name ?? 'Conversation'))

@section('content')
    <style>
        .chat-wrapper {
            display: flex;
            height: calc(100vh - 60px);
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
            color: #007bff;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-back:hover {
            color: #0056b3;
        }

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
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-header-title {
            font-size: 18px;
            font-weight: 600;
        }

        .chat-header-subtitle {
            font-size: 13px;
            color: #28a745;
            margin-top: 3px;
        }

        .chat-messages {
            flex: 1;
            padding: 30px;
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
            padding: 20px 30px;
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
    </style>

    <div class="chat-wrapper">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <button class="btn-back" onclick="window.location.href='{{ route('nurse.chat.index') }}'">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>

            <div class="conversation-list-compact" id="conversationList">
                @php
                    $allConversations = \App\Models\Conversation::with(['healthcare', 'latestMessage'])
                        ->where('nurse_id', Auth::guard('nurse_middle')->id())
                        ->where('nurse_deleted', 0)
                        ->orderBy('last_message_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp

                @foreach($allConversations as $conv)
                    @if($conv->id !== $conversation->id)
                        @php
                            $other = $conv->getOtherParticipant(Auth::guard('nurse_middle')->id());
                            $unread = $conv->unreadCount(Auth::guard('nurse_middle')->id());
                        @endphp
                        <div class="conversation-item-compact {{ $conv->id == $conversation->id ? 'active' : '' }}"
                            onclick="window.location.href='{{ route('nurse.chat.show', $conv->id) }}'">
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
                            <!-- @if($conversation->job)
                                <span style="font-size: 14px; color: #888; font-weight: normal; margin-left: 10px;">
                                    - {{ $conversation->job->title ?? $conversation->job->job_title ?? '' }}
                                </span>
                            @endif -->
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
                            $isSent = $message->sender_id == Auth::guard('nurse_middle')->id();
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
                                <img src="{{ asset(Auth::guard('nurse_middle')->user()->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}"
                                    alt="{{ Auth::guard('nurse_middle')->user()->name }}" class="message-avatar">
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
                <form id="messageForm" style="display: flex; gap: 15px; width: 100%;">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    <input type="text" name="message" class="chat-input" placeholder="Type message" id="messageInput"
                        autocomplete="off">
                    <button type="submit" class="btn-send">Send</button>
                </form>
            </div>
        </div>
    </div>

@endsection

{{-- Load Pusher and Laravel Echo from CDN --}}
<script src="https://js.pusher.com/8.4/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>
<script>
    (function () {
        'use strict';

        console.log('=== Initializing Pusher & Laravel Echo ===');
        console.log('Pusher Key:', '{{ config("broadcasting.connections.pusher.key") }}');
        console.log('Cluster:', '{{ env("PUSHER_APP_CLUSTER") }}');
        console.log('Conversation ID:', {{ $conversation->id }});

        // Setup Laravel Echo with Pusher
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true,
            encrypted: true,
            authEndpoint: '{{ url("/broadcasting/auth") }}',
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

        channel.error(function (error) {
            console.error('=== Pusher Channel Error ===', error);
        });

        channel.listen('.message.sent', function (data) {
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
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleQQAKZXZ8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBoCLJ7a8NOndBo');
            audio.play().catch(() => { });
        }

        // Attach form handler when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
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
                messageForm.onsubmit = function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Form submitted');

                    const formData = new FormData(this);
                    console.log('Form data:', Object.fromEntries(formData));

                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    fetch('{{ route("nurse.chat.send") }}', {
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
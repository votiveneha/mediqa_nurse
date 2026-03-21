@extends('nurse.layouts.layout')

@section('title', 'Chat - ' . ($otherParticipant->name ?? 'Conversation'))

@section('content')
<div class="chat-container" data-conversation-id="{{ $conversation->id }}">
    <div class="row no-gutters chat-wrapper">
        <!-- Sidebar - Conversation List (Compact) -->
        <div class="col-md-4 col-lg-3 chat-sidebar-compact">
            <div class="conversation-header">
                <a href="{{ route('nurse.chat.index') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h4>Messages</h4>
            </div>

            <div class="conversation-search-compact">
                <input type="text" class="form-control" placeholder="Search..." id="searchConversations">
            </div>

            <div class="conversation-list-compact" id="conversationList">
                @php
                    $allConversations = \App\Models\Conversation::with(['healthcare', 'latestMessage'])
                        ->where('nurse_id', Auth::id())
                        ->where('nurse_deleted', 0)
                        ->orderBy('last_message_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp

                @foreach($allConversations as $conv)
                    @if($conv->id !== $conversation->id)
                        @php
                            $other = $conv->getOtherParticipant(Auth::id());
                            $unread = $conv->unreadCount(Auth::id());
                        @endphp
                        <div class="conversation-item-compact {{ $conv->id == $conversation->id ? 'active' : '' }}"
                             onclick="window.location.href='{{ route('nurse.chat.show', $conv->id) }}'">
                            <div class="conversation-avatar-compact">
                                <img src="{{ asset($other->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" alt="{{ $other->name }}">
                            </div>
                            <div class="conversation-info-compact">
                                <h6>{{ $other->name }} {{ $other->lastname ?? '' }}</h6>
                                @if($conv->latestMessage)
                                    <p class="last-message-compact">
                                        {{ Str::limit($conv->latestMessage->message, 25) }}
                                    </p>
                                @endif
                            </div>
                            @if($unread > 0)
                                <span class="badge badge-primary unread-badge-compact">{{ $unread }}</span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9 chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-user-info">
                    <img src="{{ asset($otherParticipant->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" alt="{{ $otherParticipant->name }}">
                    <div>
                        <h5>{{ $otherParticipant->name }} {{ $otherParticipant->lastname ?? '' }}</h5>
                        <span class="online-status {{ $isOnline ? 'online' : 'offline' }}">
                            <i class="fas fa-circle"></i> {{ $isOnline ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="btn btn-sm btn-outline-info" title="View Profile" onclick="viewProfile({{ $otherParticipant->id }})">
                        <i class="fas fa-user"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" title="Block User" data-toggle="modal" data-target="#blockUserModal">
                        <i class="fas fa-ban"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" title="Delete Conversation" data-toggle="modal" data-target="#deleteConversationModal">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="chat-messages" id="chatMessages">
                @foreach($conversation->messages as $message)
                    @if(!$message->deleted_by_sender && !$message->deleted_by_receiver)
                        <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}"
                             data-message-id="{{ $message->id }}">
                            @if($message->sender_id !== Auth::id())
                                <div class="message-avatar">
                                    <img src="{{ asset($message->sender->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" alt="{{ $message->sender->name }}">
                                </div>
                            @endif
                            <div class="message-content">
                                <div class="message-header">
                                    @if($message->sender_id !== Auth::id())
                                        <span class="sender-name">{{ $message->sender->name }}</span>
                                    @endif
                                    <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                                </div>

                                @if($message->message_type === 'file')
                                    <div class="message-file">
                                        <i class="fas fa-file"></i>
                                        <a href="{{ asset($message->file_url) }}" download target="_blank">
                                            {{ $message->file_name }}
                                        </a>
                                        <span class="file-size">({{ $message->formatted_file_size }})</span>
                                    </div>
                                @elseif($message->message_type === 'image')
                                    <div class="message-image">
                                        <img src="{{ asset($message->file_url) }}" alt="Image" class="img-fluid">
                                    </div>
                                @else
                                    <p class="message-text">{{ nl2br(e($message->message)) }}</p>
                                @endif

                                @if($message->edited)
                                    <span class="edited-label">(edited)</span>
                                @endif

                                @if($message->sender_id === Auth::id())
                                    <div class="message-status">
                                        @if($message->is_read)
                                            <i class="fas fa-check-double text-primary" title="Read"></i>
                                        @else
                                            <i class="fas fa-check" title="Sent"></i>
                                        @endif
                                    </div>
                                @endif

                                <!-- Message Actions -->
                                <div class="message-actions">
                                    <button class="btn-action" onclick="replyToMessage({{ $message->id }})" title="Reply">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    @if($message->sender_id === Auth::id())
                                        <button class="btn-action" onclick="deleteMessage({{ $message->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator" style="display: none;">
                    <div class="typing-bubble">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <span class="typing-text">{{ $otherParticipant->name }} is typing...</span>
                </div>
            </div>

            <!-- Chat Input -->
            <div class="chat-input-container">
                <div id="replyPreview" class="reply-preview" style="display: none;">
                    <span>Replying to: <strong id="replyToText"></strong></span>
                    <button type="button" class="btn-close" onclick="cancelReply()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="messageForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                    <div class="chat-input-wrapper">
                        <button type="button" class="btn btn-attachment" id="attachFileBtn" title="Attach file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" name="file" id="fileInput" style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">

                        <textarea name="message" class="form-control chat-input"
                                  placeholder="Type a message..." rows="1" id="messageInput"
                                  autocomplete="off"></textarea>

                        <button type="button" class="btn btn-emoji" title="Add emoji">
                            <i class="far fa-smile"></i>
                        </button>
                        <button type="submit" class="btn btn-send" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Block User Modal -->
<div class="modal fade" id="blockUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="blockUserForm">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to block <strong>{{ $otherParticipant->name }}</strong>?</p>
                    <p class="text-muted small">They won't be able to send you messages anymore.</p>
                    <div class="form-group">
                        <label for="block_reason">Reason (Optional)</label>
                        <textarea class="form-control" name="reason" id="block_reason" rows="3" placeholder="Why are you blocking this user?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Conversation Modal -->
<div class="modal fade" id="deleteConversationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Conversation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this conversation?</p>
                <p class="text-muted small">This will only delete the conversation from your view.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteConversation()">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
<script>
window.Laravel = {
    userId: {{ Auth::guard('nurse_middle')->id() }},
    userName: '{{ Auth::guard('nurse_middle')->user()->name }}',
    userRole: {{ Auth::guard('nurse_middle')->user()->role }},
    csrfToken: '{{ csrf_token() }}',
    conversationId: {{ $conversation->id }}
};

// Initialize chat manager
document.addEventListener('DOMContentLoaded', function() {
    window.chatManager = new ChatManager({{ $conversation->id }});

    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
});

// Block user form handler
$('#blockUserForm').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: '{{ route("nurse.chat.block") }}',
        type: 'POST',
        data: {
            conversation_id: {{ $conversation->id }},
            reason: $('#block_reason').val(),
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                window.location.href = '{{ route("nurse.chat.index") }}';
            }
        },
        error: function(xhr) {
            alert('Failed to block user');
        }
    });
});

// Delete conversation
function deleteConversation() {
    $.ajax({
        url: '{{ route("nurse.chat.delete_conversation") }}',
        type: 'POST',
        data: {
            conversation_id: {{ $conversation->id }},
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                window.location.href = '{{ route("nurse.chat.index") }}';
            }
        },
        error: function(xhr) {
            alert('Failed to delete conversation');
        }
    });
}

// Delete message
function deleteMessage(messageId) {
    if (!confirm('Are you sure you want to delete this message?')) return;

    $.ajax({
        url: '{{ route("nurse.chat.delete") }}',
        type: 'POST',
        data: {
            message_id: messageId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $(`[data-message-id="${messageId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            }
        }
    });
}

// Reply to message
let replyToMessageId = null;
function replyToMessage(messageId) {
    const message = $(`[data-message-id="${messageId}"] .message-text`).text();
    replyToMessageId = messageId;
    $('#replyToText').text(message.substring(0, 50) + '...');
    $('#replyPreview').fadeIn();
    $('#messageInput').focus();
}

function cancelReply() {
    replyToMessageId = null;
    $('#replyPreview').fadeOut();
}

// View profile
function viewProfile(userId) {
    window.open('/healthcare/profile/' + userId, '_blank');
}
</script>
@endpush
@endsection

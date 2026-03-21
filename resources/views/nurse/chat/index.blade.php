@extends('nurse.layouts.layout')

@section('title', 'Messages')

@section('content')
<div class="chat-wrapper">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Conversation List -->
            <div class="col-md-4 col-lg-3 chat-sidebar">
                <div class="conversation-header">
                    <h4><i class="fas fa-comments"></i> Messages</h4>
                    <span class="badge badge-primary unread-badge" id="totalUnreadCount">0</span>
                </div>

                <div class="conversation-search">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search conversations..." id="searchConversations">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="conversation-list" id="conversationList">
                    @forelse($conversations as $conv)
                        @php
                            $otherParticipant = $conv->getOtherParticipant(Auth::id());
                            $unreadCount = $conv->unreadCount(Auth::id());
                        @endphp
                        <div class="conversation-item {{ request()->route('id') == $conv->id ? 'active' : '' }}"
                             data-conversation-id="{{ $conv->id }}"
                             onclick="window.location.href='{{ route('nurse.chat.show', $conv->id) }}'">
                            <div class="conversation-avatar">
                                <img src="{{ asset($otherParticipant->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" alt="{{ $otherParticipant->name }}">
                                <span class="online-status {{ cache()->get('user_'.$otherParticipant->id.'_online', false) ? 'online' : 'offline' }}"></span>
                            </div>
                            <div class="conversation-info">
                                <h5>{{ $otherParticipant->name }} {{ $otherParticipant->lastname ?? '' }}</h5>
                                <p class="last-message">
                                    @if($conv->latestMessage)
                                        @if($conv->latestMessage->message_type === 'file')
                                            <i class="fas fa-paperclip"></i> Attachment
                                        @else
                                            {{ Str::limit($conv->latestMessage->message, 40) }}
                                        @endif
                                    @else
                                        <em>Start a conversation</em>
                                    @endif
                                </p>
                            </div>
                            <div class="conversation-meta">
                                <span class="time">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}</span>
                                @if($unreadCount > 0)
                                    <span class="badge badge-primary unread-badge">{{ $unreadCount }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        @if($healthcareFacilities->isEmpty())
                            <div class="no-conversations">
                                <i class="fas fa-inbox"></i>
                                <p>No conversations yet</p>
                                <p class="small text-muted">Apply to jobs to start chatting with healthcare facilities</p>
                                <a href="{{ route('nurse.find_jobs') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> Browse Jobs
                                </a>
                            </div>
                        @else
                            <div class="no-conversations">
                                <i class="fas fa-comments"></i>
                                <p>Start chatting with healthcare facilities you've applied to!</p>
                                <p class="small text-muted">Click "Start New Chat" to select a healthcare facility</p>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newConversationModal">
                                    <i class="fas fa-plus"></i> Start New Chat
                                </button>
                            </div>
                        @endif
                    @endforelse
                </div>

                @if($conversations->hasPages())
                    <div class="pagination-wrapper">
                        {{ $conversations->links() }}
                    </div>
                @endif
            </div>

            <!-- Main Chat Area - Placeholder -->
            <div class="col-md-8 col-lg-9 chat-main">
                <div class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>Select a conversation to start chatting</h3>
                    <p>Choose from your existing conversations or start a new chat with a healthcare facility.</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#newConversationModal">
                        <i class="fas fa-plus"></i> Start New Chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start New Conversation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="newConversationForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient_id">Select Healthcare Facility</label>
                        <select class="form-control" name="recipient_id" id="recipient_id" required>
                            <option value="">Choose a facility...</option>
                            @forelse($healthcareFacilities as $facility)
                                <option value="{{ $facility->id }}"
                                        data-job-title="{{ $facility->job_title ?? '' }}"
                                        data-application-id="{{ $facility->application_id ?? '' }}">
                                    {{ $facility->name }} {{ $facility->lastname ?? '' }}
                                    @if($facility->job_title) - {{ $facility->job_title }} @endif
                                </option>
                            @empty
                                <option value="" disabled>No healthcare facilities available</option>
                            @endforelse
                        </select>
                        @if($healthcareFacilities->isEmpty())
                            <small class="text-muted">
                                You haven't applied to any jobs yet.
                                <a href="{{ route('nurse.find_jobs') }}">Browse jobs</a> and apply to start chatting with healthcare facilities.
                            </small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject (Optional)</label>
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="What's this about?">
                    </div>
                    <div class="form-group">
                        <label for="initial_message">Your Message</label>
                        <textarea class="form-control" name="message" id="initial_message" rows="4" required placeholder="Type your message..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Start Conversation
                    </button>
                </div>
            </form>
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
    userEmail: '{{ Auth::guard('nurse_middle')->user()->email }}',
    userRole: {{ Auth::guard('nurse_middle')->user()->role }},
    csrfToken: '{{ csrf_token() }}'
};

// Load healthcare facilities when modal opens
$('#newConversationModal').on('show.bs.modal', function() {
    const $select = $('#recipient_id');

    // If already loaded, don't load again
    if ($select.find('option').length > 1) return;

    $select.html('<option value="">Loading...</option>');

    $.ajax({
        url: '{{ route("nurse.chat.get_healthcare") }}',
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let options = '<option value="">Choose a facility...</option>';
                response.data.forEach(function(f) {
                    const jobInfo = f.job_title ? ' - ' + f.job_title : '';
                    options += `<option value="${f.id}" data-job-title="${f.job_title || ''}" data-application-id="${f.application_id || ''}">${f.name} ${f.lastname || ''}${jobInfo}</option>`;
                });
                $select.html(options);
            } else {
                $select.html('<option value="" disabled>No healthcare facilities available</option>');
                $select.after('<small class="text-muted">You haven\'t applied to any jobs yet. <a href="/jobList">Browse jobs</a> and apply to start chatting.</small>');
            }
        },
        error: function(xhr) {
            console.error('Error loading healthcare facilities:', xhr);
            $select.html('<option value="" disabled>Error loading facilities</option>');
        }
    });
});

// New conversation form handler
$('#newConversationForm').on('submit', function(e) {
    e.preventDefault();

    const $selectedOption = $('#recipient_id option:selected');
    const formData = {
        recipient_id: $('#recipient_id').val(),
        subject: $('#subject').val() || 'Job Application Inquiry',
        message: $('#initial_message').val(),
        job_title: $selectedOption.data('job-title') || '',
        application_id: $selectedOption.data('application-id') || '',
        _token: '{{ csrf_token() }}'
    };

    // Show loading state
    const $submitBtn = $(this).find('button[type="submit"]');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Starting...');

    $.ajax({
        url: '{{ route("nurse.chat.start") }}',
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success || response.conversation_id) {
                window.location.href = '/nurse/chat/conversation/' + (response.conversation_id || response.data.conversation_id);
            } else if (response.exists) {
                window.location.href = '/nurse/chat/conversation/' + response.conversation_id;
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.error || 'Failed to start conversation';
            alert(errorMsg);
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
});

// Search conversations
$('#searchConversations').on('input', function() {
    const query = $(this).val();

    if (query.length < 2) {
        $('#conversationList').load(window.location.href + ' #conversationList > *');
        return;
    }

    $.ajax({
        url: '{{ route("nurse.chat.search") }}',
        type: 'GET',
        data: { q: query },
        success: function(response) {
            // Update conversation list with search results
            let html = '';
            response.conversations.forEach(conv => {
                // Build conversation item HTML
                html += buildConversationItem(conv);
            });
            $('#conversationList').html(html);
        }
    });
});

function buildConversationItem(conv) {
    const otherParticipant = conv.nurse_id === {{ Auth::id() }} ? conv.healthcare : conv.nurse;
    return `
        <div class="conversation-item" data-conversation-id="${conv.id}"
             onclick="window.location.href='/nurse/chat/conversation/${conv.id}'">
            <div class="conversation-avatar">
                <img src="${otherParticipant.profile_img}" alt="${otherParticipant.name}">
            </div>
            <div class="conversation-info">
                <h5>${otherParticipant.name} ${otherParticipant.lastname || ''}</h5>
                <p class="last-message">${conv.latest_message?.message || 'No messages'}</p>
            </div>
            <div class="conversation-meta">
                <span class="time">${new Date(conv.last_message_at).toLocaleDateString()}</span>
            </div>
        </div>
    `;
}

// Update unread count periodically
function updateUnreadCount() {
    $.ajax({
        url: '{{ route("nurse.chat.unread_count") }}',
        type: 'GET',
        success: function(response) {
            $('#totalUnreadCount').text(response.unread_count);
            if (response.unread_count > 0) {
                document.title = `(${response.unread_count}) Messages`;
            } else {
                document.title = 'Messages';
            }
        }
    });
}

// Update unread count every 30 seconds
setInterval(updateUnreadCount, 30000);
updateUnreadCount();
</script>
@endpush
@endsection

<!-- Chat System Entry Points
     Add these links to your existing navigation -->

<!-- =====================================================
     1. NURSE HEADER - Add Chat Icon/Link
     File: resources/views/nurse/layouts/header.blade.php
     Add this inside the navigation menu
     ===================================================== -->

<!-- In your nurse header navigation menu, add: -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('nurse.chat.index') }}" title="Messages">
        <i class="fas fa-comment-dots"></i>
        <span class="badge badge-danger" id="nurse-chat-unread" style="display: none;">0</span>
        Messages
    </a>
</li>

<!-- =====================================================
     2. HEALTHCARE SIDEBAR - Add Chat Menu Item
     File: resources/views/healthcare/layouts/sidebar.blade.php
     Add this to the sidebar menu
     ===================================================== -->

<!-- In your healthcare sidebar navigation, add: -->
<li>
    <a class="btn btn-border aboutus-icon mb-20 profile_tabs" 
       href="{{ route('healthcare.chat.index') }}">
        <i class="fi fi-rr-comment-alt-dots"></i> Messages
        <span class="badge badge-danger" id="healthcare-chat-unread" style="display: none;">0</span>
    </a>
</li>

<!-- =====================================================
     3. JOB DETAILS PAGE - Add "Contact Recruiter" Button
     File: resources/views/nurse/jobs/show.blade.php (or similar)
     Add this near the job application button
     ===================================================== -->

<!-- On job details page, add: -->
@auth('nurse_middle')
    <a href="{{ route('nurse.chat.from_job', $job->id) }}" class="btn btn-primary btn-block">
        <i class="fas fa-envelope"></i> Contact Recruiter
    </a>
@endauth

<!-- =====================================================
     4. NURSE PROFILE PAGE (Healthcare View) - Add Chat Button
     File: resources/views/healthcare/nurses/profile.blade.php
     Add this near the profile actions
     ===================================================== -->

<!-- On nurse profile page (viewed by healthcare), add: -->
@auth('healthcare_facilities')
    <a href="{{ route('healthcare.chat.from_profile', $nurse->id) }}" class="btn btn-primary btn-block">
        <i class="fas fa-comment"></i> Send Message
    </a>
@endauth

<!-- =====================================================
     5. APPLICATIONS LIST - Add Chat Icon
     File: resources/views/healthcare/applications/index.blade.php
     Add chat button next to each application
     ===================================================== -->

<!-- In applications table, add column: -->
<th>Actions</th>

<!-- In each row: -->
<td>
    <a href="{{ route('healthcare.chat.start', $application->nurse_id) }}" 
       class="btn btn-sm btn-outline-primary" 
       title="Send Message">
        <i class="fas fa-comment"></i>
    </a>
</td>

<!-- =====================================================
     6. FLOATING CHAT BUTTON (Optional)
     Add a floating chat button that appears on all pages
     ===================================================== -->

<!-- Add this before </body> in your main layout -->
@auth('nurse_middle')
<div class="floating-chat-btn" onclick="window.location.href='{{ route('nurse.chat.index') }}'" 
     title="Open Messages" style="
         position: fixed;
         bottom: 20px;
         right: 20px;
         width: 60px;
         height: 60px;
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-size: 24px;
         cursor: pointer;
         box-shadow: 0 4px 12px rgba(0,0,0,0.3);
         z-index: 9999;
         transition: transform 0.2s ease;">
    <i class="fas fa-comment-dots"></i>
    <span class="floating-chat-badge" 
          style="
              position: absolute;
              top: -5px;
              right: -5px;
              background: #ff4757;
              color: white;
              font-size: 12px;
              padding: 3px 7px;
              border-radius: 10px;
              display: none;">0</span>
</div>

<script>
// Update unread count for floating button
function updateFloatingChatBadge() {
    fetch('{{ route("nurse.chat.unread_count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.floating-chat-badge');
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        });
}
setInterval(updateFloatingChatBadge, 30000);
updateFloatingChatBadge();
</script>
@endauth

<!-- =====================================================
     7. DASHBOARD WIDGET - Recent Messages
     Add to nurse dashboard to show recent conversations
     ===================================================== -->

<!-- In nurse dashboard, add widget: -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-comments"></i> Recent Messages</h5>
        <a href="{{ route('nurse.chat.index') }}" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body">
        @php
            $recentChats = \App\Models\Conversation::with(['healthcare', 'latestMessage'])
                ->where('nurse_id', Auth::id())
                ->orderBy('last_message_at', 'desc')
                ->limit(5)
                ->get();
        @endphp
        
        @forelse($recentChats as $chat)
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <img src="{{ asset($chat->healthcare->profile_img ?? 'nurse/assets/imgs/nurse06.png') }}" 
                     class="rounded-circle mr-3" width="40" height="40">
                <div class="flex-grow-1">
                    <h6 class="mb-0">{{ $chat->healthcare->name }}</h6>
                    <p class="text-muted mb-0 small">
                        {{ Str::limit($chat->latestMessage->message ?? 'No messages', 40) }}
                    </p>
                </div>
                <small class="text-muted">
                    {{ $chat->last_message_at?->diffForHumans() ?? '' }}
                </small>
            </div>
        @empty
            <p class="text-muted text-center">No messages yet</p>
            <a href="{{ route('nurse.chat.index') }}" class="btn btn-primary btn-block btn-sm">
                Start Chatting
            </a>
        @endforelse
    </div>
</div>

<!-- =====================================================
     8. QUICK ACTION DROPDOWN - Add Chat Option
     Add to any user dropdown menu
     ===================================================== -->

<!-- In user menu dropdown, add: -->
<div class="dropdown-menu">
    <!-- ... other menu items ... -->
    @if(Auth::guard('nurse_middle')->user()->role === 1)
        <a class="dropdown-item" href="{{ route('nurse.chat.index') }}">
            <i class="fas fa-comment-dots mr-2"></i> Messages
        </a>
    @else
        <a class="dropdown-item" href="{{ route('healthcare.chat.index') }}">
            <i class="fas fa-comment-dots mr-2"></i> Messages
        </a>
    @endif
    <!-- ... other menu items ... -->
</div>

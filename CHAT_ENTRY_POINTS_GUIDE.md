# 📍 Chat System - Entry Points Guide

## Where Does the Chat Open?

The chat system can be accessed from **multiple entry points** in your MediQa application:

---

## 🚀 Direct URLs (Ready to Use)

### For Nurses:
```
Main Chat Dashboard:     http://localhost/mediqa_nurse/nurse/chat
Specific Conversation:   http://localhost/mediqa_nurse/nurse/chat/conversation/{id}
Browse Healthcare:       http://localhost/mediqa_nurse/nurse/chat/healthcare-list
Start from Job:          http://localhost/mediqa_nurse/nurse/chat/start/{jobId}
```

### For Healthcare Facilities:
```
Main Chat Dashboard:     http://localhost/mediqa_nurse/healthcare/chat
Specific Conversation:   http://localhost/mediqa_nurse/healthcare/chat/conversation/{id}
Browse Nurses:           http://localhost/mediqa_nurse/healthcare/chat/nurses
Start from Profile:      http://localhost/mediqa_nurse/healthcare/chat/start/{nurseId}
```

---

## 📌 Quick Integration - Add These Links

### Option 1: Add to Nurse Header Navigation

**File:** `resources/views/nurse/layouts/header.blade.php`

Find your navigation menu (`<ul>` with class like `nav-main-menu`) and add:

```html
<li class="nav-item">
    <a class="nav-link" href="{{ route('nurse.chat.index') }}">
        <i class="fas fa-comment-dots"></i>
        <span>Messages</span>
        {{-- Show unread count --}}
        @php
            $unread = \App\Models\Message::whereHas('conversation', fn($q) => 
                $q->where('nurse_id', Auth::id())
            )->where('sender_id', '!=', Auth::id())->where('is_read', 0)->count();
        @endphp
        @if($unread > 0)
            <span class="badge badge-danger">{{ $unread }}</span>
        @endif
    </a>
</li>
```

---

### Option 2: Add to Healthcare Sidebar

**File:** `resources/views/healthcare/layouts/sidebar.blade.php`

Add to the `<ul>` in the sidebar:

```html
<li>
    <a class="btn btn-border aboutus-icon mb-20 profile_tabs" 
       href="{{ route('healthcare.chat.index') }}">
        <i class="fi fi-rr-comment-alt-dots"></i> 
        Messages
        @php
            $unread = \App\Models\Message::whereHas('conversation', fn($q) => 
                $q->where('healthcare_id', Auth::id())
            )->where('sender_id', '!=', Auth::id())->where('is_read', 0)->count();
        @endphp
        @if($unread > 0)
            <span class="badge badge-danger">{{ $unread }}</span>
        @endif
    </a>
</li>
```

---

### Option 3: Floating Chat Button (All Pages)

**Add to:** Main layout file (before `</body>`)

Include the pre-made component:
```php
@include('chat.entry_points.floating_chat_button')
```

Or copy the code from: `resources/views/chat/entry_points/floating_chat_button.blade.php`

This creates a **WhatsApp-style floating button** in the bottom-right corner.

---

## 🔗 Context-Specific Entry Points

### 1. From Job Details Page (Nurse View)

When a nurse is viewing a job posting, add this button to contact the recruiter:

```html
<a href="{{ route('nurse.chat.from_job', $job->id) }}" class="btn btn-primary">
    <i class="fas fa-envelope"></i> Contact Recruiter
</a>
```

**What happens:** Opens chat with the healthcare facility who posted the job.

---

### 2. From Nurse Profile (Healthcare View)

When healthcare is viewing a nurse's profile:

```html
<a href="{{ route('healthcare.chat.from_profile', $nurse->id) }}" class="btn btn-primary">
    <i class="fas fa-comment"></i> Send Message
</a>
```

**What happens:** Opens chat with the nurse.

---

### 3. From Applications List

In the applications table where healthcare sees nurse applications:

```html
<!-- Add this column header -->
<th>Actions</th>

<!-- Add this to each row -->
<td>
    <a href="{{ route('healthcare.chat.start', $application->nurse_id) }}" 
       class="btn btn-sm btn-outline-primary" 
       title="Send Message">
        <i class="fas fa-comment"></i>
    </a>
</td>
```

---

### 4. From User Dropdown Menu

In your header's user profile dropdown:

```html
<div class="dropdown-menu">
    {{-- ... other items ... --}}
    
    @if(Auth::guard('nurse_middle')->user()->role === 1)
        <a class="dropdown-item" href="{{ route('nurse.chat.index') }}">
            <i class="fas fa-comment-dots mr-2"></i> Messages
        </a>
    @else
        <a class="dropdown-item" href="{{ route('healthcare.chat.index') }}">
            <i class="fas fa-comment-dots mr-2"></i> Messages
        </a>
    @endif
    
    {{-- ... other items ... --}}
</div>
```

---

## 📊 Dashboard Widget

Add a "Recent Messages" widget to the nurse dashboard:

**File:** `resources/views/nurse/dashboard.blade.php` (or similar)

```html
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
                <img src="{{ asset($chat->healthcare->profile_img) }}" 
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
```

---

## 🎯 Recommended Integration Strategy

### Minimum Setup (Required):
1. ✅ Add chat link to **nurse header navigation**
2. ✅ Add chat link to **healthcare sidebar**

### Enhanced Setup (Recommended):
3. ✅ Add **floating chat button** to all pages
4. ✅ Add **context-specific buttons** on job/nurse profile pages
5. ✅ Add **dashboard widget** for recent messages

### Complete Setup (Best UX):
6. ✅ Add chat option to **user dropdown menu**
7. ✅ Add chat buttons in **applications list**
8. ✅ Show unread count badges on all chat links

---

## 🧪 Testing Chat Access

After adding entry points, test:

1. **Login as Nurse:**
   - Navigate to `/nurse/chat`
   - Verify chat dashboard loads
   - Click on a conversation
   - Send a message

2. **Login as Healthcare:**
   - Navigate to `/healthcare/chat`
   - Click "Browse Nurses"
   - Start a conversation with a nurse
   - Send a message

3. **Test Real-time:**
   - Open chat in two different browsers (nurse + healthcare)
   - Send messages from both sides
   - Verify real-time delivery

---

## 📁 Ready-to-Use Components

Pre-made Blade components are located in:
```
resources/views/chat/entry_points/
├── nurse_header_chat_link.blade.php
├── healthcare_sidebar_chat_link.blade.php
└── floating_chat_button.blade.php
```

Simply include them in your layouts:
```php
@include('chat.entry_points.nurse_header_chat_link')
```

---

## 🔧 Troubleshooting

### Chat link not showing?
- Check if user is logged in with correct guard (`nurse_middle`)
- Verify route names match in your blade files

### 404 Error when clicking chat?
- Run `php artisan route:cache` to refresh routes
- Check if routes are loaded in `routes/web.php`

### Unread count not updating?
- Ensure JavaScript is loaded
- Check browser console for errors
- Verify Pusher is configured for real-time updates

---

## 📞 Quick Reference

| Entry Point | Route Name | URL |
|-------------|-----------|-----|
| Nurse Chat Dashboard | `nurse.chat.index` | `/nurse/chat` |
| Healthcare Chat Dashboard | `healthcare.chat.index` | `/healthcare/chat` |
| View Conversation (Nurse) | `nurse.chat.show` | `/nurse/chat/conversation/{id}` |
| View Conversation (Healthcare) | `healthcare.chat.show` | `/healthcare/chat/conversation/{id}` |
| Browse Nurses | `healthcare.chat.nurses` | `/healthcare/chat/nurses` |
| Start from Job | `nurse.chat.from_job` | `/nurse/chat/start/{jobId}` |
| Start from Profile | `healthcare.chat.from_profile` | `/healthcare/chat/start/{nurseId}` |

---

**Last Updated:** March 21, 2026  
**Version:** 1.0

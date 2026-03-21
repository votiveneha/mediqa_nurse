# Nurse-Healthcare Chat System Documentation

## Table of Contents
1. [Overview](#1-overview)
2. [Database Schema](#2-database-schema)
3. [Models](#3-models)
4. [Controllers](#4-controllers)
5. [Routes](#5-routes)
6. [Frontend Views](#6-frontend-views)
7. [Real-time Implementation](#7-real-time-implementation)
8. [API Endpoints](#8-api-endpoints)
9. [Security Considerations](#9-security-considerations)
10. [Testing Guidelines](#10-testing-guidelines)

---

## 1. Overview

### 1.1 Purpose
This document outlines the complete implementation of a real-time chat system between **Nurses** and **Healthcare Facilities** (Medical Facilities/Hospitals) within the MediQa Laravel application.

### 1.2 Key Features
- **Real-time messaging** between nurses and healthcare facilities
- **Conversation threads** per job application or inquiry
- **Message status tracking** (sent, delivered, read)
- **File attachments** support (resumes, certificates, etc.)
- **Typing indicators** and **online status**
- **Message notifications** via email and in-app
- **Conversation history** with search functionality
- **Block/Report** inappropriate users

### 1.3 User Roles
| Role | Description |
|------|-------------|
| **Nurse** | Job seekers who can message healthcare facilities |
| **Healthcare Facility** | Employers who can message nurses |
| **Admin** | Can monitor conversations and moderate |

---

## 2. Database Schema

### 2.1 New Tables Required

#### 2.1.1 `conversations` Table
```sql
CREATE TABLE `conversations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` VARCHAR(255) DEFAULT NULL,
  `job_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Related job posting if any',
  `nurse_id` BIGINT UNSIGNED NOT NULL,
  `healthcare_id` BIGINT UNSIGNED NOT NULL,
  `last_message_id` BIGINT UNSIGNED DEFAULT NULL,
  `last_message_at` TIMESTAMP NULL DEFAULT NULL,
  `nurse_deleted` TINYINT(1) DEFAULT 0,
  `healthcare_deleted` TINYINT(1) DEFAULT 0,
  `nurse_blocked` TINYINT(1) DEFAULT 0,
  `healthcare_blocked` TINYINT(1) DEFAULT 0,
  `status` ENUM('active', 'archived', 'closed') DEFAULT 'active',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nurse_id` (`nurse_id`),
  KEY `healthcare_id` (`healthcare_id`),
  KEY `job_id` (`job_id`),
  CONSTRAINT `conversations_nurse_fk` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_healthcare_fk` FOREIGN KEY (`healthcare_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.2 `messages` Table
```sql
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` BIGINT UNSIGNED NOT NULL,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `sender_type` ENUM('nurse', 'healthcare') NOT NULL,
  `message` TEXT NOT NULL,
  `message_type` ENUM('text', 'file', 'image', 'system') DEFAULT 'text',
  `file_url` VARCHAR(500) DEFAULT NULL,
  `file_name` VARCHAR(255) DEFAULT NULL,
  `file_size` INT DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_by_sender` TINYINT(1) DEFAULT 0,
  `deleted_by_receiver` TINYINT(1) DEFAULT 0,
  `edited` TINYINT(1) DEFAULT 0,
  `edited_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`),
  KEY `is_read` (`is_read`),
  CONSTRAINT `messages_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.3 `message_attachments` Table
```sql
CREATE TABLE `message_attachments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_id` BIGINT UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(100) NOT NULL,
  `file_size` INT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `attachments_message_fk` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.4 `conversation_participants` Table
```sql
CREATE TABLE `conversation_participants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `last_read_message_id` BIGINT UNSIGNED DEFAULT NULL,
  `unread_count` INT DEFAULT 0,
  `is_typing` TINYINT(1) DEFAULT 0,
  `last_seen_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `participants_conversation_fk` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2.1.5 `blocked_users` Table
```sql
CREATE TABLE `blocked_users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blocker_id` BIGINT UNSIGNED NOT NULL,
  `blocked_id` BIGINT UNSIGNED NOT NULL,
  `reason` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_block` (`blocker_id`, `blocked_id`),
  CONSTRAINT `blocked_blocker_fk` FOREIGN KEY (`blocker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blocked_blocked_fk` FOREIGN KEY (`blocked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Models

### 3.1 Conversation Model
**File:** `app/Models/Conversation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'job_id',
        'nurse_id',
        'healthcare_id',
        'last_message_id',
        'last_message_at',
        'nurse_deleted',
        'healthcare_deleted',
        'nurse_blocked',
        'healthcare_blocked',
        'status',
    ];

    protected $casts = [
        'nurse_deleted' => 'boolean',
        'healthcare_deleted' => 'boolean',
        'nurse_blocked' => 'boolean',
        'healthcare_blocked' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Get all messages in the conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the nurse user
     */
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    /**
     * Get the healthcare user
     */
    public function healthcare()
    {
        return $this->belongsTo(User::class, 'healthcare_id');
    }

    /**
     * Get the related job
     */
    public function job()
    {
        return $this->belongsTo(JobsModel::class, 'job_id');
    }

    /**
     * Get participants
     */
    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Get latest message
     */
    public function latestMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Check if conversation exists between two users
     */
    public static function existsBetween($nurseId, $healthcareId)
    {
        return self::where(function($query) use ($nurseId, $healthcareId) {
            $query->where('nurse_id', $nurseId)
                  ->where('healthcare_id', $healthcareId);
        })->orWhere(function($query) use ($nurseId, $healthcareId) {
            $query->where('nurse_id', $healthcareId)
                  ->where('healthcare_id', $nurseId);
        })->first();
    }

    /**
     * Get unread count for a user
     */
    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', 0)
            ->count();
    }
}
```

### 3.2 Message Model
**File:** `app/Models/Message.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'file_url',
        'file_name',
        'file_size',
        'is_read',
        'read_at',
        'deleted_by_sender',
        'deleted_by_receiver',
        'edited',
        'edited_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'deleted_by_sender' => 'boolean',
        'deleted_by_receiver' => 'boolean',
        'edited' => 'boolean',
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    /**
     * Get the conversation
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the sender
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get attachments
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => 1,
            'read_at' => now()
        ]);
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', 0);
    }

    /**
     * Scope for text messages only
     */
    public function scopeText($query)
    {
        return $query->where('message_type', 'text');
    }
}
```

### 3.3 ConversationParticipant Model
**File:** `app/Models/ConversationParticipant.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_message_id',
        'unread_count',
        'is_typing',
        'last_seen_at',
    ];

    protected $casts = [
        'is_typing' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateLastRead($messageId)
    {
        $this->update([
            'last_read_message_id' => $messageId,
            'unread_count' => 0
        ]);
    }
}
```

### 3.4 MessageAttachment Model
**File:** `app/Models/MessageAttachment.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function getFullPathAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
```

### 3.5 BlockedUser Model
**File:** `app/Models/BlockedUser.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'reason',
    ];

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    public static function isBlocked($blockerId, $blockedId)
    {
        return self::where('blocker_id', $blockerId)
            ->where('blocked_id', $blockedId)
            ->exists();
    }
}
```

---

## 4. Controllers

### 4.1 ChatController (Base Controller)
**File:** `app/Http/Controllers/ChatController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\ConversationParticipant;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display chat interface
     */
    public function index()
    {
        $user = Auth::guard('nurse_middle')->user();
        $conversations = $this->getUserConversations($user->id);
        
        return view('chat.index', compact('conversations'));
    }

    /**
     * Get all conversations for a user
     */
    private function getUserConversations($userId)
    {
        $user = User::find($userId);
        
        if ($user->role === 1) { // Nurse
            return Conversation::with(['healthcare', 'latestMessage'])
                ->where('nurse_id', $userId)
                ->where('nurse_deleted', 0)
                ->orderBy('last_message_at', 'desc')
                ->get();
        } else { // Healthcare
            return Conversation::with(['nurse', 'latestMessage'])
                ->where('healthcare_id', $userId)
                ->where('healthcare_deleted', 0)
                ->orderBy('last_message_at', 'desc')
                ->get();
        }
    }

    /**
     * Get specific conversation
     */
    public function getConversation($conversationId)
    {
        $user = Auth::guard('nurse_middle')->user();
        
        $conversation = Conversation::with(['nurse', 'healthcare', 'messages.sender'])
            ->where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('nurse_id', $user->id)
                      ->orWhere('healthcare_id', $user->id);
            })
            ->firstOrFail();

        // Mark messages as read
        $this->markMessagesAsRead($conversation->id, $user->id);

        return view('chat.conversation', compact('conversation'));
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:5000',
        ]);

        $user = Auth::guard('nurse_middle')->user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Check if user is part of conversation
        if (!in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if blocked
        if (BlockedUser::isBlocked($conversation->nurse_id, $user->id) ||
            BlockedUser::isBlocked($conversation->healthcare_id, $user->id)) {
            return response()->json(['error' => 'You are blocked from this conversation'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => $user->role === 1 ? 'nurse' : 'healthcare',
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update conversation
        $conversation->update([
            'last_message_id' => $message->id,
            'last_message_at' => now(),
        ]);

        // Update participant
        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $user->id)
            ->increment('unread_count');

        // Broadcast event
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('sender')
        ]);
    }

    /**
     * Start new conversation
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'job_id' => 'nullable|exists:jobs,id',
        ]);

        $sender = Auth::guard('nurse_middle')->user();
        $recipient = User::find($request->recipient_id);

        // Validate roles
        if ($sender->role === $recipient->role) {
            return response()->json([
                'error' => 'Can only chat between nurses and healthcare facilities'
            ], 422);
        }

        // Determine who is nurse and who is healthcare
        $nurseId = $sender->role === 1 ? $sender->id : $recipient->id;
        $healthcareId = $sender->role === 2 ? $sender->id : $recipient->id;

        // Check if conversation already exists
        $existingConversation = Conversation::where('nurse_id', $nurseId)
            ->where('healthcare_id', $healthcareId)
            ->first();

        if ($existingConversation) {
            return response()->json([
                'conversation_id' => $existingConversation->id,
                'exists' => true
            ]);
        }

        // Create new conversation
        $conversation = Conversation::create([
            'subject' => $request->subject ?? 'New Conversation',
            'job_id' => $request->job_id,
            'nurse_id' => $nurseId,
            'healthcare_id' => $healthcareId,
            'status' => 'active',
        ]);

        // Create first message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'sender_type' => $sender->role === 1 ? 'nurse' : 'healthcare',
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        // Update conversation
        $conversation->update([
            'last_message_id' => $message->id,
            'last_message_at' => now(),
        ]);

        // Create participants
        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $recipient->id,
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }

    /**
     * Mark messages as read
     */
    private function markMessagesAsRead($conversationId, $userId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['unread_count' => 0]);
    }

    /**
     * Upload file attachment
     */
    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $user = Auth::guard('nurse_middle')->user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        $file = $request->file('file');
        $path = $file->store('chat_attachments', 'public');

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => $user->role === 1 ? 'nurse' : 'healthcare',
            'message' => 'File: ' . $file->getClientOriginalName(),
            'message_type' => 'file',
            'file_url' => Storage::url($path),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        // Create attachment record
        $message->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete message
     */
    public function deleteMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);

        $user = Auth::guard('nurse_middle')->user();
        $message = Message::findOrFail($request->message_id);

        // Check if user is sender or receiver
        $conversation = Conversation::find($message->conversation_id);
        if (!in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($message->sender_id === $user->id) {
            $message->deleted_by_sender = 1;
        } else {
            $message->deleted_by_receiver = 1;
        }

        $message->save();

        // Check if both deleted, then delete permanently
        if ($message->deleted_by_sender && $message->deleted_by_receiver) {
            $message->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Block user
     */
    public function blockUser(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::guard('nurse_middle')->user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        $blockedUserId = $user->id === $conversation->nurse_id 
            ? $conversation->healthcare_id 
            : $conversation->nurse_id;

        BlockedUser::create([
            'blocker_id' => $user->id,
            'blocked_id' => $blockedUserId,
            'reason' => $request->reason,
        ]);

        $conversation->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    /**
     * Search conversations
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $user = Auth::guard('nurse_middle')->user();

        $conversations = Conversation::where(function($q) use ($user) {
            $q->where('nurse_id', $user->id)
              ->orWhere('healthcare_id', $user->id);
        })
        ->whereHas('messages', function($q) use ($query) {
            $q->where('message', 'LIKE', "%{$query}%");
        })
        ->with(['nurse', 'healthcare', 'latestMessage'])
        ->get();

        return response()->json(['conversations' => $conversations]);
    }
}
```

### 4.2 Nurse Chat Controller
**File:** `app/Http/Controllers/nurse/ChatController.php`

```php
<?php

namespace App\Http\Controllers\nurse;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\JobsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display nurse chat dashboard
     */
    public function index()
    {
        $user = Auth::guard('nurse_middle')->user();
        
        $conversations = Conversation::with(['healthcare', 'latestMessage'])
            ->where('nurse_id', $user->id)
            ->where('nurse_deleted', 0)
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        $unreadCount = Message::whereHas('conversation', function($q) use ($user) {
                $q->where('nurse_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->count();

        return view('nurse.chat.index', compact('conversations', 'unreadCount'));
    }

    /**
     * Show conversation with healthcare
     */
    public function showConversation($id)
    {
        $user = Auth::guard('nurse_middle')->user();
        
        $conversation = Conversation::with(['healthcare', 'messages.sender', 'job'])
            ->where('id', $id)
            ->where('nurse_id', $user->id)
            ->firstOrFail();

        // Mark messages as read
        Message::where('conversation_id', $id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return view('nurse.chat.conversation', compact('conversation'));
    }

    /**
     * Start chat from job posting
     */
    public function chatFromJob($jobId)
    {
        $user = Auth::guard('nurse_middle')->user();
        $job = JobsModel::findOrFail($jobId);

        // Check if conversation exists
        $conversation = Conversation::where('nurse_id', $user->id)
            ->where('healthcare_id', $job->created_by)
            ->where('job_id', $jobId)
            ->first();

        if ($conversation) {
            return redirect()->route('nurse.chat.show', $conversation->id);
        }

        return view('nurse.chat.start_from_job', compact('job'));
    }
}
```

### 4.3 Healthcare Chat Controller
**File:** `app/Http/Controllers/medical_facilities/ChatController.php`

```php
<?php

namespace App\Http\Controllers\medical_facilities;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display healthcare chat dashboard
     */
    public function index()
    {
        $user = Auth::guard('nurse_middle')->user();
        
        $conversations = Conversation::with(['nurse', 'latestMessage'])
            ->where('healthcare_id', $user->id)
            ->where('healthcare_deleted', 0)
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        $unreadCount = Message::whereHas('conversation', function($q) use ($user) {
                $q->where('healthcare_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->count();

        return view('medical_facilities.chat.index', compact('conversations', 'unreadCount'));
    }

    /**
     * Show conversation with nurse
     */
    public function showConversation($id)
    {
        $user = Auth::guard('nurse_middle')->user();
        
        $conversation = Conversation::with(['nurse', 'messages.sender', 'job'])
            ->where('id', $id)
            ->where('healthcare_id', $user->id)
            ->firstOrFail();

        // Mark messages as read
        Message::where('conversation_id', $id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1, 'read_at' => now()]);

        return view('medical_facilities.chat.conversation', compact('conversation'));
    }

    /**
     * View all nurses available to chat
     */
    public function nursesList()
    {
        $nurses = User::where('role', 1)
            ->where('status', 1)
            ->with('profile')
            ->paginate(20);

        return view('medical_facilities.chat.nurses_list', compact('nurses'));
    }
}
```

---

## 5. Routes

### 5.1 Web Routes
**File:** `routes/web.php`

Add the following routes:

```php
// ==========================================
// Chat System Routes
// ==========================================

// Nurse Chat Routes
Route::prefix('nurse/chat')->name('nurse.chat.')->middleware('auth:nurse_middle')->group(function () {
    Route::get('/', 'App\Http\Controllers\nurse\ChatController@index')->name('index');
    Route::get('/conversation/{id}', 'App\Http\Controllers\nurse\ChatController@showConversation')->name('show');
    Route::get('/start/{jobId}', 'App\Http\Controllers\nurse\ChatController@chatFromJob')->name('from_job');
    Route::post('/send', 'App\Http\Controllers\ChatController@sendMessage')->name('send');
    Route::post('/start', 'App\Http\Controllers\ChatController@startConversation')->name('start');
    Route::post('/upload', 'App\Http\Controllers\ChatController@uploadAttachment')->name('upload');
    Route::post('/delete', 'App\Http\Controllers\ChatController@deleteMessage')->name('delete');
    Route::post('/block', 'App\Http\Controllers\ChatController@blockUser')->name('block');
    Route::get('/search', 'App\Http\Controllers\ChatController@search')->name('search');
});

// Healthcare Chat Routes
Route::prefix('healthcare/chat')->name('healthcare.chat.')->middleware('auth:nurse_middle')->group(function () {
    Route::get('/', 'App\Http\Controllers\medical_facilities\ChatController@index')->name('index');
    Route::get('/conversation/{id}', 'App\Http\Controllers\medical_facilities\ChatController@showConversation')->name('show');
    Route::get('/nurses', 'App\Http\Controllers\medical_facilities\ChatController@nursesList')->name('nurses');
    Route::post('/send', 'App\Http\Controllers\ChatController@sendMessage')->name('send');
    Route::post('/start', 'App\Http\Controllers\ChatController@startConversation')->name('start');
    Route::post('/upload', 'App\Http\Controllers\ChatController@uploadAttachment')->name('upload');
    Route::post('/delete', 'App\Http\Controllers\ChatController@deleteMessage')->name('delete');
    Route::post('/block', 'App\Http\Controllers\ChatController@blockUser')->name('block');
    Route::get('/search', 'App\Http\Controllers\ChatController@search')->name('search');
});
```

### 5.2 API Routes
**File:** `routes/api.php`

```php
// Chat API Routes
Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::get('/conversations', 'App\Http\Controllers\Api\ChatApiController@conversations');
    Route::get('/conversation/{id}', 'App\Http\Controllers\Api\ChatApiController@conversation');
    Route::post('/message', 'App\Http\Controllers\Api\ChatApiController@sendMessage');
    Route::post('/read', 'App\Http\Controllers\Api\ChatApiController@markAsRead');
    Route::delete('/message/{id}', 'App\Http\Controllers\Api\ChatApiController@deleteMessage');
    Route::post('/typing', 'App\Http\Controllers\Api\ChatApiController@typingStatus');
    Route::get('/unread-count', 'App\Http\Controllers\Api\ChatApiController@unreadCount');
});
```

### 5.3 Broadcast Routes
**File:** `routes/channels.php`

```php
<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Conversation channel
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = App\Models\Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }

    return in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id]);
});

// User online status channel
Broadcast::channel('user.{userId}.online', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Typing status channel
Broadcast::channel('conversation.{conversationId}.typing', function ($user, $conversationId) {
    $conversation = App\Models\Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }

    return in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id]);
});
```

---

## 6. Frontend Views

### 6.1 Chat Layout Structure

```
resources/views/
├── chat/
│   ├── index.blade.php              # Main chat dashboard
│   ├── conversation.blade.php       # Individual conversation view
│   └── components/
│       ├── conversation-list.blade.php
│       ├── message-bubble.blade.php
│       ├── chat-input.blade.php
│       └── typing-indicator.blade.php
├── nurse/chat/
│   ├── index.blade.php
│   └── conversation.blade.php
└── medical_facilities/chat/
    ├── index.blade.php
    ├── conversation.blade.php
    └── nurses_list.blade.php
```

### 6.2 Main Chat View Example
**File:** `resources/views/chat/conversation.blade.php`

```blade
@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="chat-container" data-conversation-id="{{ $conversation->id }}">
    <div class="row">
        <!-- Sidebar - Conversation List -->
        <div class="col-md-4 col-lg-3 chat-sidebar">
            <div class="conversation-header">
                <h4>Messages</h4>
                <button class="btn btn-primary" data-toggle="modal" data-target="#newConversationModal">
                    <i class="fas fa-plus"></i> New
                </button>
            </div>
            
            <div class="conversation-search">
                <input type="text" class="form-control" placeholder="Search conversations..." id="searchConversations">
            </div>

            <div class="conversation-list">
                @foreach($conversations as $conv)
                    <div class="conversation-item {{ $conv->id == $conversation->id ? 'active' : '' }}" 
                         data-conversation-id="{{ $conv->id }}">
                        <div class="conversation-avatar">
                            <img src="{{ asset($conv->nurse->profile_img ?? 'default.png') }}" alt="">
                        </div>
                        <div class="conversation-info">
                            <h5>{{ $conv->nurse->name ?? 'Healthcare Facility' }}</h5>
                            <p class="last-message">{{ Str::limit($conv->latestMessage->message ?? 'No messages', 40) }}</p>
                        </div>
                        <div class="conversation-meta">
                            <span class="time">{{ $conv->last_message_at->diffForHumans() }}</span>
                            @if($conv->unreadCount(Auth::id()) > 0)
                                <span class="badge badge-primary">{{ $conv->unreadCount(Auth::id()) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9 chat-main">
            <div class="chat-header">
                <div class="chat-user-info">
                    <img src="{{ asset($conversation->nurse->profile_img ?? 'default.png') }}" alt="">
                    <div>
                        <h5>{{ $conversation->nurse->name ?? $conversation->healthcare->name }}</h5>
                        <span class="online-status {{ $isOnline ? 'online' : 'offline' }}">
                            {{ $isOnline ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="btn btn-sm btn-outline-secondary" title="Block User" data-toggle="modal" data-target="#blockUserModal">
                        <i class="fas fa-ban"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" title="Delete Conversation" data-toggle="modal" data-target="#deleteConversationModal">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                @foreach($conversation->messages as $message)
                    @if(!$message->deleted_by_sender && !$message->deleted_by_receiver)
                        <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}" 
                             data-message-id="{{ $message->id }}">
                            <div class="message-avatar">
                                <img src="{{ asset($message->sender->profile_img ?? 'default.png') }}" alt="">
                            </div>
                            <div class="message-content">
                                <div class="message-header">
                                    <span class="sender-name">{{ $message->sender->name }}</span>
                                    <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                                </div>
                                
                                @if($message->message_type === 'file')
                                    <div class="message-file">
                                        <i class="fas fa-file"></i>
                                        <a href="{{ asset($message->file_url) }}" download>
                                            {{ $message->file_name }}
                                        </a>
                                    </div>
                                @else
                                    <p class="message-text">{{ $message->message }}</p>
                                @endif

                                @if($message->edited)
                                    <span class="edited-label">(edited)</span>
                                @endif

                                @if($message->sender_id === Auth::id())
                                    <div class="message-status">
                                        @if($message->is_read)
                                            <i class="fas fa-check-double text-primary"></i>
                                        @else
                                            <i class="fas fa-check"></i>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator" style="display: none;">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>

            <div class="chat-input-container">
                <form id="messageForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                    
                    <div class="chat-input-wrapper">
                        <button type="button" class="btn btn-attachment" id="attachFileBtn">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="file" name="file" id="fileInput" style="display: none;">
                        
                        <textarea name="message" class="form-control chat-input" 
                                  placeholder="Type a message..." rows="1" id="messageInput"></textarea>
                        
                        <button type="submit" class="btn btn-send" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chat JavaScript will be implemented in Section 7
</script>
@endpush
@endsection
```

---

## 7. Real-time Implementation

### 7.1 Broadcasting Configuration

**File:** `config/broadcasting.php`

```php
return [
    'default' => env('BROADCAST_DRIVER', 'null'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'ably' => [
            'driver' => 'ably',
            'key' => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
```

### 7.2 MessageSent Event
**File:** `app/Events/MessageSent.php`

```php
<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->profile_img,
            'message' => $this->message->message,
            'message_type' => $this->message->message_type,
            'file_url' => $this->message->file_url,
            'file_name' => $this->message->file_name,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
```

### 7.3 UserTyping Event
**File:** `app/Events/UserTyping.php`

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $userId;
    public $userName;
    public $isTyping;

    public function __construct($conversationId, $userId, $userName, $isTyping = true)
    {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('conversation.' . $this->conversationId);
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}
```

### 7.4 Frontend JavaScript (Echo)
**File:** `resources/js/chat.js`

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});

class ChatManager {
    constructor(conversationId) {
        this.conversationId = conversationId;
        this.typingTimeout = null;
        this.init();
    }

    init() {
        this.listenForMessages();
        this.listenForTyping();
        this.setupMessageForm();
        this.setupTypingDetection();
        this.scrollToBottom();
    }

    listenForMessages() {
        Echo.private(`conversation.${this.conversationId}`)
            .listen('.message.sent', (event) => {
                this.appendMessage(event);
                this.markAsRead(event.id);
            });
    }

    listenForTyping() {
        Echo.join(`conversation.${this.conversationId}`)
            .here((users) => {
                console.log('Users online:', users);
            })
            .joining((user) => {
                console.log(`${user.name} joined`);
            })
            .leaving((user) => {
                console.log(`${user.name} left`);
            })
            .listen('.user.typing', (event) => {
                this.toggleTypingIndicator(event);
            });
    }

    setupMessageForm() {
        const form = document.getElementById('messageForm');
        const input = document.getElementById('messageInput');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = input.value.trim();
            if (!message) return;

            const formData = new FormData(form);
            
            try {
                const response = await fetch('/nurse/chat/send', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                
                if (data.success) {
                    this.appendMessage({
                        ...data.message,
                        sender_id: data.message.sender.id,
                        sender_name: data.message.sender.name,
                        sender_avatar: data.message.sender.profile_img,
                    });
                    input.value = '';
                    this.stopTyping();
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });
    }

    setupTypingDetection() {
        const input = document.getElementById('messageInput');

        input.addEventListener('input', () => {
            this.startTyping();
        });

        input.addEventListener('blur', () => {
            this.stopTyping();
        });
    }

    startTyping() {
        if (this.typingTimeout) return;

        Echo.private(`conversation.${this.conversationId}`)
            .whisper('typing', {
                conversationId: this.conversationId,
                userId: window.Laravel.userId,
                userName: window.Laravel.userName,
                isTyping: true,
            });

        this.typingTimeout = setTimeout(() => {
            this.stopTyping();
        }, 2000);
    }

    stopTyping() {
        clearTimeout(this.typingTimeout);
        this.typingTimeout = null;

        Echo.private(`conversation.${this.conversationId}`)
            .whisper('typing', {
                conversationId: this.conversationId,
                userId: window.Laravel.userId,
                userName: window.Laravel.userName,
                isTyping: false,
            });
    }

    appendMessage(event) {
        const messagesContainer = document.getElementById('chatMessages');
        const isSent = event.sender_id === window.Laravel.userId;

        const messageHtml = `
            <div class="message ${isSent ? 'sent' : 'received'}" data-message-id="${event.id}">
                <div class="message-avatar">
                    <img src="${event.sender_avatar}" alt="">
                </div>
                <div class="message-content">
                    <div class="message-header">
                        <span class="sender-name">${event.sender_name}</span>
                        <span class="message-time">${new Date(event.created_at).toLocaleTimeString()}</span>
                    </div>
                    <p class="message-text">${event.message}</p>
                    ${isSent ? '<div class="message-status"><i class="fas fa-check"></i></div>' : ''}
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
        this.scrollToBottom();
    }

    toggleTypingIndicator(event) {
        const indicator = document.getElementById('typingIndicator');
        if (event.isTyping && event.userId !== window.Laravel.userId) {
            indicator.style.display = 'flex';
            this.scrollToBottom();
        } else {
            indicator.style.display = 'none';
        }
    }

    markAsRead(messageId) {
        fetch('/nurse/chat/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message_id: messageId }),
        });
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// Initialize chat
document.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) {
        const conversationId = chatContainer.dataset.conversationId;
        window.chatManager = new ChatManager(conversationId);
    }
});
```

---

## 8. API Endpoints

### 8.1 Complete API Reference

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/chat/conversations` | Get all conversations | Yes |
| GET | `/api/chat/conversation/{id}` | Get specific conversation | Yes |
| POST | `/api/chat/message` | Send a message | Yes |
| POST | `/api/chat/read` | Mark messages as read | Yes |
| DELETE | `/api/chat/message/{id}` | Delete a message | Yes |
| POST | `/api/chat/typing` | Update typing status | Yes |
| GET | `/api/chat/unread-count` | Get unread message count | Yes |
| POST | `/api/chat/start` | Start new conversation | Yes |
| POST | `/api/chat/upload` | Upload file attachment | Yes |
| POST | `/api/chat/block` | Block a user | Yes |
| GET | `/api/chat/search?q=query` | Search conversations | Yes |

### 8.2 API Response Examples

**Get Conversations Response:**
```json
{
    "success": true,
    "data": {
        "conversations": [
            {
                "id": 1,
                "subject": "Job Application",
                "nurse": {
                    "id": 5,
                    "name": "Jane Smith",
                    "profile_img": "nurse/assets/imgs/nurse01.png"
                },
                "healthcare": {
                    "id": 10,
                    "name": "City Hospital",
                    "profile_img": "healthcare/assets/imgs/hospital01.png"
                },
                "latest_message": {
                    "id": 45,
                    "message": "Thank you for your application",
                    "created_at": "2026-03-21T10:30:00Z"
                },
                "unread_count": 2,
                "last_message_at": "2026-03-21T10:30:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total": 15,
            "per_page": 20
        }
    }
}
```

**Send Message Response:**
```json
{
    "success": true,
    "message": {
        "id": 46,
        "conversation_id": 1,
        "sender_id": 5,
        "message": "Looking forward to hearing from you",
        "message_type": "text",
        "is_read": false,
        "created_at": "2026-03-21T10:35:00Z"
    }
}
```

---

## 9. Security Considerations

### 9.1 Authentication & Authorization
- All chat routes require authentication via `auth:nurse_middle` middleware
- Users can only access conversations they are part of
- Conversation access is validated before every operation

### 9.2 Data Validation
- Message length limited to 5000 characters
- File uploads limited to 10MB
- File types validated (images, PDFs, documents only)
- SQL injection prevention via Eloquent ORM

### 9.3 Privacy & Moderation
- Users can block other users
- Messages can be deleted by sender or receiver
- Admin can access all conversations for moderation
- Blocked users cannot send new messages

### 9.4 Rate Limiting
Add rate limiting to prevent spam:

**File:** `app/Http/Kernel.php`
```php
protected $routeMiddleware = [
    // ...
    'chat.throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 messages per minute
];
```

### 9.5 Content Filtering
Implement profanity filter and content moderation:

```php
// In ChatController@sendMessage
public function sendMessage(Request $request)
{
    $message = strip_tags($request->message);
    
    // Profanity filter
    if ($this->containsProfanity($message)) {
        return response()->json([
            'error' => 'Message contains inappropriate content'
        ], 422);
    }
    
    // Continue with message creation...
}
```

---

## 10. Testing Guidelines

### 10.1 Unit Tests

**File:** `tests/Feature/ChatTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_nurse_can_send_message_to_healthcare()
    {
        $nurse = User::factory()->create(['role' => 1]);
        $healthcare = User::factory()->create(['role' => 2]);
        
        $conversation = Conversation::create([
            'nurse_id' => $nurse->id,
            'healthcare_id' => $healthcare->id,
        ]);

        $response = $this->actingAs($nurse, 'nurse_middle')
            ->postJson('/nurse/chat/send', [
                'conversation_id' => $conversation->id,
                'message' => 'Hello from nurse',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $nurse->id,
            'message' => 'Hello from nurse',
        ]);
    }

    public function test_user_cannot_access_others_conversation()
    {
        $nurse = User::factory()->create(['role' => 1]);
        $healthcare = User::factory()->create(['role' => 2]);
        $otherNurse = User::factory()->create(['role' => 1]);
        
        $conversation = Conversation::create([
            'nurse_id' => $nurse->id,
            'healthcare_id' => $healthcare->id,
        ]);

        $response = $this->actingAs($otherNurse, 'nurse_middle')
            ->getJson("/nurse/chat/conversation/{$conversation->id}");

        $response->assertStatus(403);
    }

    public function test_blocked_user_cannot_send_message()
    {
        $nurse = User::factory()->create(['role' => 1]);
        $healthcare = User::factory()->create(['role' => 2]);
        
        $conversation = Conversation::create([
            'nurse_id' => $nurse->id,
            'healthcare_id' => $healthcare->id,
        ]);

        // Block nurse
        \App\Models\BlockedUser::create([
            'blocker_id' => $healthcare->id,
            'blocked_id' => $nurse->id,
        ]);

        $response = $this->actingAs($nurse, 'nurse_middle')
            ->postJson('/nurse/chat/send', [
                'conversation_id' => $conversation->id,
                'message' => 'Hello',
            ]);

        $response->assertStatus(403);
    }
}
```

### 10.2 Browser Tests (Laravel Dusk)

```php
public function test_user_can_send_and_receive_messages()
{
    $nurse = User::factory()->create(['role' => 1]);
    $healthcare = User::factory()->create(['role' => 2]);
    
    $conversation = Conversation::create([
        'nurse_id' => $nurse->id,
        'healthcare_id' => $healthcare->id,
    ]);

    $this->browse(function ($browser) use ($nurse, $conversation) {
        $browser->loginAs($nurse, 'nurse_middle')
            ->visit("/nurse/chat/conversation/{$conversation->id}")
            ->type('message', 'Test message from nurse')
            ->press('#sendBtn')
            ->waitForText('Test message from nurse')
            ->assertSee('Test message from nurse');
    });
}
```

---

## 11. Migration Files

Create migration files for all new tables:

```bash
php artisan make:migration create_conversations_table
php artisan make:migration create_messages_table
php artisan make:migration create_message_attachments_table
php artisan make:migration create_conversation_participants_table
php artisan make:migration create_blocked_users_table
```

Run migrations:
```bash
php artisan migrate
```

---

## 12. Installation Steps

1. **Install Pusher for real-time broadcasting:**
```bash
composer require pusher/pusher-php-server
```

2. **Install Laravel Echo for frontend:**
```bash
npm install laravel-echo pusher-js --save
```

3. **Configure environment variables in `.env`:**
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

4. **Create all models, migrations, and controllers as documented above**

5. **Run migrations:**
```bash
php artisan migrate
```

6. **Build frontend assets:**
```bash
npm install && npm run build
```

7. **Configure queue worker for broadcasting:**
```bash
php artisan queue:work
```

---

## 13. Future Enhancements

1. **Voice/Video Calls** - Integration with WebRTC or Twilio
2. **Message Reactions** - Emoji reactions to messages
3. **Message Forwarding** - Forward messages to other conversations
4. **Scheduled Messages** - Send messages at specific times
5. **Chatbots** - Automated responses for common queries
6. **Translation** - Real-time message translation
7. **Voice Messages** - Audio message recording and playback
8. **Screen Sharing** - For healthcare consultations
9. **Group Chats** - Multiple nurses/facilities in one conversation
10. **Analytics** - Chat response time metrics and reporting

---

## 14. Troubleshooting

### Common Issues:

1. **Messages not appearing in real-time:**
   - Check Pusher configuration
   - Ensure queue worker is running
   - Verify broadcast channel authorization

2. **File uploads failing:**
   - Check `php.ini` upload_max_filesize and post_max_size
   - Verify storage directory permissions
   - Ensure symbolic link created: `php artisan storage:link`

3. **Authentication errors:**
   - Verify guard configuration in `config/auth.php`
   - Check middleware is applied correctly

---

**Document Version:** 1.0  
**Last Updated:** March 21, 2026  
**Author:** MediQa Development Team

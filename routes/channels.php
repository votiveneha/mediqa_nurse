<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ==========================================
// Chat System Broadcast Channels
// ==========================================

// Conversation channel - only participants can access
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

// Typing status channel - only conversation participants
Broadcast::channel('conversation.{conversationId}.typing', function ($user, $conversationId) {
    $conversation = App\Models\Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }

    return in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id]);
});

// Presence channel for conversation
Broadcast::channel('conversation.{conversationId}.presence', function ($user, $conversationId) {
    $conversation = App\Models\Conversation::find($conversationId);
    
    if (!$conversation) {
        return false;
    }

    if (!in_array($user->id, [$conversation->nurse_id, $conversation->healthcare_id])) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name . ' ' . ($user->lastname ?? ''),
        'avatar' => $user->profile_img,
        'role' => $user->role,
    ];
});

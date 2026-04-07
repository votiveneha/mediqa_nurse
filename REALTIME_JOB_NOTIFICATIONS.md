# Real-Time Job Notifications for Nurses

## Overview
When a healthcare facility publishes a new job, all nurses with app notifications enabled receive **real-time notifications** through:

1. **WebSocket Broadcast** (Real-time via Pusher)
2. **Database Notifications** (Persistent fallback)

## Implementation Details

### Backend Components

#### 1. Event Class: `JobPublished`
- **Location**: `app/Events/JobPublished.php`
- **Purpose**: Broadcasts job publication to nurses via Pusher
- **Channel**: `user.{nurseId}` (private channel)
- **Event Name**: `job.published`

#### 2. Notification Class: `JobPublishedNotification`
- **Location**: `app/Notifications/JobPublishedNotification.php`
- **Purpose**: Creates persistent database notifications
- **Channels**: `database` + `broadcast` (if user has app notifications enabled)

#### 3. Modified Controller Method
- **Location**: `app/Http/Controllers/medical_facilities/JobPostingController.php`
- **Method**: `saveDraft()`
- **Changes**: When `$request->save == 2` (publish), it:
  - Sends database notifications to all nurses with `app_notification = 1`
  - Dispatches `JobPublished` event for real-time broadcast

#### 4. Database Migration
- **Location**: `database/migrations/2026_04_07_073042_create_notifications_table.php`
- **Purpose**: Laravel's standard notifications table
- **Status**: ✅ Migrated

#### 5. User Model Updates
- **Location**: `app/Models/User.php`
- **Added**:
  - `HasDatabaseNotifications` trait
  - `scopeWhereHasAppNotifications()` scope method

#### 6. JobsModel Updates
- **Location**: `app/Models/JobsModel.php`
- **Added**: `postedBy()` relationship to get the healthcare facility

### Frontend Components

#### 1. Job Notification Manager
- **Location**: `resources/js/job-notifications.js`
- **Features**:
  - Listens to `user.{userId}` channel for `job.published` events
  - Shows toast notifications (top-right corner)
  - Notification badge with unread count
  - Notification panel with history
  - Click to view job details
  - Sound notifications (when window is hidden)
  - Persistent storage via localStorage

#### 2. Built Asset
- **Location**: `public/build/assets/job-notifications-ca17ddac.js`
- **Build Command**: `npm run build`

## Setup Instructions

### For Backend (Already Done ✅)

1. **Pusher Configuration** - Ensure `.env` has:
   ```env
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your_app_id
   PUSHER_APP_KEY=your_app_key
   PUSHER_APP_SECRET=your_app_secret
   PUSHER_APP_CLUSTER=your_cluster
   ```

2. **Database Migration** - Already run:
   ```bash
   php artisan migrate --path=database/migrations/2026_04_07_073042_create_notifications_table.php --force
   ```

### For Frontend Integration

Add the job notifications script to nurse dashboard/layout views:

```blade
{{-- In your nurse dashboard layout --}}
@vite(['resources/js/job-notifications.js'])

{{-- Ensure Laravel configuration is available --}}
<script>
    window.Laravel = {
        userId: {{ auth()->id() }},
        userRole: 'nurse',
        isNurse: true,
        baseUrl: '{{ url('/') }}',
        csrfToken: '{{ csrf_token() }}'
    };
</script>
```

### Example: Nurse Dashboard View

```blade
{{-- resources/views/nurse/dashboard.blade.php --}}

@extends('layouts.nurse')

@section('scripts')
    @vite(['resources/js/job-notifications.js'])
    
    <script>
        window.Laravel = {
            userId: {{ auth()->id() }},
            userRole: 'nurse',
            isNurse: true,
            baseUrl: '{{ url('/') }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
@endsection
```

## How It Works

### When a Job is Published:

1. **Healthcare Facility** clicks "Publish" on a job
2. **Controller** (`saveDraft` method) is called with `save=2`
3. **Database Notifications** are sent to all nurses with `app_notification = 1`
4. **Event** (`JobPublished`) is dispatched
5. **Pusher** broadcasts to all subscribed nurses' private channels
6. **Frontend** (`job-notifications.js`) receives the event and:
   - Shows a toast notification (top-right)
   - Updates the notification badge
   - Plays a notification sound (if window is hidden)
   - Saves to localStorage for persistence

### Nurse User Experience:

1. **Toast Notification** appears for 8 seconds
   - Shows job title, facility name, location
   - Click to view job details in new tab
   - Auto-dismisses

2. **Notification Badge** shows unread count
   - Red circle with count
   - Click to open notification panel

3. **Notification Panel** shows all recent jobs
   - List of all job notifications
   - Unread indicators (green dots)
   - Mark all as read button
   - Click any notification to view job

4. **Persistent Storage**
   - Notifications saved in localStorage
   - Survives page refresh
   - Shows badge count on return

## Testing

### Test the Backend:

```php
// In tinker or test script
$job = JobsModel::find(1);
JobPublished::dispatch($job);
```

### Test the Frontend:

1. Open nurse dashboard in browser
2. Open browser console (F12)
3. From another terminal, trigger event:
   ```bash
   php artisan tinker
   ```
   ```php
   $job = \App\Models\JobsModel::first();
   \App\Events\JobPublished::dispatch($job);
   ```
4. Watch console for event receipt
5. Toast notification should appear

### Verify Database Notifications:

```php
// Check notifications table
$nurse = User::find({nurse_id});
$notifications = $nurse->notifications;
dump($notifications);
```

## Customization

### Change Notification Sound:
Replace `/sounds/notification.mp3` with your custom sound file.

### Adjust Notification Duration:
In `job-notifications.js`, change the timeout:
```javascript
setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease-out';
    setTimeout(() => notification.remove(), 300);
}, 8000); // Change 8000 to desired milliseconds
```

### Disable Sound:
```javascript
class JobNotificationManager {
    constructor() {
        this.soundEnabled = false; // Change to false
        // ...
    }
}
```

### Increase Max Notifications:
```javascript
this.maxNotifications = 20; // Change from 10 to 20
```

## Troubleshooting

### Notifications Not Appearing:

1. **Check Pusher Configuration**:
   ```bash
   php artisan config:clear
   ```

2. **Verify Nurse has App Notifications Enabled**:
   ```sql
   SELECT id, name, app_notification FROM users WHERE role = 'nurse';
   ```

3. **Check Browser Console**:
   - Look for Pusher connection logs
   - Check for JavaScript errors
   - Verify event receipt

4. **Verify Channel Authentication**:
   Check `/broadcasting/auth` endpoint is working for nurse guard

### Database Notifications Not Saving:

1. **Verify Migration**:
   ```bash
   php artisan migrate:status
   ```

2. **Check User Model**:
   Ensure `HasDatabaseNotifications` trait is present

### Sound Not Playing:

- Browser requires user interaction before playing audio
- Check browser permissions for audio
- Verify `/sounds/notification.mp3` exists

## API Endpoints

No new API endpoints required. Uses existing:
- `/broadcasting/auth` - Channel authentication
- Pusher WebSocket - Real-time events

## Database Schema

### `notifications` Table
```sql
- id (UUID)
- type (string)
- notifiable_type (string)
- notifiable_id (bigint)
- data (text - JSON)
- read_at (timestamp, nullable)
- created_at
- updated_at
```

## Performance Considerations

- **Event Broadcasting**: Uses `ShouldBroadcastNow` for immediate delivery
- **Database Notifications**: Sent synchronously (consider queueing for large user bases)
- **Frontend Storage**: Limited to last 10 notifications in localStorage
- **Pusher Limits**: Check your Pusher plan for message limits

## Future Enhancements

- [ ] Add job preference matching (only notify nurses whose preferences match)
- [ ] Email notification fallback
- [ ] SMS notifications for urgent jobs
- [ ] Notification settings per nurse
- [ ] Bulk notification queuing for large user bases
- [ ] Analytics on notification open rates
- [ ] Mark notifications as read from panel
- [ ] Filter notifications by specialty/location

## Support

For issues or questions, check:
1. Browser console for frontend errors
2. Laravel logs (`storage/logs/laravel.log`)
3. Pusher dashboard for connection stats
4. Network tab for WebSocket connection status

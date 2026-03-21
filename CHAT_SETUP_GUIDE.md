# Chat System Setup Guide

## Quick Start

Follow these steps to set up the Nurse-Healthcare Chat System:

### 1. Install Dependencies

```bash
# Install Pusher PHP SDK
composer require pusher/pusher-php-server

# Install Laravel Echo and Pusher JS
npm install laravel-echo pusher-js --save
```

### 2. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `conversations`
- `messages`
- `message_attachments`
- `conversation_participants`
- `blocked_users`

### 3. Configure Broadcasting

Update your `.env` file with Pusher credentials:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

### 4. Configure Broadcast Channels

The channels are already configured in `routes/channels.php`. Ensure broadcasting is enabled in `config/broadcasting.php`.

### 5. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Queue Worker

For real-time broadcasting, start the queue worker:

```bash
php artisan queue:work
```

### 7. Set Up Storage Link

For file attachments:

```bash
php artisan storage:link
```

### 8. Configure Guards (if needed)

Ensure the `nurse_middle` guard is configured in `config/auth.php`:

```php
'guards' => [
    'nurse_middle' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
],
```

## Testing the Chat System

### For Nurses:
1. Login as a nurse
2. Navigate to `/nurse/chat`
3. Click "Start New Chat" or select an existing conversation
4. Send messages to healthcare facilities

### For Healthcare Facilities:
1. Login as a healthcare facility
2. Navigate to `/healthcare/chat`
3. Click "Browse Nurses" to find nurses to chat with
4. Start a conversation or select an existing one

## Features

### Core Features
- ✅ Real-time messaging
- ✅ Typing indicators
- ✅ Online/Offline status
- ✅ Read receipts (single/double check marks)
- ✅ File attachments (images, documents)
- ✅ Message search
- ✅ Conversation list with unread count
- ✅ Delete messages
- ✅ Block users
- ✅ Archive conversations

### User Interface
- ✅ Responsive design (mobile-friendly)
- ✅ Modern gradient theme
- ✅ Smooth animations
- ✅ Emoji support
- ✅ Auto-scroll to new messages
- ✅ Message reply preview

## API Endpoints

### Web Routes (Nurse)
- `GET /nurse/chat` - Chat dashboard
- `GET /nurse/chat/conversation/{id}` - View conversation
- `POST /nurse/chat/send` - Send message
- `POST /nurse/chat/start` - Start new conversation
- `POST /nurse/chat/upload` - Upload file
- `POST /nurse/chat/delete` - Delete message
- `POST /nurse/chat/block` - Block user
- `GET /nurse/chat/search` - Search conversations
- `GET /nurse/chat/unread-count` - Get unread count

### Web Routes (Healthcare)
- `GET /healthcare/chat` - Chat dashboard
- `GET /healthcare/chat/conversation/{id}` - View conversation
- `GET /healthcare/chat/nurses` - Browse nurses
- `POST /healthcare/chat/send` - Send message
- (Same additional routes as nurse)

### API Routes
- `GET /api/chat/conversations` - Get all conversations
- `GET /api/chat/conversation/{id}` - Get specific conversation
- `POST /api/chat/message` - Send message
- `POST /api/chat/read` - Mark as read
- `DELETE /api/chat/message/{id}` - Delete message
- `POST /api/chat/typing` - Update typing status
- `GET /api/chat/unread-count` - Get unread count

## Troubleshooting

### Messages not appearing in real-time
1. Check Pusher configuration in `.env`
2. Ensure queue worker is running: `php artisan queue:work`
3. Verify broadcast channel authorization in `routes/channels.php`
4. Check browser console for JavaScript errors

### File uploads failing
1. Check `php.ini` settings:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`
2. Verify storage directory is writable
3. Ensure symbolic link exists: `php artisan storage:link`

### Authentication errors
1. Verify `nurse_middle` guard in `config/auth.php`
2. Check middleware is applied to routes
3. Ensure user is properly authenticated

### Echo connection issues
1. Verify Pusher credentials are correct
2. Check if Pusher app is enabled for your cluster
3. Ensure CORS is configured properly
4. Check firewall settings for Pusher ports

## Customization

### Change Theme Colors
Edit `public/css/chat.css` and modify the gradient values:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Modify Message Limit
In `ChatController.php`, change the validation:
```php
'message' => 'required|string|max:5000', // Change 5000 to desired limit
```

### Adjust File Upload Size
In `ChatController.php`, modify:
```php
'file' => 'required|file|max:10240', // Change 10240 (10MB) to desired size
```

## Security Considerations

1. **Authentication**: All routes require authentication via `nurse_middle` guard
2. **Authorization**: Users can only access conversations they participate in
3. **CSRF Protection**: All forms include CSRF tokens
4. **XSS Prevention**: Messages are escaped before display
5. **File Validation**: Uploaded files are validated for type and size
6. **Rate Limiting**: Consider adding rate limiting for message sending

## Performance Optimization

1. **Pagination**: Conversations are paginated (20 per page)
2. **Lazy Loading**: Messages load on demand
3. **Database Indexing**: Key columns are indexed for faster queries
4. **Caching**: Online status is cached
5. **Queue**: Broadcasting uses queue for better performance

## Next Steps

1. Set up Pusher account at https://pusher.com
2. Configure your Pusher app credentials
3. Test the chat system with multiple users
4. Customize the UI to match your brand
5. Add additional features as needed (voice messages, video calls, etc.)

## Support

For issues or questions:
1. Check the documentation in `CHAT_SYSTEM_DOCUMENTATION.md`
2. Review Laravel broadcasting documentation
3. Check Pusher documentation for real-time features

<?php
// Fix healthcare chat view
$file = __DIR__ . '/resources/views/healthcare/chat/conversation.blade.php';

if (!file_exists($file)) {
    die("File not found: $file\n");
}

$content = file_get_contents($file);

// Replace old Pusher/Echo CDN versions and fix configuration
$replacements = [
    // Update Pusher CDN
    '<script src="https://js.pusher.com/7.0/pusher.min.js"></script>' 
    => '<script src="https://js.pusher.com/8.4/pusher.min.js"></script>',
    
    // Update Echo CDN
    '<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.10.0/dist/echo.iife.js"></script>'
    => '<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.min.js"></script>',
    
    // Fix Echo configuration - use config() instead of env()
    "key: '{{ env(\"PUSHER_APP_KEY\") }}'"
    => "key: '{{ config(\"broadcasting.connections.pusher.key\") }}'",
    
    // Add better logging
    "console.log('Real-time message received:', data);"
    => "console.log('=== Real-time Message Received ===', data);"
];

$newContent = str_replace(array_keys($replacements), array_values($replacements), $content);

// Backup original
$backupFile = $file . '.backup2';
copy($file, $backupFile);
echo "Backup created: $backupFile\n";

// Write updated content
file_put_contents($file, $newContent);
echo "✅ Healthcare chat view updated!\n";
echo "File: $file\n";
echo "\nPlease clear cache:\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
?>

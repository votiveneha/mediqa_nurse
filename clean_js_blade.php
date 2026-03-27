<?php
$file = 'c:/xampp_8.2.12/htdocs/mediqa_nurse/resources/views/nurse/layouts/js.blade.php';
$content = file_get_contents($file);

// Find the sweetalert2 line and keep only that, remove everything after
$pos = strpos($content, '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>');
if ($pos !== false) {
    // Keep everything up to and including sweetalert2
    $content = substr($content, 0, $pos + strlen('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'));
    
    // Add proper notification script tag
    $content .= PHP_EOL . PHP_EOL . '<!-- Chat Notification Counter -->' . PHP_EOL;
    $content .= '@vite(["resources/js/chat-notification-counter.js"])' . PHP_EOL;
    
    file_put_contents($file, $content);
    echo "✅ Fixed! Cleaned up js.blade.php\n";
    echo "Last 5 lines:\n";
    $lines = explode(PHP_EOL, $content);
    echo implode(PHP_EOL, array_slice($lines, -5));
} else {
    echo "❌ Could not find sweetalert2 script\n";
}
?>

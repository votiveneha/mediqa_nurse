<?php
$file = 'c:/xampp_8.2.12/htdocs/mediqa_nurse/resources/views/nurse/layouts/js.blade.php';
$content = file_get_contents($file);

// Remove the problematic lines
$lines = explode(PHP_EOL, $content);
$clean = [];
foreach ($lines as $line) {
    $trimmed = trim($line);
    // Skip empty lines and the problematic quoted lines
    if ($trimmed === '""' || strpos($trimmed, 'chat-notification-counter') !== false) {
        continue;
    }
    $clean[] = $line;
}

// Add proper line at the end
$clean[] = '';
$clean[] = '<!-- Chat Notification Counter -->';
$clean[] = '@vite(["resources/js/chat-notification-counter.js"])';

file_put_contents($file, implode(PHP_EOL, $clean));
echo "✅ Fixed js.blade.php\n";
?>

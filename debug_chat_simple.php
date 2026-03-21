<?php

/**
 * Simple Chat Debug - No Auth Required
 * Just enter a nurse ID to test
 * Access: http://localhost/mediqa_nurse/debug_chat_simple.php
 */

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');

// Get nurse ID from URL or form
$nurseId = isset($_GET['nurse_id']) ? (int)$_GET['nurse_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nurse_id'])) {
    $nurseId = (int)$_POST['nurse_id'];
    header("Location: debug_chat_simple.php?nurse_id={$nurseId}");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat Debug - Simple</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #667eea; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin-bottom: 20px; }
        .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; }
        .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; }
        .warning { background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #5568d3; }
        .btn-success { background: #4caf50; }
        .btn-success:hover { background: #45a049; }
        input[type="number"] { padding: 10px; border: 1px solid #ddd; border-radius: 5px; width: 200px; }
        .nurse-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .nurse-btn { background: #667eea; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 13px; }
        .nurse-btn:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Chat System Debug</h1>
        
        <?php
        if ($nurseId > 0) {
            // Get nurse info
            $nurse = DB::table('users')->find($nurseId);
            
            if ($nurse) {
                echo "<div class='success'>";
                echo "<strong>Testing Nurse:</strong> {$nurse->name} (ID: {$nurseId})<br>";
                echo "<strong>Email:</strong> {$nurse->email}<br>";
                echo "<strong>Role:</strong> {$nurse->role}<br>";
                echo "<a href='/nurse/chat' class='btn' style='margin-top:10px;'>📨 Go to Chat →</a>";
                echo "</div>";
            }

            // 1. Check job applications
            $applications = DB::table('nurse_applications')
                ->where('nurse_id', $nurseId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            echo "<h2>📋 Job Applications ({$applications->count()})</h2>";
            if ($applications->isEmpty()) {
                echo "<div class='warning'>No job applications found for this nurse.</div>";
            } else {
                echo "<table>";
                echo "<tr><th>ID</th><th>Job ID</th><th>Employer ID</th><th>Job Title</th><th>Status</th><th>Applied At</th></tr>";
                foreach ($applications as $app) {
                    $jobTitle = isset($app->job_title) ? $app->job_title : 'N/A';
                    $appliedAt = isset($app->applied_at) ? $app->applied_at : $app->created_at;
                    
                    echo "<tr>";
                    echo "<td>{$app->id}</td>";
                    echo "<td>{$app->job_id}</td>";
                    echo "<td>{$app->employer_id}</td>";
                    echo "<td>{$jobTitle}</td>";
                    echo "<td>{$app->status}</td>";
                    echo "<td>{$appliedAt}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            // 2. Check conversations
            $conversations = DB::table('conversations')
                ->where('nurse_id', $nurseId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            echo "<h2>💬 Conversations ({$conversations->count()})</h2>";
            if ($conversations->isEmpty()) {
                echo "<div class='error'>";
                echo "<strong>⚠️ No conversations found!</strong><br>";
                echo "This is why chat is empty. Click the button below to create conversations for your job applications.";
                echo "</div>";
                
                // Create conversations button
                if (!$applications->isEmpty()) {
                    echo "<form method='POST' style='margin: 20px 0;'>";
                    echo "<input type='hidden' name='nurse_id' value='{$nurseId}'>";
                    echo "<input type='hidden' name='action' value='create_conversations'>";
                    echo "<button type='submit' class='btn btn-success'>🚀 Create Missing Conversations</button>";
                    echo "</form>";
                }
            } else {
                echo "<table>";
                echo "<tr><th>ID</th><th>Subject</th><th>Healthcare ID</th><th>Job ID</th><th>Status</th><th>Created</th></tr>";
                foreach ($conversations as $conv) {
                    $subject = isset($conv->subject) ? $conv->subject : '-';
                    $jobId = isset($conv->job_id) ? $conv->job_id : '-';
                    
                    echo "<tr>";
                    echo "<td>{$conv->id}</td>";
                    echo "<td>{$subject}</td>";
                    echo "<td>{$conv->healthcare_id}</td>";
                    echo "<td>{$jobId}</td>";
                    echo "<td>{$conv->status}</td>";
                    echo "<td>{$conv->created_at}</td>";
                    echo "</tr>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            // Handle create conversations
            if (isset($_POST['action']) && $_POST['action'] === 'create_conversations') {
                $created = 0;
                
                foreach ($applications as $app) {
                    $exists = DB::table('conversations')
                        ->where('nurse_id', $nurseId)
                        ->where('healthcare_id', $app->employer_id)
                        ->where('job_id', $app->job_id)
                        ->exists();
                    
                    if (!$exists) {
                        $jobTitle = isset($app->job_title) ? $app->job_title : 'Position';
                        
                        $convId = DB::table('conversations')->insertGetId([
                            'subject' => 'Job Application: ' . $jobTitle,
                            'job_id' => $app->job_id,
                            'nurse_id' => $nurseId,
                            'healthcare_id' => $app->employer_id,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        // Add system message
                        DB::table('messages')->insert([
                            'conversation_id' => $convId,
                            'sender_id' => $nurseId,
                            'sender_type' => 'nurse',
                            'message' => 'You have submitted your application for this position.',
                            'message_type' => 'system',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        // Add participants
                        DB::table('conversation_participants')->insert([
                            ['conversation_id' => $convId, 'user_id' => $nurseId, 'created_at' => now()],
                            ['conversation_id' => $convId, 'user_id' => $app->employer_id, 'created_at' => now()],
                        ]);
                        
                        $created++;
                    }
                }
                
                echo "<div class='success'>✅ Created {$created} conversation(s) successfully! <a href='debug_chat_simple.php?nurse_id={$nurseId}'>Refresh to see</a></div>";
            }

        } else {
            // Show nurse selection
            echo "<div class='info'>";
            echo "<strong>Select a nurse to test:</strong>";
            echo "<form method='GET' style='margin: 15px 0;'>";
            echo "<input type='number' name='nurse_id' placeholder='Enter Nurse ID' min='1' required> ";
            echo "<button type='submit' class='btn'>Test</button>";
            echo "</form>";
            echo "</div>";
            
            // Show list of nurses - don't filter by status to show all
            echo "<h3>Recent Nurses</h3>";
            
            $nurses = DB::table('users')
                ->where('role', 1)
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
            
            if ($nurses->isEmpty()) {
                echo "<div class='warning'>No nurses found with role=1.</div>";
            } else {
                echo "<div class='nurse-list'>";
                foreach ($nurses as $n) {
                    $statusLabel = $n->status == 1 ? '✓' : '✗';
                    echo "<a href='?nurse_id={$n->id}' class='nurse-btn'>{$n->name} (ID: {$n->id}) [Status: {$statusLabel}]</a> ";
                }
                echo "</div>";
            }
        }
        ?>

        <hr style="margin-top: 40px;">
        <p style="color: #999; font-size: 12px;">
            Delete this file after debugging for security.
        </p>
    </div>
</body>
</html>

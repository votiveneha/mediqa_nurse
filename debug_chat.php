<?php

/**
 * Debug Script: Check Chat Conversations
 * Access: http://localhost/mediqa_nurse/debug_chat.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Conversation;
use App\Models\Message;
use App\Models\NurseApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin-bottom: 20px; }
        .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; }
        .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; }
        .warning { background: #fff3e0; padding: 15px; border-left: 4px solid #ff9800; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .count { font-size: 2em; font-weight: bold; color: #667eea; }
    </style>
</head>
<body>
    <h1>🔍 Chat System Debug</h1>
    
    <?php
    try {
        // Initialize Laravel session and auth
        if (!Auth::guard('nurse_middle')->check()) {
            // Try to get user from session
            $sessionId = session_id();
            if (!$sessionId && isset($_COOKIE[session_name()])) {
                session_id($_COOKIE[session_name()]);
                session_start();
            }
        }
        
        // Get current logged in user
        $currentUser = Auth::guard('nurse_middle')->user();
        
        if ($currentUser) {
            echo "<div class='success'>";
            echo "<strong>Current User:</strong> {$currentUser->name} (ID: {$currentUser->id})<br>";
            echo "<strong>Guard:</strong> nurse_middle<br>";
            echo "<strong>Role:</strong> {$currentUser->role}<br>";
            echo "<strong>Email:</strong> {$currentUser->email}";
            echo "</div>";
            
            $nurseId = $currentUser->id;
        } else {
            echo "<div class='warning'>";
            echo "<strong>No user logged in with nurse_middle guard.</strong><br>";
            echo "Please login as a nurse first, or specify a nurse ID below.<br><br>";
            echo "<strong>Available guards:</strong><br>";
            echo "• nurse_middle (your nurse login)<br>";
            echo "• web (standard Laravel)<br>";
            echo "</div>";
            
            // Show all nurses for quick testing
            $nurses = DB::table('users')->where('role', 1)->where('status', 1)->limit(5)->get();
            if (!$nurses->isEmpty()) {
                echo "<div class='info'>";
                echo "<strong>Quick Test - Click a nurse to test:</strong><br>";
                foreach ($nurses as $nurse) {
                    echo "<a href='debug_chat.php?nurse_id={$nurse->id}' style='display:inline-block; margin:5px; padding:8px 15px; background:#667eea; color:white; text-decoration:none; border-radius:5px;'>{$nurse->name} (ID: {$nurse->id})</a> ";
                }
                echo "</div>";
            }
            
            // Allow manual ID input for testing
            $nurseId = isset($_GET['nurse_id']) ? (int)$_GET['nurse_id'] : 0;
            if ($nurseId > 0) {
                echo "<div class='info'>Testing with Nurse ID: <strong>{$nurseId}</strong></div>";
            }
        }

        if ($nurseId > 0) {
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
                    $jobTitle = isset($app->job_title) ? $app->job_title : '-';
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
                echo "<div class='warning'>";
                echo "No conversations found.<br>";
                echo "<strong>This is the issue!</strong> Conversations are not being created when applying to jobs.<br>";
                echo "</div>";
                
                // Show what SHOULD exist
                echo "<div class='info'>";
                echo "<strong>Expected conversations based on applications:</strong><br>";
                foreach ($applications as $app) {
                    $jobTitle = isset($app->job_title) ? $app->job_title : 'Position';
                    
                    echo "• Nurse {$nurseId} → Healthcare {$app->employer_id} (Job {$app->job_id})<br>";
                }
                echo "</div>";
            } else {
                echo "<table>";
                echo "<tr><th>ID</th><th>Subject</th><th>Healthcare ID</th><th>Job ID</th><th>Status</th><th>Last Message</th><th>Created</th></tr>";
                foreach ($conversations as $conv) {
                    $subject = isset($conv->subject) ? $conv->subject : '-';
                    $jobId = isset($conv->job_id) ? $conv->job_id : '-';
                    $lastMessageId = isset($conv->last_message_id) ? $conv->last_message_id : 'None';
                    
                    echo "<tr>";
                    echo "<td>{$conv->id}</td>";
                    echo "<td>{$subject}</td>";
                    echo "<td>{$conv->healthcare_id}</td>";
                    echo "<td>{$jobId}</td>";
                    echo "<td>{$conv->status}</td>";
                    echo "<td>{$lastMessageId}</td>";
                    echo "<td>{$conv->created_at}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            // 3. Check messages
            $conversationIds = $conversations->pluck('id')->toArray();
            $messages = DB::table('messages')
                ->whereIn('conversation_id', $conversationIds)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            echo "<h2>📨 Recent Messages ({$messages->count()})</h2>";
            if ($messages->isEmpty()) {
                echo "<div class='info'>No messages in conversations yet.</div>";
            } else {
                echo "<table>";
                echo "<tr><th>ID</th><th>Conv ID</th><th>Sender</th><th>Type</th><th>Message</th><th>Created</th></tr>";
                foreach ($messages as $msg) {
                    echo "<tr>";
                    echo "<td>{$msg->id}</td>";
                    echo "<td>{$msg->conversation_id}</td>";
                    echo "<td>{$msg->sender_id} ({$msg->sender_type})</td>";
                    echo "<td>{$msg->message_type}</td>";
                    echo "<td>" . substr($msg->message, 0, 50) . "...</td>";
                    echo "<td>{$msg->created_at}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            // 4. Check participants
            $participants = DB::table('conversation_participants')
                ->whereIn('conversation_id', $conversationIds)
                ->get();
            
            echo "<h2>👥 Conversation Participants ({$participants->count()})</h2>";
            if (!$participants->isEmpty()) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Conv ID</th><th>User ID</th><th>Unread</th><th>Last Seen</th></tr>";
                foreach ($participants as $part) {
                    $lastSeen = isset($part->last_seen_at) ? $part->last_seen_at : '-';
                    
                    echo "<tr>";
                    echo "<td>{$part->id}</td>";
                    echo "<td>{$part->conversation_id}</td>";
                    echo "<td>{$part->user_id}</td>";
                    echo "<td>{$part->unread_count}</td>";
                    echo "<td>{$lastSeen}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }

            // 5. Fix button
            echo "<h2>🔧 Quick Fix</h2>";
            echo "<div class='info'>";
            echo "<p>Create missing conversations for your job applications:</p>";
            echo "<form method='POST' action='debug_chat.php?action=create_conversations&nurse_id={$nurseId}'>";
            echo "<button type='submit' style='background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
            echo "🚀 Create Missing Conversations";
            echo "</button>";
            echo "</form>";
            echo "</div>";

            // 6. Test application
            if ($applications->isEmpty()) {
                echo "<h2>🧪 Create Test Application</h2>";
                echo "<div class='info'>";
                echo "<p>Create a test job application to verify the flow:</p>";
                echo "<form method='POST' action='debug_chat.php?action=create_test_application&nurse_id={$nurseId}'>";
                echo "<button type='submit' style='background: #4caf50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
                echo "📝 Create Test Application";
                echo "</button>";
                echo "</form>";
                echo "</div>";
            }
        }

        // Handle actions
        if (isset($_GET['action'])) {
            echo "<hr><h2>Action Results</h2>";
            
            if ($_GET['action'] === 'create_conversations' && $nurseId > 0) {
                $applications = DB::table('nurse_applications')->where('nurse_id', $nurseId)->get();
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
                
                echo "<div class='success'>Created {$created} conversation(s) successfully!</div>";
                echo "<a href='debug_chat.php?nurse_id={$nurseId}'>← Back to Debug</a>";
            }
            
            if ($_GET['action'] === 'create_test_application' && $nurseId > 0) {
                // Get first available job
                $job = DB::table('job_boxes')->first();
                if ($job) {
                    DB::table('nurse_applications')->insert([
                        'nurse_id' => $nurseId,
                        'job_id' => $job->id,
                        'employer_id' => isset($job->healthcare_id) ? $job->healthcare_id : 1,
                        'job_title' => isset($job->job_title) ? $job->job_title : 'Test Position',
                        'status' => 1,
                        'applied_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    echo "<div class='success'>Created test application for job: " . (isset($job->job_title) ? $job->job_title : 'Unknown') . "</div>";
                } else {
                    echo "<div class='error'>No jobs available in database</div>";
                }
                echo "<a href='debug_chat.php?nurse_id={$nurseId}'>← Back to Debug</a>";
            }
        }

    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<strong>Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
    ?>

    <hr>
    <p style="color: #666; font-size: 12px;">
        Delete this file after debugging for security reasons.
    </p>
</body>
</html>

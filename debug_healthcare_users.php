<?php

/**
 * Debug Script: Check Healthcare Users in Database
 * 
 * Access this file at: http://localhost/mediqa_nurse/debug_healthcare_users.php
 * 
 * This will show all users with role = 2 (healthcare) to help debug the dropdown issue.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Healthcare Users Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin-bottom: 20px; }
        .error { background: #ffebee; padding: 15px; border-left: 4px solid #f44336; }
        .success { background: #e8f5e9; padding: 15px; border-left: 4px solid #4caf50; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Healthcare Users Debug</h1>
    
    <div class="info">
        <strong>Purpose:</strong> This page shows all users in the database to help debug the chat dropdown issue.
    </div>

    <?php
    try {
        // Check total users
        $totalUsers = DB::table('users')->count();
        echo "<div class='info'>";
        echo "<strong>Total Users:</strong> {$totalUsers}";
        echo "</div>";

        // Get users by role
        $usersByRole = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();
        
        echo "<h2>Users by Role</h2>";
        echo "<table><tr><th>Role</th><th>Count</th><th>Description</th></tr>";
        foreach ($usersByRole as $row) {
            $desc = match($row->role) {
                1 => 'Nurse',
                2 => 'Healthcare Facility',
                3 => 'Agency',
                4 => 'CPD Provider',
                default => 'Unknown'
            };
            echo "<tr><td>{$row->role}</td><td>{$row->count}</td><td>{$desc}</td></tr>";
        }
        echo "</table>";

        // Get all healthcare users (role = 2)
        $healthcareUsers = DB::table('users')
            ->where('role', 2)
            ->orderBy('created_at', 'desc')
            ->get();
        
        echo "<h2>Healthcare Facilities (role = 2)</h2>";
        
        if ($healthcareUsers->isEmpty()) {
            echo "<div class='error'>";
            echo "<strong>No healthcare users found!</strong><br>";
            echo "This is why the dropdown is empty. You need to either:<br>";
            echo "<ol>";
            echo "<li>Create healthcare facility accounts</li>";
            echo "<li>Or change existing users' role to 2</li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div class='success'>Found {$healthcareUsers->count()} healthcare facility user(s)</div>";
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th><th>Name</th><th>Lastname</th><th>Email</th>";
            echo "<th>Status</th><th>Profile Status</th><th>Created</th>";
            echo "</tr>";
            
            foreach ($healthcareUsers as $user) {
                $statusClass = $user->status == 1 ? 'success' : 'error';
                $lastname = isset($user->lastname) ? $user->lastname : '-';
                $profileStatus = isset($user->profile_status) ? $user->profile_status : 'N/A';
                $createdAt = isset($user->created_at) ? $user->created_at : 'N/A';
                
                echo "<tr>";
                echo "<td>{$user->id}</td>";
                echo "<td>{$user->name}</td>";
                echo "<td>{$lastname}</td>";
                echo "<td>{$user->email}</td>";
                echo "<td><span class='{$statusClass}'>" . ($user->status == 1 ? '✓ Active' : '✗ Inactive') . "</span></td>";
                echo "<td>{$profileStatus}</td>";
                echo "<td>{$createdAt}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Show recent users
        $recentUsers = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        echo "<h2>Recent Users (All Roles)</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr>";
        foreach ($recentUsers as $user) {
            echo "<tr>";
            echo "<td>{$user->id}</td>";
            echo "<td>{$user->name} {$user->lastname}</td>";
            echo "<td>{$user->email}</td>";
            echo "<td>{$user->role}</td>";
            echo "<td>" . ($user->status == 1 ? 'Active' : 'Inactive') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // SQL to create test healthcare user
        echo "<h2>🔧 Quick Fix: Create Test Healthcare User</h2>";
        echo "<div class='info'>";
        echo "<p>Run this SQL to create a test healthcare facility:</p>";
        echo "<pre>";
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        echo "INSERT INTO users (name, lastname, email, password, role, status, profile_status, created_at, updated_at)\n";
        echo "VALUES ('Test', 'Healthcare Facility', 'healthcare@test.com', '{$hashedPassword}', 2, 1, 'Yes', NOW(), NOW());";
        echo "</pre>";
        echo "<p><strong>Login:</strong> healthcare@test.com / password123</p>";
        echo "</div>";

        // SQL to convert a nurse to healthcare
        echo "<h2>🔧 Convert Existing User to Healthcare</h2>";
        echo "<div class='info'>";
        echo "<p>To convert a specific user to healthcare facility, run:</p>";
        echo "<pre>UPDATE users SET role = 2 WHERE id = YOUR_USER_ID;</pre>";
        echo "</div>";

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

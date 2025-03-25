<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = "localhost";
$dbname = "instagram_clone";
$username = "root";
$password = "";

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get all users
try {
    $stmt = $conn->prepare("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Get all login logs
try {
    $stmt = $conn->prepare("SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 100");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram Clone - User Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #fafafa;
        }
        h1, h2 {
            color: #262626;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background-color: #fff;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            padding: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dbdbdb;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-success {
            color: #28a745;
        }
        .status-failed {
            color: #dc3545;
        }
        .status-new {
            color: #007bff;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0095f6;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html" class="back-link">← Back to Login</a>
        
        <h1>Instagram Clone Database</h1>
        
        <div class="card">
            <h2>Registered Users</h2>
            <?php if (count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found in the database.</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Login Logs</h2>
            <?php if (count($logs) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>Status</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['id']); ?></td>
                                <td><?php echo htmlspecialchars($log['user_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['username']); ?></td>
                                <td><?php echo htmlspecialchars($log['email']); ?></td>
                                <td><?php echo htmlspecialchars($log['login_time']); ?></td>
                                <td class="status-<?php echo $log['status'] === 'success' ? 'success' : ($log['status'] === 'new_user' ? 'new' : 'failed'); ?>">
                                    <?php echo htmlspecialchars($log['status']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No login logs found in the database.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


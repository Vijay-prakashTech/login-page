<?php

require_once "config.php";
// Database configuration
$host = "sql12.freesqldatabase.com";
$dbname = "sql12770417";
$username = "sql12770417";
$password = "knrSMX4L4u";

// Create connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Process login or registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userInput = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($userInput) || empty($password)) {
        echo json_encode(['success' => false, 'message' => "Username/email and password are required"]);
        exit;
    }

    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :userInput OR email = :userInput LIMIT 1");
        $stmt->bindParam(':userInput', $userInput);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User exists, check password
            if ($password === $user['password']) {
                $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, username, email, login_time, status) 
                                           VALUES (:user_id, :username, :email, NOW(), 'success')");
                $logStmt->bindParam(':user_id', $user['id']);
                $logStmt->bindParam(':username', $user['username']);
                $logStmt->bindParam(':email', $user['email']);
                $logStmt->execute();

                echo json_encode(['success' => true, 'message' => "Login successful"]);
            } else {
                echo json_encode(['success' => false, 'message' => "Invalid password"]);
            }
        } else {
            // User does not exist, register new user
            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $insertStmt->bindParam(':username', $userInput);
            $insertStmt->bindParam(':email', $userInput); // Assuming email = username for simplicity
            $insertStmt->bindParam(':password', $password);
            $insertStmt->execute();

            // Log new registration
            $logStmt = $conn->prepare("INSERT INTO login_logs (username, email, login_time, status) 
                                       VALUES (:username, :email, NOW(), 'new_user')");
            $logStmt->bindParam(':username', $userInput);
            $logStmt->bindParam(':email', $userInput);
            $logStmt->execute();

            echo json_encode(['success' => true, 'message' => "User registered successfully"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
    }
}

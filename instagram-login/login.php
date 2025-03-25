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
    $response = [
        'success' => false,
        'message' => "Connection failed: " . $e->getMessage()
    ];
    echo json_encode($response);
    exit;
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $userInput = trim($_POST["username"]);
    $password = $_POST["password"];
    
    // Validate input
    if (empty($userInput) || empty($password)) {
        $response = [
            'success' => false,
            'message' => "Username/email and password are required"
        ];
        echo json_encode($response);
        exit;
    }
    
    try {
        // Check if user exists (by username or email)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :userInput OR email = :userInput LIMIT 1");
        $stmt->bindParam(':userInput', $userInput);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // User exists - log the login attempt
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Log the login attempt
            $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, username, email, login_time, status, ip_address, user_agent) 
                                      VALUES (:user_id, :username, :email, NOW(), 'success', :ip, :user_agent)");
            $logStmt->bindParam(':user_id', $user['id']);
            $logStmt->bindParam(':username', $user['username']);
            $logStmt->bindParam(':email', $user['email']);
            $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $logStmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
            $logStmt->execute();
            
            // Success response
            $response = [
                'success' => true,
                'message' => "Login successful. User already exists in database."
            ];
        } else {
            // User doesn't exist - save the new credentials
            
            // Determine if input is email or username
            $isEmail = filter_var($userInput, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                $email = $userInput;
                $username = explode('@', $email)[0]; // Use part before @ as username
            } else {
                $username = $userInput;
                $email = $username . "@example.com"; // Create a placeholder email
            }
            
            // Insert new user
            $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) 
                                         VALUES (:username, :email, :password, NOW())");
            $insertStmt->bindParam(':username', $username);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':password', $password); // Note: In production, use password_hash()
            $insertStmt->execute();
            
            $userId = $conn->lastInsertId();
            
            // Log the new user creation
            $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, username, email, login_time, status, ip_address, user_agent) 
                                      VALUES (:user_id, :username, :email, NOW(), 'new_user', :ip, :user_agent)");
            $logStmt->bindParam(':user_id', $userId);
            $logStmt->bindParam(':username', $username);
            $logStmt->bindParam(':email', $email);
            $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
            $logStmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
            $logStmt->execute();
            
            $response = [
                'success' => true,
                'message' => "New user created and logged in successfully!"
            ];
        }
        
        echo json_encode($response);
        
    } catch(PDOException $e) {
        $response = [
            'success' => false,
            'message' => "Error: " . $e->getMessage()
        ];
        echo json_encode($response);
    }
}
?>


<?php
require_once "config.php";
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration - modify these to match your environment
// Database configuration
$host = "sql12.freesqldatabase.com";
$dbname = "sql12770417";
$username = "sql12770417";
$password = "knrSMX4L4u";

echo "<h2>Instagram Clone Database Setup</h2>";
echo "<div style='font-family: Arial, sans-serif; line-height: 1.6;'>";

// Check if PDO extension is loaded
if (!extension_loaded('pdo')) {
    die("<p style='color: red;'>Error: PDO extension is not loaded. Please enable it in your PHP configuration.</p>");
}

if (!extension_loaded('pdo_mysql')) {
    die("<p style='color: red;'>Error: PDO_MYSQL extension is not loaded. Please enable it in your PHP configuration.</p>");
}

try {
    echo "<p>Attempting to connect to MySQL server at $host...</p>";

    // Create connection without database first
    $conn = new PDO("mysql:host=$host", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p style='color: green;'>✓ Connected to MySQL server successfully!</p>";

    // Create database
    echo "<p>Creating database '$dbname' if it doesn't exist...</p>";
    $sql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Database created or already exists!</p>";

    // Connect to the specific database
    echo "<p>Connecting to database '$dbname'...</p>";
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Connected to database successfully!</p>";

    // Create users table
    echo "<p>Creating 'users' table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(30) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100),
        `profile_pic` VARCHAR(255),
        `bio` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `username_unique` (`username`),
        UNIQUE KEY `email_unique` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Users table created successfully!</p>";

    // Create login_logs table
    echo "<p>Creating 'login_logs' table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS `login_logs` (
        `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT(11) UNSIGNED,
        `username` VARCHAR(30),
        `email` VARCHAR(100),
        `login_time` DATETIME,
        `status` ENUM('success', 'failed') NOT NULL,
        `ip_address` VARCHAR(45),
        `user_agent` TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Login logs table created successfully!</p>";

    // Check if sample user already exists
    echo "<p>Checking for existing sample user...</p>";
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'demo_user' OR `email` = 'demo@example.com'");
    $stmt->execute();
    $userExists = (int)$stmt->fetchColumn();

    if ($userExists > 0) {
        echo "<p style='color: blue;'>ℹ Sample user already exists, skipping creation.</p>";
    } else {
        // Insert sample user
        echo "<p>Creating sample user...</p>";
        $sql = "INSERT INTO `users` (`username`, `email`, `password`, `full_name`) 
                VALUES ('demo_user', 'demo@example.com', 'password123', 'Demo User')";
        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Sample user created successfully!</p>";
    }

    echo "<p style='color: green; font-weight: bold;'>✓ Database setup completed successfully!</p>";
    echo "<p>You can now use the following credentials to test the login:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> demo_user</li>";
    echo "<li><strong>Email:</strong> demo@example.com</li>";
    echo "<li><strong>Password:</strong> password123</li>";
    echo "</ul>";
    echo "<p><a href='index.html' style='color: blue;'>Go to Login Page</a></p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";

    // Provide more specific troubleshooting advice based on the error
    if (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<p style='color: red;'>It looks like your MySQL username or password is incorrect. Please check your credentials.</p>";
    } else if (strpos($e->getMessage(), "Unknown host") !== false) {
        echo "<p style='color: red;'>Cannot connect to the database server. Please check if MySQL is running and the hostname is correct.</p>";
    } else if (strpos($e->getMessage(), "Base table or view already exists") !== false) {
        echo "<p style='color: orange;'>A table already exists. This might not be an issue if you're running the script again.</p>";
    }

    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ol>";
    echo "<li>Make sure MySQL server is running</li>";
    echo "<li>Verify your username and password are correct</li>";
    echo "<li>Check if you have permissions to create databases</li>";
    echo "<li>If using XAMPP/WAMP, make sure the service is started</li>";
    echo "<li>Try changing the database name if it's already in use</li>";
    echo "</ol>";
}

$conn = null;
echo "</div>";

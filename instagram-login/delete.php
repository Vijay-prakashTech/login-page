<?php
require_once "config.php"; // Ensure database connection

if (!isset($_GET['id'])) {
    die("Invalid request!");
}

$id = (int)$_GET['id'];

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

echo "<script>alert('User deleted successfully!'); window.location='index.html';</script>";

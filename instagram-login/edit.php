<?php
require_once "config.php"; // Ensure database connection

if (!isset($_GET['id'])) {
    die("Invalid request!");
}

$id = (int)$_GET['id']; // Get user ID

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found!");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];

    // Update user
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $id]);

    echo "<script>alert('User updated successfully!'); window.location='index.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit User</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control">
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="index.html" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
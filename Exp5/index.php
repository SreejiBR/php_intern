<?php
session_start();
require 'db.php';

$error = '';

// Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;

                // Redirect after successful login
                header("Location: dashboard.php");
                exit;
            } else {
                // Incorrect password, send error via GET parameter
                header("Location: index.php?error=Invalid+username+or+password");
                exit;
            }
        } else {
            // Invalid username, send error via GET parameter
            header("Location: index.php?error=Invalid+username+or+password");
            exit;
        }
    } else {
        // Missing fields, send error via GET parameter
        header("Location: index.php?error=Please+fill+in+all+fields");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles_main.css">
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>

    <!-- Display error message if set in URL -->
    <?php if (isset($_GET['error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>

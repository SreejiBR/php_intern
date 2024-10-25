<?php
require 'config.php';
require 'functions.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
// Fetch the user's data
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $password = $_POST['password']; // This will be used for updating the password if provided

    // Validate inputs
    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required.";
    } else {
        // Update user details
        $update_query = "UPDATE users SET first_name = ?, last_name = ?";
        $params = [$first_name, $last_name];

        // Update password if provided
        if (!empty($password)) {
            // Validate new password
            if (!isValidPassword($password)) {
                $error = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one digit, and one special character.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_query .= ", password = ?";
                $params[] = $hashed_password;
            }
        }

        $update_query .= " WHERE id = ?";
        $params[] = $_SESSION['user_id'];

        // Execute update only if there are no errors
        if (!isset($error)) {
            $stmt = $pdo->prepare($update_query);
            $stmt->execute($params);
            $success = "Profile updated successfully!";
        }
    }
}
// Password validation function
function isValidPassword($password) {
    // Ensure password meets the criteria
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Edit Profile</title>
    <style>
        .email-container {
            position: relative;
        }

        .lock-icon {
            position: absolute;
            right: 10px;
            top: 20%;
            color: #888; /* Color of the lock icon */
            pointer-events: none; /* Prevents clicking on the icon */
        }

        input[type="email"] {
            background-color: #f0f0f0;
            color: #333; /* Text color */
            border: 1px solid #ccc; /* Border color */
            cursor: not-allowed; /* Cursor to indicate it's read-only */
        }

        input[type="email"]:focus {
            outline: none; /* Remove outline on focus */
            border-color: #999; /* Change border color on focus */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Profile</h1>

    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>

    <form method="POST" action="">
        <label>First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        <label>Email:</label>
        <div class="email-container">
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            <span class="lock-icon">
                <i class="fas fa-lock"></i>
            </span>
        </div>
        <label>Password (leave blank to keep current):</label>
        <div class="password-container">
            <input type="password" name="password" id="password">
            <span class="toggle-password" onclick="togglePassword()">
                <i id="password-icon" class="fas fa-eye"></i>
            </span>
        </div>

        <button type="submit">Update Profile</button>
    </form>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>
<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
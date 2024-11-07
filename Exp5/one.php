<?php
require 'db.php';

$username = 'admin';
$password = password_hash('securepassword', PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

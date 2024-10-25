<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php_internship";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];

$sql_check = "SELECT * FROM registrations WHERE email = '$email'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    $error_message = "Email already exists. Please use a different email.";
    header("Location: index.php?error=" . urlencode($error_message));
    exit();
} else {
    $sql_insert = "INSERT INTO registrations (name, email) VALUES ('$name', '$email')";
    if ($conn->query($sql_insert) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php_internship";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql_delete = "DELETE FROM registrations WHERE id = $id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

$conn->close();
?>

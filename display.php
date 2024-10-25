<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php_internship";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM registrations ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td><button class='delete-btn' onclick='confirmDelete(" . $row['id'] . ")'>Delete</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No registered students found.";
}

$conn->close();
?>

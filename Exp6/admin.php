<?php
$mysqli = new mysqli("localhost", "root", "", "onlineexam");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM results ORDER BY submission_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <h2>Student Results</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Score</th>
                <th>Submission Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= $row['score'] ?></td>
                    <td><?= $row['submission_time'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
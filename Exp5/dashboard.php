<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

// Include necessary files
include 'db.php'; // Database connection

// Check for form submission and perform actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_class'])) {
        // Handle adding class
        $class_name = $_POST['class_name'];
        $description = $_POST['description'];

        if (!empty($class_name)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO classes (class_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $class_name, $description);
            $stmt->execute();

            // Redirect after successful insert to avoid resubmission
            header("Location: classes.php?success=Class+added+successfully");
            exit;
        }
    }
    
    // Add more actions here like update, delete, etc.
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles_main.css">
    <title>Dashboard</title>
</head>
<body>

<div class="container">
    <h2>Admin Dashboard</h2>

    <div class="nav-block">
        <div class="nav-item" onclick="location.href='teachers.php';">
            <h3>Teachers</h3>
            <p>Manage teachers, their subjects, and details.</p>
        </div>
        <div class="nav-item" onclick="location.href='classes.php';">
            <h3>Classes & Divisions</h3>
            <p>Add and manage classes and their divisions.</p>
        </div>
        <div class="nav-item" onclick="location.href='subjects.php';">
            <h3>Subjects</h3>
            <p>Assign subjects to classes and divisions.</p>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <div class="logout">
        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>
</div>

</body>
</html>

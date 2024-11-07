<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$classes = [];
$divisions = [];
$error = '';
$success = '';

// Fetch classes
$result = $conn->query("SELECT * FROM classes");
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $division_name = trim($_POST['division_name']);

    if ($class_id && $division_name) {
        $stmt = $conn->prepare("INSERT INTO divisions (class_id, division_name) VALUES (?, ?)");
        $stmt->bind_param("is", $class_id, $division_name);
        if ($stmt->execute()) {
            // Redirect after success
            header("Location: manage_divisions.php?success=Division+added+successfully");
            exit;
        } else {
            // Redirect with error message
            header("Location: manage_divisions.php?error=Error+adding+division");
            exit;
        }
    } else {
        // Redirect with error message
        header("Location: manage_divisions.php?error=Class+and+division+name+are+required");
        exit;
    }
}

// Fetch existing divisions
$result = $conn->query("SELECT d.*, c.class_name FROM divisions d JOIN classes c ON d.class_id = c.id");
while ($row = $result->fetch_assoc()) {
    $divisions[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Divisions</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-container">
        <h2>Manage Divisions</h2>

        <!-- Display error or success messages from GET parameters -->
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="class_id">Select Class</label>
            <select name="class_id" required>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="division_name">Division Name</label>
            <input type="text" name="division_name" required>

            <button type="submit">Add Division</button>
        </form>

        <h3>Existing Divisions</h3>
        <ul>
            <?php foreach ($divisions as $division): ?>
                <li><?php echo htmlspecialchars($division['class_name']); ?> - <?php echo htmlspecialchars($division['division_name']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>

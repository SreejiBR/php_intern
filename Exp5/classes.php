<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
$class_name = '';
$description = '';
$class_id = null;

// Handle adding, updating, and deleting classes or divisions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update class
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $class_name = trim($_POST['class_name']);
        $description = trim($_POST['description']);
        $class_id = $_POST['class_id'] ?? null;

        if (!empty($class_name)) {
            if ($action == 'add_class') {
                $stmt = $conn->prepare("INSERT INTO classes (class_name, description) VALUES (?, ?)");
                $stmt->bind_param("ss", $class_name, $description);
                $result = $stmt->execute();
                $success = $result ? "Class added successfully." : "Error adding class.";
            } elseif ($action == 'update_class' && $class_id) {
                $stmt = $conn->prepare("UPDATE classes SET class_name = ?, description = ? WHERE id = ?");
                $stmt->bind_param("ssi", $class_name, $description, $class_id);
                $result = $stmt->execute();
                $success = $result ? "Class updated successfully." : "Error updating class.";
            }
        } else {
            $error = "Class Name is required.";
        }
    }

    // Add division
    if (isset($_POST['add_division'])) {
        $class_id = $_POST['class_id'];
        $divisions = is_array($_POST['division_name']) ? $_POST['division_name'] : [$_POST['division_name']];

        foreach ($divisions as $division_name) {
            $division_name = trim($division_name);
            if (!empty($division_name)) {
                $stmt = $conn->prepare("SELECT 1 FROM divisions WHERE division_name = ? AND class_id = ?");
                $stmt->bind_param("si", $division_name, $class_id);
                $stmt->execute();
                $exists = $stmt->get_result()->fetch_assoc();

                if (!$exists) {
                    $stmt = $conn->prepare("INSERT INTO divisions (division_name, class_id) VALUES (?, ?)");
                    $stmt->bind_param("si", $division_name, $class_id);
                    $stmt->execute();
                    $success = "Division(s) added successfully.";
                } else {
                    $error = "Division '$division_name' already exists for this class.";
                }
            } else {
                $error = "Division Name cannot be empty.";
            }
        }
    }

    // Remove division
    if (isset($_POST['remove_division'])) {
        $division_id = $_POST['division_id'];
        $stmt = $conn->prepare("DELETE FROM divisions WHERE id = ?");
        $stmt->bind_param("i", $division_id);
        if ($stmt->execute()) {
            $success = "Division removed successfully.";
        } else {
            $error = "Error removing division.";
        }
    }

    // Redirect to the same page after handling form submission to prevent resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all classes for dropdown
$stmt = $conn->prepare("SELECT * FROM classes");
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch divisions for each class
$divisions = [];
foreach ($classes as $class) {
    $stmt = $conn->prepare("SELECT * FROM divisions WHERE class_id = ?");
    $stmt->bind_param("i", $class['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $divisions[$class['id']] = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class & Division Management</title>
    <link rel="stylesheet" href="styles_classes.css">
</head>
<body>
<div class="container">
    <h2>Manage Classes & Divisions</h2>

    <!-- Alert Messages for Success/Error -->
    <?php if ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Flex Container to Hold Both Class and Division Forms -->
    <div class="flex-container">
        <!-- Class Management Form -->
        <div class="card">
            <h3>Add / Update Class</h3>
            <form method="POST" action="classes.php">
                <label for="class_name">Class Name</label>
                <input type="text" id="class_name" name="class_name" value="<?php echo htmlspecialchars($class_name); ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="2" placeholder="Class Description"><?php echo htmlspecialchars($description); ?></textarea>

                <input type="hidden" name="action" value="<?php echo isset($_GET['edit']) ? 'update_class' : 'add_class'; ?>">
                <button type="submit" class="btn full-width"><?php echo isset($_GET['edit']) ? 'Update Class' : 'Add Class'; ?></button>
            </form>
        </div>

        <!-- Division Management Form -->
        <div class="card">
            <h3>Add Division</h3>
            <form method="POST" action="classes.php">
                <label for="class_id">Select Class</label>
                <select id="class_id" name="class_id" required>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="division_name">Division Name</label>
                <input type="text" id="division_name" name="division_name" required>

                <button type="submit" name="add_division" class="btn full-width">Add Division</button>
            </form>
        </div>
    </div>

    <!-- Section to Manage Existing Classes and Divisions -->
    <h3>Existing Classes & Divisions</h3>
    <div class="grid-container">
        <?php foreach ($classes as $class): ?>
            <div class="class-card">
                <h4><?php echo htmlspecialchars($class['class_name']); ?></h4>
                <p class="description"><?php echo htmlspecialchars($class['description']); ?></p>

                <h5>Divisions</h5>
                <ul class="division-list">
                    <?php if (!empty($divisions[$class['id']])): ?>
                        <?php foreach ($divisions[$class['id']] as $division): ?>
                            <li>
                                <span><?php echo htmlspecialchars($division['division_name']); ?></span>
                                <form method="POST" action="classes.php" class="inline-form">
                                    <input type="hidden" name="division_id" value="<?php echo $division['id']; ?>">
                                    <button type="submit" name="remove_division" class="btn-remove">Remove</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No divisions added yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>

<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
$teachers = [];
$subjects = [];
$classes = [];
$divisions = [];

// Fetch classes
$stmt = $conn->prepare("SELECT * FROM classes");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

// Fetch subjects
$stmt = $conn->prepare("SELECT * FROM subjects");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Fetch divisions
$stmt = $conn->prepare("SELECT * FROM divisions");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $divisions[] = $row;
}

// Fetch teachers
$stmt = $conn->prepare("SELECT t.*, s.subject_name, d.division_name, c.class_name FROM teachers t
    JOIN subjects s ON t.subject_id = s.id
    JOIN divisions d ON t.division_id = d.id
    JOIN classes c ON s.class_id = c.id");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $teachers[] = $row;
}

// Handle form submission for adding/updating teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $subject_id = trim($_POST['subject_id']);
    $division_id = trim($_POST['division_id']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $teacher_id = isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : null;

    if (!empty($name) && !empty($subject_id) && !empty($division_id) && !empty($username) && !empty($password)) {
        if ($teacher_id) { // Update
            $stmt = $conn->prepare("UPDATE teachers SET name = ?, subject_id = ?, division_id = ?, username = ?, password = ? WHERE id = ?");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssssi", $name, $subject_id, $division_id, $username, $hashed_password, $teacher_id);
            header("Refresh:0");
            $success = "Teacher updated successfully.";
        } else { // Add new teacher
            $stmt = $conn->prepare("INSERT INTO teachers (name, subject_id, division_id, username, password) VALUES (?, ?, ?, ?, ?)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sssss", $name, $subject_id, $division_id, $username, $hashed_password);
            $success = "Teacher added successfully.";
        }

        if ($stmt->execute()) {
            // Clear input after successful addition/update
            $name = $subject_id = $division_id = $username = $password = ''; 
        } else {
            $error = "Error adding/updating teacher.";
        }
    } else {
        $error = "All fields are required.";
    }
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success = "Teacher deleted successfully.";
    } else {
        $error = "Error deleting teacher.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management</title>
    <link rel="stylesheet" href="styles_main.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a, .action-buttons button {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-buttons a {
            background-color: #dc3545; /* Red for delete */
        }
        .action-buttons button {
            background-color: #28a745; /* Green for edit */
        }
    </style>
</head>
<body>
<div class="management-container">
    <h2>Teacher Management</h2>
    <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <form method="POST" action="teachers.php">
        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id ?? ''); ?>">

        <label for="class_id">Select Class</label>
        <select id="class_id" name="class_id" required onchange="updateDivisionsAndSubjects()">
            <option value="">-- Select Class --</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="division_id">Select Division</label>
        <select id="division_id" name="division_id" required onchange="updateSubjects()">
            <option value="">-- Select Division --</option>
            <?php foreach ($divisions as $division): ?>
                <option value="<?php echo $division['id']; ?>" class="division-option" data-class="<?php echo $division['class_id']; ?>"><?php echo htmlspecialchars($division['division_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="subject_id">Select Subject</label>
        <select id="subject_id" name="subject_id" required>
            <option value="">-- Select Subject --</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?php echo $subject['id']; ?>" class="subject-option" data-class="<?php echo $subject['class_id']; ?>" data-division="<?php echo $subject['division_id']; ?>"><?php echo htmlspecialchars($subject['subject_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Add/Update Teacher</button>
    </form>

    <h3>Existing Teachers</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Division</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['division_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                    <td class="action-buttons">
                        <a href="teachers.php?delete_id=<?php echo $teacher['id']; ?>" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a>
                        <button type="button" onclick="editTeacher(<?php echo $teacher['id']; ?>, '<?php echo htmlspecialchars($teacher['name']); ?>', <?php echo $teacher['subject_id']; ?>, <?php echo $teacher['division_id']; ?>, '<?php echo htmlspecialchars($teacher['username']); ?>')">Edit</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function updateDivisionsAndSubjects() {
    const classId = document.querySelector('[name="class_id"]').value;
    const divisions = document.querySelectorAll('.division-option');
    const subjects = document.querySelectorAll('.subject-option');

    // Show divisions based on class
    divisions.forEach(division => {
        if (division.dataset.class == classId) {
            division.style.display = 'block';
        } else {
            division.style.display = 'none';
        }
    });

    // Show subjects based on class and division
    subjects.forEach(subject => {
        const divisionId = document.querySelector('[name="division_id"]').value;
        if (subject.dataset.class == classId && subject.dataset.division == divisionId) {
            subject.style.display = 'block';
        } else {
            subject.style.display = 'none';
        }
    });
}

function updateSubjects() {
    updateDivisionsAndSubjects();
}

function editTeacher(id, name, subjectId, divisionId, username) {
    document.querySelector('[name="teacher_id"]').value = id;
    document.querySelector('[name="name"]').value = name;
    document.querySelector('[name="subject_id"]').value = subjectId;
    document.querySelector('[name="division_id"]').value = divisionId;
    document.querySelector('[name="username"]').value = username;
}
</script>
</body>
</html>

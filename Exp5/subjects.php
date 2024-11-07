<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
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

// Fetch divisions
$stmt = $conn->prepare("SELECT * FROM divisions");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $divisions[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = trim($_POST['class_id']);
    $division_id = trim($_POST['division_id']);
    $subject_name = trim($_POST['subject_name']);

    if (!empty($class_id) && !empty($division_id) && !empty($subject_name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (class_id, division_id, subject_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $class_id, $division_id, $subject_name);
        if ($stmt->execute()) {
            // Redirect after successful submission
            header("Location: subjects.php?success=Subject+added+successfully");
            exit;
        } else {
            // Redirect with error message
            header("Location: subjects.php?error=Error+adding+subject");
            exit;
        }
    } else {
        // Redirect with error message
        header("Location: subjects.php?error=All+fields+are+required");
        exit;
    }
}

// Fetch subjects
$stmt = $conn->prepare("SELECT s.*, c.class_name, d.division_name FROM subjects s
    JOIN classes c ON s.class_id = c.id
    JOIN divisions d ON s.division_id = d.id");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject Management</title>
    <link rel="stylesheet" href="styles_subjects.css">
    <script>
        // Function to update divisions based on the selected class
        function updateDivisions() {
            const classId = document.getElementById('class_id').value;
            const divisionSelect = document.getElementById('division_id');
            divisionSelect.innerHTML = '<option value="">-- Select Division --</option>';

            if (classId) {
                const divisions = <?php echo json_encode($divisions); ?>;
                const filteredDivisions = divisions.filter(function(division) {
                    return division.class_id == classId;
                });

                filteredDivisions.forEach(function(division) {
                    const option = document.createElement('option');
                    option.value = division.id;
                    option.textContent = division.division_name;
                    divisionSelect.appendChild(option);
                });
            }
        }
    </script>
</head>
<body>
<div class="management-container">
    <h2>Subject Management</h2>

    <!-- Display error or success messages from GET parameters -->
    <?php if (isset($_GET['error'])): ?>
        <div class="notification error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php elseif (isset($_GET['success'])): ?>
        <div class="notification success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form method="POST" action="subjects.php" class="form-container">
        <div class="form-group">
            <label for="class_id">Select Class</label>
            <select id="class_id" name="class_id" required onchange="updateDivisions()">
                <option value="">-- Select Class --</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="division_id">Select Division</label>
            <select id="division_id" name="division_id" required>
                <option value="">-- Select Division --</option>
            </select>
        </div>

        <div class="form-group">
            <label for="subject_name">Subject Name</label>
            <input type="text" id="subject_name" name="subject_name" value="<?php echo htmlspecialchars($subject_name ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn submit-btn">Add Subject</button>
    </form>

    <h3>Existing Subjects</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Class</th>
                    <th>Division</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($subject['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($subject['division_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

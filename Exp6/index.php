<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "onlineexam");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$questions = [
    ["question" => "What is 10*5?", "options" => ["40", "50", "15", "60"], "correct" => 1],
    ["question" => "What is the capital of India?", "options" => ["New York", "Paris", "New Delhi", "Rome"], "correct" => 2],
    ["question" => "Pick odd one among : ", "options" => ["C++", "Python", "Pascal", "Swift"], "correct" => 1],
    ["question" => "Which is not a Php Super Global variable :", "options" => ["\$_REQUEST", "\$_POST", "\$_FILES", "\$_DATA"], "correct" => 3],
    ["question" => "What is the correct way to end a PHP statement?", "options" => [";", ":", "}", "End of Line"], "correct" => 0],
    ["question" => "How can you create a variable in PHP?", "options" => ["var variableName;", "\$variableName;", "variable variableName;", "create variableName;"], "correct" => 1],
    ["question" => "What is the default file extension for PHP scripts?", "options" => [".php", ".html", ".js", ".ph"], "correct" => 0],
    ["question" => "Which of these functions is used to start a session in PHP?", "options" => ["session_start()", "session_begin()", "start_session()", "open_session()"], "correct" => 0],
    ["question" => "What is the correct syntax to add a comment in PHP?", "options" => ["// This is a comment", "\/* This is a comment */", "# This is a comment", "All of the above"], "correct" => 3],
    ["question" => "What is the full form of HTML?", "options" => ["Hypertext Markup Language", "Hyperlink and Text Markup Language", "Home Tool Markup Language", "Hyper Transfer Markup Language"], "correct" => 0],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $student_name = $_POST['student_name'];
    $user_answers = [];

    foreach ($questions as $index => $question) {
        if (isset($_POST['question_' . $index]) && $_POST['question_' . $index] == $question['correct']) {
            $score++;
        }
        // Store the USER'S ANS for later display
        $user_answers[$index] = isset($_POST['question_' . $index]) ? $_POST['question_' . $index] : null;
    }

    $stmt = $mysqli->prepare("INSERT INTO results (student_name, score) VALUES (?, ?)");
    $stmt->bind_param("si", $student_name, $score);
    $stmt->execute();
    $stmt->close();

    $_SESSION['score'] = $score;
    $_SESSION['user_answers'] = $user_answers;  // Store ANSWERS in session
    $_SESSION['questions'] = $questions;  // Store questions for result page
    header('Location: result.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Exam</title>
</head>
<body>
    <h1>Online Exam</h1>
    <form method="POST" action="">
        <label for="student_name">Enter your name:</label>
        <input type="text" id="student_name" name="student_name" required><br><br>
        
        <?php foreach ($questions as $index => $question): ?>
            <fieldset>
                <legend>Question <?= $index + 1 ?>: <?= htmlspecialchars($question['question']) ?></legend>
                <?php foreach ($question['options'] as $optionIndex => $option): ?>
                    <label>
                        <input type="radio" name="question_<?= $index ?>" value="<?= $optionIndex ?>"
                            <?= (isset($_POST['question_' . $index]) && $_POST['question_' . $index] == $optionIndex) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($option) ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?><br><br>

        <div style="text-align: center;">
            <button type="submit">Submit</button>
            <button type="reset">Clear</button>
        </div>
    </form>
</body>
</html>

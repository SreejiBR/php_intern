<?php
session_start();

// Get the stored session data
$score = isset($_SESSION['score']) ? $_SESSION['score'] : 0;// CONDITIONAL STATEMENTS: CONDITION? TRUE: ELSE;
$user_answers = isset($_SESSION['user_answers']) ? $_SESSION['user_answers'] : [];
$questions = isset($_SESSION['questions']) ? $_SESSION['questions'] : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Results</title>
    <style>
        .correct { color: green; }
        .incorrect { color: red; }
    </style>
</head>
<body>
    <h1>Your Results</h1>
    <p>You scored <?= $score ?> out of <?= count($questions) ?>.</p>
    <p><a href="onlineexam.php">Take the test again</a></p>
    
    <?php foreach ($questions as $index => $question): ?>
        <fieldset>
            <legend>Question <?= $index + 1 ?>: <?= htmlspecialchars($question['question']) ?></legend>
            <?php foreach ($question['options'] as $optionIndex => $option): ?>
                <label>
                    <!-- Display correct answer in green if selected, incorrect in red -->
                    <?php
                    $isSelected = (isset($user_answers[$index]) && $user_answers[$index] == $optionIndex);
                    $isCorrect = ($optionIndex == $question['correct']);
                    if ($isSelected && $isCorrect) {
                        echo "<span class='correct'>Correct: " . htmlspecialchars($option) . "</span><br>";
                    } elseif ($isSelected && !$isCorrect) {
                        echo "<span class='incorrect'>Your Answer: " . htmlspecialchars($option) . "</span><br>";
                    } elseif ($isCorrect) {
                        echo "<span class='correct'>Correct Answer: " . htmlspecialchars($option) . "</span><br>";
                    }
                    ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>
</body>
</html>

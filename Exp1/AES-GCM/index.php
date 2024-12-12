<?php
// Decrypt files and execute scheduled task
if (isset($_POST['decrypt'])) {
    $key = $_POST['key'];

    // Validate the key length
    if (strlen($key) !== 32) {
        echo "<div class='error'>Key must be 32 characters long (256 bits).</div>";
        exit;
    }

    // Files to decrypt
    $inputFiles = ['1.garuda', '2.garuda', '3.garuda', '4.garuda'];
    $outputFiles = ['home.php', 'register.php', 'display.php', 'delete.php'];

    foreach ($inputFiles as $index => $inputFile) {
        // Read encrypted data
        $data = file_get_contents($inputFile);
        if ($data === false) {
            echo "<div class='error'>Error reading $inputFile.</div>";
            exit;
        }

        // Extract IV, tag, and ciphertext
        $iv = substr($data, 0, 12);
        $tag = substr($data, 12, 16);
        $ciphertext = substr($data, 28);

        // Decrypt data
        $decryptedData = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($decryptedData === false) {
            echo "<div class='error'>Decryption failed for $inputFile!</div>";
            exit;
        }

        // Write decrypted data to output file
        if (file_put_contents($outputFiles[$index], $decryptedData) === false) {
            echo "<div class='error'>Error writing to {$outputFiles[$index]}.</div>";
            exit;
        }
    }
    echo "<div class='success'>Files decrypted successfully.</div>";

    // Get current time and add 30 minutes to it for the scheduled task
    $currentTime = new DateTime();
    $currentTime->modify('+30 minutes');
    // Command to delete the files using schtasks
    $cmd = 'schtasks /create /tn "GarudaDeleteTask" /tr "cmd /c del \"C:\\wamp64\\www\\garuda\\home.php\" && del \"C:\\wamp64\\www\\garuda\\register.php\" && del \"C:\\wamp64\\www\\garuda\\display.php\" && del \"C:\\wamp64\\www\\garuda\\delete.php\"" /sc once /st ' . $scheduledTime . ' /f /ru "SYSTEM"';

    // Execute the command and capture the output
    $output = [];
    $returnCode = -1;
    exec($cmd . ' 2>&1', $output, $returnCode);

    // Display the command output
    echo "<pre>Command Output:\n" . implode("\n", $output) . "\nReturn Code: $returnCode</pre>";

    // Handle execution result
    if ($returnCode !== 0) {
        echo "<div class='error'>Failed to create scheduled task (Error code: $returnCode). Output: <br>" . nl2br(implode("\n", $output)) . "</div>";
        exit;
    }

    echo "<div class='success'>Scheduled task created successfully to delete files.</div>";

    // Optionally redirect or perform further actions
    header("Location: home.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decrypt Files</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: normal;
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .toggle-btn {
            background: transparent;
            color: #007bff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            margin-top: -10px;
            padding: 0;
            text-align: left;
        }
        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
        }
        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('key');
            const toggleBtn = document.getElementById('togglePassword');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.innerText = 'Hide';
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerText = 'Show';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Decrypt File</h2>
        <form method="POST">
            <label for="key">Enter key</label>
            <input type="password" id="key" name="key" maxlength="32" required>
            <button type="button" class="toggle-btn" id="togglePassword" onclick="togglePasswordVisibility()">
                Show
            </button>
            <button type="submit" name="decrypt">Decrypt Files</button>
        </form>
    </div>
</body>
</html>
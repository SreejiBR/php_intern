<?php
if (isset($_POST['encrypt'])) {
    $key = $_POST['key'];
    if (strlen($key) !== 32) {
        echo "<div class='error'>Key must be 32 characters long (256 bits).</div>";
        exit;
    }
    $inputFiles = ['home.php', 'config.php', 'dashboard.php', 'functions.php', 'logout.php', 'profile.php', 'signup.php', 'styles.css'];
    $outputFiles = ['1.garuda', '2.garuda', '3.garuda', '4.garuda', '5.garuda', '5.garuda', '7.garuda', '8.garuda'];
    foreach ($inputFiles as $index => $inputFile) {
        $data = file_get_contents($inputFile);
        if ($data === false) {
            echo "<div class='error'>Error reading $inputFile.</div>";
            exit;
        }
        $iv = random_bytes(12); // AES-GCM requires a 96-bit IV
        $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            echo "<div class='error'>Encryption failed!</div>";
            exit;
        }
        $encryptedData = $iv . $tag . $ciphertext;
        file_put_contents($outputFiles[$index], $encryptedData);
    }
    echo "<div class='success'>Files encrypted successfully.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt Files</title>
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
        <h2>Encrypt Files</h2>
        <form method="POST">
            <label for="key">Enter key:</label>
            <input type="password" id="key" name="key" maxlength="32" required>
            <button type="button" class="toggle-btn" id="togglePassword" onclick="togglePasswordVisibility()">
                Show
            </button>
            <button type="submit" name="encrypt">Encrypt Files</button>
        </form>
    </div>
</body>
</html>

=<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #35424a;
            color: #ffffff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #e8491d 3px solid;
        }
        header h1 {
            text-align: center;
        }
        form {
            background: #ffffff;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            display: block;
            width: 100%;
            background: #35424a;
            color: #ffffff;
            border: 0;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #35424a;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #e8491d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
 <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this entry ? ID : "+id)) {
                window.location.href = "delete.php?id=" + id;
            }
        }

        function showAlert(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Student Registration System - Hazalto</h1>
        </div>
    </header>

    <div class="container">
        <?php
        if (isset($_GET['error'])) {
            echo "<script>showAlert('" . $_GET['error'] . "');</script>";
        }
        ?>
        <form method="post" action="register.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Register">
        </form>

        <h2>Registered Students</h2>
        <?php include 'display.php'; ?>
    </div>
</body>
</html>
<?php
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "review";
$conn = mysqli_connect($server, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if($user === "admin" && $pass ==="admin@123"){
        $_SESSION['loggedin'] = true;
    }else{
        echo "Invalid credentials!";
    }
}
if(!isset($_SESSION['loggedin'])){
?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
    </head>
    <body>
    <div id="time"></div>        <form method="post" action = "">
            Username : <input type="text" name="username"><br><br>
            Password : <input type="password" name="password"><br><br>
            <input type="submit" name="login" value="Login">
        </form>
    </body>
    </html>
<?php
    exit;
}

//ADd Student
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query("INSERT INTO students (name, email) VALUES ('$name', '$email')");
}
//Del
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id = $id");
}
// Upd
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_student'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $conn->query("UPDATE students SET name='$name', email='$email' WHERE id=$id");
}

$students = $conn->query("SELECT * FROM students");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
</head>
<body>
    <h2>Register Student</h2>
    <form method="post" action="">
        Name: <input type="text" name="name" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        <input type="submit" name="add_student" value="Add Student">
    </form>

    <h3>Student List</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $students->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td>
                <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if (isset($_GET['edit'])):
        $id = $_GET['edit'];
        $result = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
    ?>
    <h3>Edit Student</h3>
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        Name: <input type="text" name="name" value="<?php echo $result['name']; ?>" required><br><br>
        Email: <input type="email" name="email" value="<?php echo $result['email']; ?>" required><br><br>
        <input type="submit" name="edit_student" value="Update Student">
    </form>
    <?php endif; ?>

    <p><a href="?logout=true">Logout</a></p>

    <?php
    if (isset($_GET['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    $conn->close();
    ?>

</body>
</html>

<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$id = intval($_GET['id']); // sanitize input
$result = mysqli_query($conn, "SELECT * FROM users WHERE id='$id' AND role='student'");
$student = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    mysqli_query($conn, "UPDATE users SET full_name='$name', email='$email' WHERE id='$id'");
    header("Location: manage_students.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
</head>
<body>
<h2>Edit Student</h2>
<form method="POST">
    <input type="text" name="name" value="<?php echo $student['full_name']; ?>" required><br><br>
    <input type="email" name="email" value="<?php echo $student['email']; ?>" required><br><br>
    <button name="update">Update</button>
</form>
<a href="manage_students.php">Back</a>
</body>
</html>

<?php
session_start();
include("../config/db.php");

// Only students can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student info
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id'"));

// Handle form submission
$message = "";
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Update password only if provided
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET full_name='$name', email='$email', password='$password' WHERE id='$student_id'");
    } else {
        mysqli_query($conn, "UPDATE users SET full_name='$name', email='$email' WHERE id='$student_id'");
    }
    
    $message = "Profile updated successfully!";
    // Refresh student data
    $student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id'"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | EduFund</title>
<style>
body {font-family: Arial; margin:0; background-color:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background-color:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background-color:#1f6391;}
header {background-color:#2980b9; color:white; position: fixed; top:0; left:220px; right:0; height:60px; display:flex; justify-content:space-between; align-items:center; padding:0 20px;}
header h1 {margin:0; font-size:22px;}
header a {color:white; text-decoration:none; background-color:#e67e22; padding:8px 15px; border-radius:5px;}
header a:hover {background-color:#cf711f;}
.main {margin-left:220px; padding:20px; margin-top:60px; max-width:600px;}
h2 {color:#2980b9;}
input, button {width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc;}
button {background-color:#2980b9; color:white; border:none; cursor:pointer;}
button:hover {background-color:#1f6391;}
.message {color:green; font-weight:bold;}
</style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align:center; margin-bottom:20px;">Student Menu</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Available Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility Criteria</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<header>
    <h1>My Profile</h1>
</header>

<div class="main">
    <h2>Update Your Profile</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo $student['full_name']; ?>" required>
        
        <label>Email</label>
        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
        
        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password">
        
        <button type="submit" name="update">Update Profile</button>
    </form>
</div>

</body>
</html>

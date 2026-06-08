<?php
session_start();
include("../config/db.php");

// Only students can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch all scholarships
$scholarships = mysqli_query($conn, "SELECT * FROM scholarships");

// Fetch IDs of scholarships the student has already applied to
$applied = mysqli_query($conn, "SELECT scholarship_id FROM applications WHERE user_id='$student_id'");
$applied_ids = [];
while($row = mysqli_fetch_assoc($applied)){
    $applied_ids[] = $row['scholarship_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Scholarships | EduFund</title>
<style>
body {font-family: Arial; margin:0; background-color:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background-color:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background-color:#1f6391;}
header {background-color:#2980b9; color:white; position: fixed; top:0; left:220px; right:0; height:60px; display:flex; justify-content:space-between; align-items:center; padding:0 20px;}
header h1 {margin:0; font-size:22px;}
header a {color:white; text-decoration:none; background-color:#e67e22; padding:8px 15px; border-radius:5px;}
header a:hover {background-color:#cf711f;}
.main {margin-left:220px; padding:20px; margin-top:60px;}
h2 {color:#2980b9;}
table {width:100%; border-collapse: collapse; margin-top:20px;}
th, td {border:1px solid #ccc; padding:10px; text-align:left;}
th {background-color:#2980b9; color:white;}
.action-btn {padding:5px 10px; border:none; border-radius:3px; cursor:pointer;}
.apply {background-color:#27ae60; color:white;}
.apply:hover {background-color:#1e8449;}
.applied {background-color:#95a5a6; color:white; cursor:default;}
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
    <h1>Available Scholarships</h1>
</header>

<div class="main">
    <h2>Scholarships</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Eligibility</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>
        <?php while($scholarship = mysqli_fetch_assoc($scholarships)){ ?>
        <tr>
            <td><?php echo $scholarship['id']; ?></td>
            <td><?php echo $scholarship['title']; ?></td>
            <td><?php echo $scholarship['description']; ?></td>
            <td><?php echo $scholarship['eligibility']; ?></td>
            <td><?php echo $scholarship['deadline']; ?></td>
            <td>
                <?php if(in_array($scholarship['id'], $applied_ids)){ ?>
                    <button class="action-btn applied" disabled>Applied</button>
                <?php } else { ?>
                    <a href="apply.php?scholarship_id=<?php echo $scholarship['id']; ?>" class="action-btn apply">Apply</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

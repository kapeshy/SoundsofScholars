<?php
session_start();
include("../config/db.php");

// Only students can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch all applications of this student
$applications = mysqli_query($conn, "
    SELECT a.id, s.title, a.status, a.applied_at
    FROM applications a
    JOIN scholarships s ON a.scholarship_id = s.id
    WHERE a.user_id='$student_id'
    ORDER BY a.applied_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Applications | EduFund</title>
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
.status {padding:5px 10px; border-radius:3px; color:white;}
.pending {background-color:#f39c12;}
.approved {background-color:#27ae60;}
.rejected {background-color:#e74c3c;}
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
    <h1>My Applications</h1>
</header>

<div class="main">
    <h2>Submitted Applications</h2>
    <table>
        <tr>
            <th>Scholarship</th>
            <th>Status</th>
            <th>Date Applied</th>
        </tr>
        <?php if(mysqli_num_rows($applications) > 0): ?>
            <?php while($app = mysqli_fetch_assoc($applications)): ?>
            <tr>
                <td><?php echo $app['title']; ?></td>
                <td class="status <?php echo strtolower($app['status']); ?>">
                    <?php echo ucfirst($app['status']); ?>
                </td>
                <td><?php echo date("d M Y", strtotime($app['applied_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">You have not applied to any scholarships yet.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>

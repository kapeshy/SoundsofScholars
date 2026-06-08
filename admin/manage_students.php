<?php
session_start();
include("../config/db.php");

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Handle student deletion
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']); 
    mysqli_query($conn, "DELETE FROM users WHERE id='$delete_id' AND role='student'");
    header("Location: manage_students.php");
    exit();
}

// Handle application status update
if(isset($_GET['app_id']) && isset($_GET['status'])){
    $app_id = intval($_GET['app_id']);
    $status = $_GET['status'];
    $allowed_status = ['Pending','Approved','Rejected'];
    if(in_array($status, $allowed_status)){
        mysqli_query($conn, "UPDATE applications SET status='$status' WHERE id='$app_id'");
        header("Location: manage_students.php");
        exit();
    }
}

// Fetch all students with their applications
$students = mysqli_query($conn, "SELECT * FROM users WHERE role='student'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Students | Admin</title>
<style>
body {font-family: Arial; margin:0; background:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background:#1f6391;}
header {background:#2980b9; color:white; position:fixed; top:0; left:220px; right:0; height:60px; display:flex; justify-content:space-between; align-items:center; padding:0 20px;}
header h1 {margin:0; font-size:22px;}
.main {margin-left:220px; padding:20px; margin-top:60px;}
h2 {color:#2980b9; margin-bottom:20px;}
table {width:100%; border-collapse: collapse; margin-bottom:30px; background:white; border-radius:10px; overflow:hidden;}
th, td {border-bottom:1px solid #ddd; padding:12px; text-align:left;}
th {background:#2980b9; color:white;}
tr:nth-child(even) {background:#f9f9f9;}
tr:hover {background:#f1f1f1;}
.action-btn {padding:5px 10px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:12px;}
.edit {background:#3498db; color:white;}
.delete {background:#e74c3c; color:white;}
.approve {background:#27ae60; color:white;}
.reject {background:#e74c3c; color:white;}
.pending {background:#f39c12; color:white;}
.status-badge {padding:5px 8px; border-radius:5px; color:black; font-weight:bold; font-size:12px; display:inline-block;}
</style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align:center; margin-bottom:20px;">Admin Menu</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_students.php">Manage Students</a>
    <a href="manage_donors.php">Manage Donors</a>
    <a href="manage_scholarships.php">Manage Scholarships</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<header>
    <h1>Manage Students & Applications</h1>
</header>

<div class="main">
    <h2>All Students & Applications</h2>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th>Scholarship</th>
            <th>Status</th>
            <th>Documents</th>
            <th>Actions</th>
        </tr>

        <?php while($student = mysqli_fetch_assoc($students)):
            $apps = mysqli_query($conn, "SELECT a.id AS app_id, s.title AS scholarship, a.status, a.documents 
                                         FROM applications a 
                                         JOIN scholarships s ON a.scholarship_id = s.id
                                         WHERE a.user_id='{$student['id']}'");
            if(mysqli_num_rows($apps) > 0):
                while($app = mysqli_fetch_assoc($apps)):
        ?>
        <tr>
            <td><?php echo $student['full_name']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td><?php echo $app['scholarship']; ?></td>
            <td>
                <span class="status-badge <?php 
                    echo strtolower($app['status']); ?>">
                    <?php echo ucfirst($app['status']); ?>
                </span>
            </td>
            <td>
                <?php if($app['documents']): ?>
                    <a href="../student/<?php echo $app['documents']; ?>" target="_blank">View/Download</a>
                <?php else: ?>
                    No Document
                <?php endif; ?>
            </td>
            <td>
                <a href="manage_students.php?app_id=<?php echo $app['app_id']; ?>&status=Approved" class="action-btn approve">Approve</a>
                <a href="manage_students.php?app_id=<?php echo $app['app_id']; ?>&status=Rejected" class="action-btn reject">Reject</a>
                <a href="manage_students.php?app_id=<?php echo $app['app_id']; ?>&status=Pending" class="action-btn pending">Pending</a>
                <a href="manage_students.php?delete_id=<?php echo $student['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this student and all their applications?')">Delete</a>
            </td>
        </tr>
        <?php 
                endwhile;
            else: 
        ?>
        <tr>
            <td><?php echo $student['full_name']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td colspan="4" style="text-align:center;">No applications</td>
        </tr>
        <?php endif; endwhile; ?>
    </table>
</div>

</body>
</html>

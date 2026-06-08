<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all scholarships
$result = mysqli_query($conn, "SELECT * FROM scholarships");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Scholarships |SoundsOfScholars Admin</title>
<style>
/* same styles as above */
body {font-family: Arial; margin:0; background-color:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background-color:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background-color:#1f6391;}
header {background-color:#2980b9; color:white; position: fixed; top:0; left:220px; right:0; height:60px; display:flex; justify-content:space-between; align-items:center; padding:0 20px;}
header h1 {margin:0; font-size:22px;}
header a {color:white; text-decoration:none; background-color:#e67e22; padding:8px 15px; border-radius:5px;}
header a:hover {background-color:#cf711f;}
.main {margin-left:220px; padding:20px; margin-top:60px;}
table {width:100%; border-collapse: collapse; margin-top:20px;}
th, td {border:1px solid #ccc; padding:10px; text-align:left;}
th {background-color:#2980b9; color:white;}
.action-btn {padding:5px 10px; border:none; border-radius:3px; cursor:pointer;}
.edit {background-color:#3498db; color:white;}
.delete {background-color:#e74c3c; color:white;}
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
    <h1>Manage Scholarships</h1>
</header>

<div class="main">
    <h2>All Scholarships</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Scholarship Name</th>
            <th>Details</th>
            <th>Actions</th>
        </tr>
        <?php while($scholarship = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?php echo $scholarship['id']; ?></td>
            <td><?php echo $scholarship['title']; ?></td>
            <td><?php echo $scholarship['description']; ?></td>
            <td>
                <a href="edit_student.php?id=<?php echo $scholarship['id']; ?>" class="action-btn edit">Edit</a>
                <a href="manage_scholarships.php?delete_id=<?php echo $scholarship['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this scholarship?')">Delete</a>

            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>

<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Handle donor deletion (with confirmation)
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    // Optionally: delete their scholarships or restrict deletion if they have existing scholarships
    mysqli_query($conn, "DELETE FROM users WHERE id='$delete_id' AND role='donor'");
    header("Location: manage_donors.php");
    exit();
}

// Fetch all donors
$donors = mysqli_query($conn, "SELECT * FROM users WHERE role='donor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Donors | Admin</title>
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
.action-btn {padding:5px 10px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:12px; margin-right:4px;}
.edit {background:#3498db; color:white;}
.delete {background:#e74c3c; color:white;}
.view {background:#27ae60; color:white;}
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
    <h1>Manage Donors</h1>
</header>

<div class="main">
    <h2>All Donors</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Scholarships Created</th>
            <th>Actions</th>
        </tr>
        <?php while($donor = mysqli_fetch_assoc($donors)):
            $scholarships_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='{$donor['id']}'"))['total'];
        ?>
        <tr>
            <td><?php echo $donor['id']; ?></td>
            <td><?php echo $donor['full_name']; ?></td>
            <td><?php echo $donor['email']; ?></td>
            <td><?php echo $scholarships_count; ?></td>
            <td>
                <a href="manage_donors.php?delete_id=<?php echo $donor['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this donor and all their scholarships?')">Delete</a>
                <a href="view_scholarships.php?donor_id=<?php echo $donor['id']; ?>" class="action-btn view">View Scholarships</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

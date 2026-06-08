<?php
session_start();
include("../config/db.php");

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Get donor ID
if(!isset($_GET['donor_id'])){
    header("Location: manage_donors.php");
    exit();
}

$donor_id = intval($_GET['donor_id']);

// Handle scholarship status update
if(isset($_GET['sch_id']) && isset($_GET['status'])){
    $sch_id = intval($_GET['sch_id']);
    $status = $_GET['status'];
    $allowed_status = ['Pending','Approved','Rejected'];

    if(in_array($status, $allowed_status)){

        // Fetch current scholarship info
        $sch = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM scholarships WHERE id='$sch_id'"));

        // Update only if status changed
        if($sch['status'] != $status){
            mysqli_query($conn, "UPDATE scholarships SET status='$status' WHERE id='$sch_id'");

            // Insert notification
            $message = "Your scholarship '{$sch['title']}' status has been updated to '{$status}'!";
         mysqli_real_escape_string($conn, "INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ('{$sch['donor_id']}', '$message', 0, NOW())");
        }

        header("Location: view_scholarships.php?donor_id=$donor_id");
        exit();
    }
}



// Handle scholarship deletion
if(isset($_GET['delete_sch'])){
    $sch_id = intval($_GET['delete_sch']);
    mysqli_query($conn, "DELETE FROM scholarships WHERE id='$sch_id'");
    header("Location: view_scholarships.php?donor_id=$donor_id");
    exit();
}

// Fetch donor info
$donor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$donor_id'"));

// Fetch donor's scholarships
$scholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE donor_id='$donor_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Donor Scholarships | Admin</title>
<style>
body {font-family: Arial; margin:0; background:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background:#1f6391;}
header {background:#2980b9; color:white; position:fixed; top:0; left:220px; right:0; height:60px; display:flex; align-items:center; padding:0 20px;}
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
.approve {background:#27ae60; color:white;}
.reject {background:#e74c3c; color:white;}
.pending {background:#f39c12; color:white;}
.status-badge {padding:5px 8px; border-radius:5px; font-weight:bold; font-size:14px; display:inline-block;}
/* Actions column buttons */
td.actions {
    display: flex;
    gap: 6px;           /* space between buttons */
    flex-wrap: wrap;    /* wrap if too many buttons */
}

.action-btn {
    padding: 6px 10px;  /* slightly smaller */
    font-size: 13px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-weight: bold;
}

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
    <h1>Scholarships by <?php echo $donor['full_name']; ?></h1>
</header>

<div class="main">
    <h2>All Scholarships Created by <?php echo $donor['full_name']; ?></h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Eligibility</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while($sch = mysqli_fetch_assoc($scholarships)): ?>
        <tr>
            <td><?php echo $sch['id']; ?></td>
            <td><?php echo $sch['title']; ?></td>
            <td><?php echo $sch['description']; ?></td>
            <td><?php echo $sch['eligibility']; ?></td>
            <td><?php echo $sch['deadline']; ?></td>
            <td>
                <span class="status-badge <?php echo strtolower($sch['status']); ?>">
                    <?php echo ucfirst($sch['status']); ?>
                </span>
            </td>
            <td class="actions"> 
                <a href="view_scholarships.php?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Approved" class="action-btn approve">Approve</a>
                <a href="view_scholarships.php?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Rejected" class="action-btn reject">Reject</a>
                <a href="view_scholarships.php?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Pending" class="action-btn pending">Pending</a>
                <a href="view_scholarships.php?donor_id=<?php echo $donor_id; ?>&delete_sch=<?php echo $sch['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this scholarship?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <a href="manage_donors.php" class="action-btn edit">Back to Donors</a>
</div>

</body>
</html>

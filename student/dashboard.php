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

// Fetch all scholarships
$scholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE status='Approved' AND deadline >= CURDATE()");

// Fetch student's applications
$applications = mysqli_query($conn, "
    SELECT a.id, s.title, a.status, a.documents
    FROM applications a
    JOIN scholarships s ON a.scholarship_id = s.id
    WHERE a.user_id='$student_id'
");


// ===== DASHBOARD STATS =====
$totalScholarships = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships")
)['total'];

$totalApplications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id'")
)['total'];

$approvedApplications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Approved'")
)['total'];

$rejectedApplications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Rejected'")
)['total'];

$pendingApplications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Pending'")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard | EduFund</title>
<style>
body {font-family: Arial; margin:0; background-color:#f4f6f8;}
.sidebar {position: fixed; left:0; top:0; height:100%; width:220px; background-color:#2980b9; color:white; padding-top:60px;}
.sidebar a {display:block; color:white; padding:15px 20px; text-decoration:none; font-weight:bold;}
.sidebar a:hover {background-color:#1f6391;}
header {background-color:#2980b9; color:white; position: fixed; top:0; left:220px; right:0; height:60px; display:flex; align-items:center; padding:0 20px;}
header h1 {margin:0; font-size:22px;}
.main {margin-left:220px; padding:20px; margin-top:60px;}
h2 {color:#2980b9;}

/* ===== DASHBOARD CARDS ===== */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card h3 {
    margin: 0;
    color: #555;
    font-size: 16px;
}
.card p {
    font-size: 28px;
    font-weight: bold;
    margin-top: 10px;
}
.blue {border-left: 5px solid #2980b9;}
.green {border-left: 5px solid #27ae60;}
.orange {border-left: 5px solid #f39c12;}
.red {border-left: 5px solid #e74c3c;}

/* ===== TABLES ===== */
table {width:100%; border-collapse: collapse; margin-top:20px;}
th, td {border:1px solid #ccc; padding:10px; text-align:left;}
th {background-color:#2980b9; color:white;}
.action-btn {padding:5px 10px; border-radius:3px; text-decoration:none;}
.apply {background-color:#27ae60; color:white;}
.apply:hover {background-color:#1e8449;}
.status {padding:5px 10px; border-radius:3px; color:white;}
.pending {background-color:#f39c12;}
.approved {background-color:#27ae60;}
.rejected {background-color:#e74c3c;}
</style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align:center;">Student Menu</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Available Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility Criteria</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<header>
    <h1>Welcome, <?php echo $student['full_name']; ?></h1>
</header>

<div class="main">

    <!-- ===== DASHBOARD STATS ===== -->
    <h2>Dashboard Overview</h2>
    <div class="cards">
        <div class="card blue">
            <h3>Total Scholarships</h3>
            <p><?php echo $totalScholarships; ?></p>
        </div>
        <div class="card orange">
            <h3>Applications Submitted</h3>
            <p><?php echo $totalApplications; ?></p>
        </div>
        <div class="card green">
            <h3>Approved</h3>
            <p><?php echo $approvedApplications; ?></p>
        </div>
        <div class="card red">
            <h3>Rejected</h3>
            <p><?php echo $rejectedApplications; ?></p>
        </div>
        <div class="card blue">
            <h3>Pending</h3>
            <p><?php echo $pendingApplications; ?></p>
        </div>
    </div>

    <!-- ===== AVAILABLE SCHOLARSHIPS ===== -->
    <h2>Available Scholarships</h2>
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
                <a href="apply.php?scholarship_id=<?php echo $scholarship['id']; ?>" class="action-btn apply">Apply</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- ===== MY APPLICATIONS ===== -->
    <h2>My Applications</h2>
<table>
    <tr>
        <th>Scholarship</th>
        <th>Status</th>
        <th>Documents</th>
    </tr>
    <?php while($app = mysqli_fetch_assoc($applications)){ ?>
    <tr>
        <td><?php echo $app['title']; ?></td>
        <td class="status <?php echo strtolower($app['status']); ?>">
            <?php echo ucfirst($app['status']); ?>
        </td>
        <td>
            <?php if($app['documents']): ?>
                <a href="../<?php echo $app['documents']; ?>" target="_blank">View/Download</a>
            <?php else: ?>
                No Document
            <?php endif; ?>
        </td>
    </tr>
    <?php } ?>
</table>


</div>

</body>
</html>

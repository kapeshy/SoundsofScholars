<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor'){
    header("Location: ../auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];

$donor = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id='$donor_id'")
);

$totalScholarships = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id'")
)['total'];

$approvedScholarships = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id' AND status='Approved'")
)['total'];

$pendingScholarships = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id' AND status='Pending'")
)['total'];

// Fetch notifications
$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$donor_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Donor Dashboard | EduFund</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background-color: #f4f6f8;
}
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    height: 100%;
    background-color: #2980b9;
    color: white;
    padding-top: 60px;
}
.sidebar a {
    display: block;
    color: white;
    padding: 15px 20px;
    text-decoration: none;
    font-weight: bold;
}
.sidebar a:hover {
    background-color: #1f6391;
}
header {
    background-color: #2980b9;
    color: white;
    position: fixed;
    top: 0;
    left: 220px;
    right: 0;
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 20px;
}
header h1 {
    margin: 0;
    font-size: 22px;
}
.main {
    margin-left: 220px;
    margin-top: 60px;
    padding: 20px;
}
h2 {
    color: #2980b9;
}

/* Cards */
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
    font-size: 16px;
    color: #555;
}
.card p {
    font-size: 30px;
    font-weight: bold;
    margin-top: 10px;
    color: #2980b9;
}

/* CTA */
.cta {
    background-color: #2980b9;
    color: white;
    padding: 25px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.cta a {
    background-color: #27ae60;
    color: white;
    padding: 12px 18px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
}
.cta a:hover {
    background-color: #1e8449;
}

/* Notifications */
.notifications {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
.notifications h3 {
    color: #2980b9;
    margin-top: 0;
}
.notification-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.notification-item.unread {
    background-color: #dff0d8;
    font-weight: bold;
}
.notification-item:last-child {
    border-bottom: none;
}
.notification-time {
    display: block;
    font-size: 12px;
    color: #777;
    margin-top: 5px;
}
</style>
</head>
<body>

<div class="sidebar">
    <h2 style="text-align:center;">Donor Menu</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="create_scholarship.php">Add Scholarship</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<header>
    <h1>Welcome, <?php echo $donor['full_name']; ?></h1>
</header>

<div class="main">

<h2>Donor Dashboard Overview</h2>

<div class="cards">
    <div class="card">
        <h3>Total Scholarships</h3>
        <p><?php echo $totalScholarships; ?></p>
    </div>
    <div class="card">
        <h3>Approved Scholarships</h3>
        <p><?php echo $approvedScholarships; ?></p>
    </div>
    <div class="card">
        <h3>Pending Approval</h3>
        <p><?php echo $pendingScholarships; ?></p>
    </div>
</div>

<div class="cta">
    <div>
        <h2>Support Education</h2>
        <p>Create scholarships and change lives through education.</p>
    </div>
    <a href="create_scholarship.php">+ Create Scholarship</a>
</div>

<div class="notifications">
    <h3>Notifications</h3>
    <?php if(mysqli_num_rows($notifications) > 0): ?>
        <?php while($note = mysqli_fetch_assoc($notifications)): ?>
            <div class="notification-item <?php echo $note['is_read'] ? '' : 'unread'; ?>">
                <?php echo $note['message']; ?>
                <span class="notification-time"><?php echo $note['created_at']; ?></span>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications yet.</p>
    <?php endif; ?>
</div>

</div>

</body>
</html>

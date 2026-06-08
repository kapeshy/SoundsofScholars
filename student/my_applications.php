<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

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
<title>My Applications | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --blue-50:#E6F1FB;
    --amber-200:#EF9F27;
    --gray-100:#D3D1C7;
}

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'DM Sans', sans-serif;
}

/* TOP BAR */
.topbar {
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:64px;
    background:var(--blue-900);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 24px;
    color:white;
    z-index:1000;
}

.topbar h1 {
    font-family:'Playfair Display', serif;
    font-size:18px;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 64px;
    left: 0;
    bottom: 0;
    width: 230px;
    background: var(--blue-900);
    padding: 20px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.brand {
    display:flex;
    align-items:center;
    gap:10px;
    color:white;
    font-family:'Playfair Display', serif;
    margin-bottom:20px;
    padding:10px;
}

.brand .dot {
    width:10px;
    height:10px;
    background:var(--amber-200);
    border-radius:50%;
}

.sidebar a {
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    padding:10px 12px;
    border-radius:8px;
    font-size:14px;
}

.sidebar a:hover {
    background:rgba(255,255,255,0.08);
    color:white;
}

.sidebar a.active {
    background:var(--amber-200);
    color:var(--blue-900);
    font-weight:600;
}

/* MAIN */
.container {
    margin-top:80px;
    margin-left:250px;
    padding:24px;
}

/* TITLE */
h2 {
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:6px;
}

.subtitle {
    font-size:14px;
    color:#666;
    margin-bottom:20px;
}

/* SUMMARY CARDS */
.summary {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:14px;
    margin-bottom:25px;
}

.box {
    background:white;
    padding:16px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    border-left:4px solid var(--blue-600);
}

.box h3 {
    font-size:13px;
    color:#666;
}

.box p {
    font-size:24px;
    font-weight:700;
    color:var(--blue-900);
    margin-top:6px;
}

/* TABLE */
table {
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

th {
    background:var(--blue-900);
    color:white;
    padding:12px;
    text-align:left;
    font-size:13px;
}

td {
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

tr:hover {
    background:#fafafa;
}

/* STATUS BADGES */
.status {
    padding:6px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.pending {
    background:#fff3cd;
    color:#856404;
}

.approved {
    background:#d4edda;
    color:#155724;
}

.rejected {
    background:#f8d7da;
    color:#721c24;
}

/* EMPTY STATE */
.empty {
    background:white;
    padding:20px;
    border-radius:12px;
    text-align:center;
    color:#777;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>My Applications</h1>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="dot"></div>
        <span>Student Portal</span>
    </div>

    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php" class="active">My Applications</a>
    <a href="eligibility.php">Eligibility</a>
</div>

<!-- MAIN -->
<div class="container">

    <h2>Application Tracker</h2>
    <p class="subtitle">Track the status of all your scholarship applications</p>

    <!-- You can later calculate real counts if you want -->
    <div class="summary">
        <div class="box">
            <h3>Total Applications</h3>
            <p><?php echo mysqli_num_rows($applications); ?></p>
        </div>
    </div>

    <?php if(mysqli_num_rows($applications) > 0){ ?>

    <table>
        <tr>
            <th>Scholarship</th>
            <th>Status</th>
            <th>Date Applied</th>
        </tr>

        <?php while($app = mysqli_fetch_assoc($applications)){ ?>
        <tr>
            <td><?php echo $app['title']; ?></td>
            <td>
                <span class="status <?php echo strtolower($app['status']); ?>">
                    <?php echo ucfirst($app['status']); ?>
                </span>
            </td>
            <td>
                <?php echo date("d M Y", strtotime($app['applied_at'])); ?>
            </td>
        </tr>
        <?php } ?>

    </table>

    <?php } else { ?>

    <div class="empty">
        You have not applied to any scholarships yet.
    </div>

    <?php } ?>

</div>

</body>
</html>
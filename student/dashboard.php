<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id'"));

$scholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE status='Approved' AND deadline >= CURDATE()");

$applications = mysqli_query($conn, "
    SELECT a.id, s.title, a.status, a.documents
    FROM applications a
    JOIN scholarships s ON a.scholarship_id = s.id
    WHERE a.user_id='$student_id'
");

$totalScholarships = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships"))['total'];

$totalApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id'"))['total'];

$approvedApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Approved'"))['total'];

$rejectedApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Rejected'"))['total'];

$pendingApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Pending'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --blue-50:#E6F1FB;
    --amber-200:#EF9F27;
    --gray-900:#2C2C2A;
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

.logout {
    color:white;
    text-decoration:none;
    font-size:14px;
    padding:8px 14px;
    border:1px solid rgba(255,255,255,0.2);
    border-radius:6px;
}

.logout:hover {
    background:rgba(255,255,255,0.1);
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
    transition:0.2s;
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

.sidebar .logout {
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,0.1);
    padding-top:12px;
}

/* MAIN */
.container {
    margin-top:80px;
    margin-left:250px;
    padding:24px;
    max-width:1200px;
}

/* CARDS */
.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:16px;
    margin-bottom:30px;
}

.card {
    background:white;
    padding:18px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    border-left:4px solid var(--blue-600);
}

.card h3 {
    font-size:13px;
    color:#666;
}

.card p {
    font-size:26px;
    font-weight:700;
    margin-top:8px;
    color:var(--blue-900);
}

/* TABLES */
h2 {
    font-family:'Playfair Display', serif;
    margin:20px 0 10px;
    color:var(--blue-900);
}

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
    text-align:left;
    padding:12px;
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

/* BUTTON */
.apply {
    background:var(--amber-200);
    color:var(--blue-900);
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.apply:hover {
    background:#f7b224;
}

/* STATUS */
.status {
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.pending { background:#fff3cd; color:#856404; }
.approved { background:#d4edda; color:#155724; }
.rejected { background:#f8d7da; color:#721c24; }

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>Welcome, <?php echo $student['full_name']; ?></h1>
    <a class="logout" href="../auth/logout.php">Logout</a>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="dot"></div>
        <span>Student Portal</span>
    </div>

    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility</a>

    <a href="../auth/logout.php" class="logout">Logout</a>
</div>

<!-- MAIN -->
<div class="container">

    <div class="cards">
        <div class="card">
            <h3>Total Scholarships</h3>
            <p><?php echo $totalScholarships; ?></p>
        </div>

        <div class="card">
            <h3>Applications</h3>
            <p><?php echo $totalApplications; ?></p>
        </div>

        <div class="card">
            <h3>Approved</h3>
            <p><?php echo $approvedApplications; ?></p>
        </div>

        <div class="card">
            <h3>Pending</h3>
            <p><?php echo $pendingApplications; ?></p>
        </div>

        <div class="card">
            <h3>Rejected</h3>
            <p><?php echo $rejectedApplications; ?></p>
        </div>
    </div>

    <h2>Available Scholarships</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>

        <?php while($s = mysqli_fetch_assoc($scholarships)){ ?>
        <tr>
            <td><?php echo $s['title']; ?></td>
            <td><?php echo $s['deadline']; ?></td>
            <td>
                <a class="apply" href="apply.php?scholarship_id=<?php echo $s['id']; ?>">Apply</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h2>My Applications</h2>
    <table>
        <tr>
            <th>Scholarship</th>
            <th>Status</th>
            <th>Documents</th>
        </tr>

        <?php while($a = mysqli_fetch_assoc($applications)){ ?>
        <tr>
            <td><?php echo $a['title']; ?></td>
            <td>
                <span class="status <?php echo strtolower($a['status']); ?>">
                    <?php echo $a['status']; ?>
                </span>
            </td>
            <td>
                <?php if($a['documents']){ ?>
                    <a href="../<?php echo $a['documents']; ?>">View</a>
                <?php } else { echo "None"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
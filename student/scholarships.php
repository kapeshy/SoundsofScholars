<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$scholarships = mysqli_query($conn, "SELECT * FROM scholarships");

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
<title>Scholarships | SoundsOfScholars</title>

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
    margin-bottom:20px;
}

/* GRID CARDS */
.grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
    gap:16px;
}

/* CARD */
.card {
    background:white;
    border-radius:14px;
    padding:20px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    display:flex;
    flex-direction:column;
    gap:10px;
    border-left:4px solid var(--blue-600);
}

.card h3 {
    font-size:16px;
    color:var(--blue-900);
}

.card p {
    font-size:14px;
    color:#666;
    line-height:1.5;
}

/* META */
.meta {
    font-size:13px;
    color:#555;
}

/* BUTTONS */
.btn {
    display:inline-block;
    padding:10px 12px;
    border-radius:8px;
    text-align:center;
    font-size:14px;
    font-weight:600;
    text-decoration:none;
    margin-top:auto;
}

.apply {
    background:var(--amber-200);
    color:var(--blue-900);
}

.apply:hover {
    background:#f7b224;
}

.applied {
    background:#d6d6d6;
    color:#666;
    cursor:not-allowed;
}

.badge {
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    margin-top:5px;
    width:fit-content;
    background:var(--blue-50);
    color:var(--blue-600);
}

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>Available Scholarships</h1>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="dot"></div>
        <span>Student Portal</span>
    </div>

    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php" class="active">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility</a>
</div>

<!-- MAIN -->
<div class="container">

    <h2>Scholarships</h2>

    <div class="grid">

        <?php while($s = mysqli_fetch_assoc($scholarships)){ ?>

        <div class="card">

            <h3><?php echo $s['title']; ?></h3>

            <span class="badge">Deadline: <?php echo $s['deadline']; ?></span>

            <p><?php echo $s['description']; ?></p>

            <div class="meta">
                <strong>Eligibility:</strong> <?php echo $s['eligibility']; ?>
            </div>

            <?php if(in_array($s['id'], $applied_ids)){ ?>
                <div class="btn applied">Already Applied</div>
            <?php } else { ?>
                <a class="btn apply"
                   href="apply.php?scholarship_id=<?php echo $s['id']; ?>">
                   Apply Now
                </a>
            <?php } ?>

        </div>

        <?php } ?>

    </div>

</div>

</body>
</html>
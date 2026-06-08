<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$scholarships = mysqli_query($conn, "SELECT * FROM scholarships");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Eligibility Criteria | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root{
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --amber:#EF9F27;
    --gray:#f4f6f8;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'DM Sans', sans-serif;
}

/* TOPBAR */
.topbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:64px;
    background:var(--blue-900);
    color:white;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 24px;
    z-index:1000;
}

.topbar h1{
    font-family:'Playfair Display', serif;
    font-size:18px;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    top:64px;
    left:0;
    bottom:0;
    width:230px;
    background:var(--blue-900);
    padding:20px 14px;
}

.sidebar a{
    display:block;
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    padding:10px;
    border-radius:8px;
    font-size:14px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.08);
    color:white;
}

/* MAIN */
.container{
    margin-left:250px;
    margin-top:80px;
    padding:24px;
}

/* HEADER */
h2{
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:6px;
}

.subtitle{
    font-size:14px;
    color:#666;
    margin-bottom:20px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:16px;
}

/* CARD */
.card{
    background:white;
    border-radius:12px;
    padding:18px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    border-left:4px solid var(--blue-600);
    transition:0.2s;
}

.card:hover{
    transform:translateY(-2px);
}

.card h3{
    font-size:16px;
    color:var(--blue-900);
    margin-bottom:8px;
}

.card p{
    font-size:13px;
    color:#555;
    line-height:1.5;
    margin-bottom:8px;
}

.meta{
    font-size:12px;
    color:#777;
}

/* TAG */
.tag{
    display:inline-block;
    padding:4px 8px;
    border-radius:20px;
    font-size:11px;
    background:#fff3cd;
    color:#856404;
    margin-bottom:8px;
}
</style>
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <h1>Eligibility Criteria</h1>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility</a>
</div>

<!-- MAIN -->
<div class="container">

    <h2>Scholarship Requirements</h2>
    <p class="subtitle">Understand eligibility before applying to avoid rejection</p>

    <div class="grid">

        <?php while($scholarship = mysqli_fetch_assoc($scholarships)){ ?>

        <div class="card">

            <div class="tag">Scholarship</div>

            <h3><?php echo htmlspecialchars($scholarship['title']); ?></h3>

            <p>
                <?php echo htmlspecialchars($scholarship['eligibility']); ?>
            </p>

            <div class="meta">
                Deadline: <?php echo date("d M Y", strtotime($scholarship['deadline'])); ?>
            </div>

        </div>

        <?php } ?>

    </div>

</div>

</body>
</html>
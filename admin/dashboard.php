<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Users
$total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='student'"))['total'];
$total_donors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='donor'"))['total'];

// Scholarships
$total_scholarships = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships"))['total'];

// Applications
$total_applications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications"))['total'];

$pending_applications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Pending'"))['total'];
$approved_applications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Approved'"))['total'];
$rejected_applications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Rejected'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | SoundsOfScholars</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --blue-900: #042C53;
    --blue-800: #0C447C;
    --blue-600: #185FA5;
    --blue-400: #378ADD;
    --blue-200: #85B7EB;
    --blue-100: #B5D4F4;
    --blue-50:  #E6F1FB;
    --amber-400: #BA7517;
    --amber-200: #EF9F27;
    --teal-600: #0F6E56;
    --teal-400: #1D9E75;
    --teal-50:  #E1F5EE;
    --gray-900: #2C2C2A;
    --gray-600: #5F5E5A;
    --gray-200: #B4B2A9;
    --gray-100: #D3D1C7;
    --gray-50:  #F1EFE8;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--gray-50);
    color: var(--gray-900);
}

/* ─── SIDEBAR ─── */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 230px;
    height: 100%;
    background: var(--blue-900);
    color: white;
    box-shadow: 4px 0 20px rgba(0,0,0,0.08);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 22px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.sidebar-brand .logo-icon {
    width: 32px;
    height: 32px;
    background: var(--amber-200);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.sidebar-brand span {
    font-family: 'Playfair Display', serif;
    font-size: 17px;
    font-weight: 700;
    letter-spacing: -0.3px;
}

.sidebar nav { padding-top: 10px; }

.sidebar a {
    display: block;
    padding: 15px 22px;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    font-weight: 500;
    font-size: 14.5px;
    border-left: 4px solid transparent;
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.08);
    color: white;
    border-left: 4px solid var(--amber-200);
}

.sidebar a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left: 4px solid var(--amber-200);
}

.sidebar a.logout-link {
    color: rgba(255,255,255,0.55);
    margin-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

/* ─── HEADER ─── */
header {
    position: fixed;
    top: 0;
    left: 230px;
    right: 0;
    height: 64px;
    background: var(--blue-900);
    color: white;
    display: flex;
    align-items: center;
    padding: 0 28px;
    font-weight: 500;
    font-size: 14.5px;
    font-family: 'DM Sans', sans-serif;
    z-index: 1000;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

/* ─── MAIN ─── */
.main {
    margin-left: 230px;
    padding: 32px;
    margin-top: 64px;
}

.main h2 {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    font-weight: 700;
    color: var(--blue-900);
    margin-bottom: 20px;
}

.section-heading {
    margin-top: 36px;
}

/* ─── CARDS ─── */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.card {
    background: white;
    padding: 24px;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
    border-left: 4px solid var(--blue-600);
    transition: box-shadow 0.2s, transform 0.2s;
}

.card:hover {
    box-shadow: 0 10px 30px rgba(4,44,83,0.1);
    transform: translateY(-1px);
}

.card.donors        { border-left-color: var(--teal-400); }
.card.scholarships   { border-left-color: var(--amber-200); }
.card.applications   { border-left-color: var(--blue-400); }

.card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 30px;
    font-weight: 700;
    color: var(--blue-900);
}

.card p {
    color: var(--gray-600);
    margin-top: 6px;
    font-size: 13.5px;
    font-weight: 500;
}

/* ─── CHARTS ─── */
.chart-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 24px;
}

.chart-box {
    background: white;
    padding: 22px;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
}

.chart-box h3 {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--blue-900);
    text-align: center;
    margin-bottom: 14px;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">🎓</div>
        <span>SoundsOfScholars</span>
    </div>

    <nav>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="manage_students.php">Manage Student Applications</a>
        <a href="manage_donors.php">Manage Donor Scholarships</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    SoundsOfScholars Admin Dashboard
</header>

<!-- MAIN -->
<div class="main">

    <h2>System Overview</h2>

    <div class="cards">
        <div class="card">
            <h3><?php echo $total_students; ?></h3>
            <p>Students</p>
        </div>

        <div class="card donors">
            <h3><?php echo $total_donors; ?></h3>
            <p>Donors</p>
        </div>

        <div class="card scholarships">
            <h3><?php echo $total_scholarships; ?></h3>
            <p>Scholarships</p>
        </div>

        <div class="card applications">
            <h3><?php echo $total_applications; ?></h3>
            <p>Applications</p>
        </div>
    </div>

    <h2 class="section-heading">Analytics</h2>

    <div class="chart-container">
        <div class="chart-box">
            <h3>Users &amp; Scholarships</h3>
            <canvas id="usersChart"></canvas>
        </div>

        <div class="chart-box">
            <h3>Applications Status</h3>
            <canvas id="applicationsChart"></canvas>
        </div>
    </div>

</div>

<script>
new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: ['Students', 'Donors', 'Scholarships'],
        datasets: [{
            data: [
                <?php echo $total_students; ?>,
                <?php echo $total_donors; ?>,
                <?php echo $total_scholarships; ?>
            ],
            backgroundColor: ['#185FA5', '#0F6E56', '#EF9F27'],
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        responsive: true,
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

new Chart(document.getElementById('applicationsChart'), {
    type: 'pie',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            data: [
                <?php echo $pending_applications; ?>,
                <?php echo $approved_applications; ?>,
                <?php echo $rejected_applications; ?>
            ],
            backgroundColor: ['#EF9F27', '#1D9E75', '#D85A30']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

</body>
</html>
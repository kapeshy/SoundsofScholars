<?php
session_start();
include("../config/db.php");

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


// Users
$total_students = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='student'")
)['total'];

$total_donors = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='donor'")
)['total'];

// Scholarships
$total_scholarships = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships")
)['total'];

// Applications
$total_applications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications")
)['total'];

$pending_applications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Pending'")
)['total'];

$approved_applications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Approved'")
)['total'];

$rejected_applications = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE status='Rejected'")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | SoundsOfScholars</title>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    background-color: #f4f6f8;
}

/* Sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100%;
    width: 220px;
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

/* Header */
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
    z-index: 1000;
}

header h1 {
    margin: 0;
    font-size: 22px;
}

/* Main */
.main {
    margin-left: 220px;
    padding: 20px;
    margin-top: 60px;
}

h2 {
    color: #2980b9;
}

/* Cards */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    text-align: center;
}

.card h3 {
    margin: 0;
    font-size: 32px;
    color: #2980b9;
}

.card p {
    margin-top: 10px;
    color: #555;
    font-weight: bold;
}

/* Charts */
.chart-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.chart-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2 style="text-align:center;">Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_students.php">Manage Students</a>
    <a href="manage_donors.php">Manage Donors</a>
    <a href="manage_scholarships.php">Manage Scholarships</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- HEADER -->
<header>
    <h1>SoundsOfScholars | Admin Dashboard</h1>
</header>

<!-- MAIN -->
<div class="main">

    <h2>Admin System Overview</h2>

    <!-- STAT CARDS -->
    <div class="cards">
        <div class="card">
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>

        <div class="card">
            <h3><?php echo $total_donors; ?></h3>
            <p>Total Donors</p>
        </div>

        <div class="card">
            <h3><?php echo $total_scholarships; ?></h3>
            <p>Total Scholarships</p>
        </div>

        <div class="card">
            <h3><?php echo $total_applications; ?></h3>
            <p>Total Applications</p>
        </div>
    </div>

    <!-- CHARTS -->
    <h2 style="margin-top:40px;">Analytics</h2>

    <div class="chart-container">
        <div class="chart-box">
            <h3 style="text-align:center;">Users & Scholarships</h3>
            <canvas id="usersChart"></canvas>
        </div>

        <div class="chart-box">
            <h3 style="text-align:center;">Application Status</h3>
            <canvas id="applicationsChart"></canvas>
        </div>
    </div>

</div>

<!-- CHART SCRIPTS -->
<script>
// Bar Chart
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
            backgroundColor: ['#2980b9', '#8e44ad', '#16a085']
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        responsive: true
    }
});

// Pie Chart
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
            backgroundColor: ['#f39c12', '#27ae60', '#e74c3c']
        }]
    },
    options: {
        responsive: true
    }
});
</script>

</body>
</html>

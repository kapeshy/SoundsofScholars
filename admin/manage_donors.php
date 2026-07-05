<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Handle donor deletion
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Donors | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

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
    height: 100%;
    width: 230px;
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
    font-size: 24px;
    font-weight: 700;
    color: var(--blue-900);
    margin-bottom: 20px;
}

/* ─── TABLE ─── */
.table-box {
    background: white;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: var(--blue-900);
    color: white;
    text-align: left;
    padding: 14px 16px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--gray-100);
    font-size: 13.5px;
    color: var(--gray-900);
}

tr:last-child td { border-bottom: none; }

tbody tr {
    transition: background 0.15s;
}

tr:hover {
    background: var(--blue-50);
}

/* ─── BUTTONS ─── */
.btn {
    padding: 7px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    margin-right: 6px;
    display: inline-block;
    transition: opacity 0.15s, transform 0.15s;
}

.btn:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

.btn.delete {
    background: #D85A30;
    color: white;
}

.btn.view {
    background: var(--blue-600);
    color: white;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .table-box { overflow-x: auto; }
    table { min-width: 560px; }
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
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_students.php">Manage Students Applications</a>
        <a href="manage_donors.php" class="active">Manage Donor Scholarships</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    Manage Donors
</header>

<!-- MAIN -->
<div class="main">
    <h2>Donor Scholarships</h2>

    <div class="table-box">
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Scholarships</th>
            <th>Actions</th>
        </tr>

        <?php while($donor = mysqli_fetch_assoc($donors)):
            $count = mysqli_fetch_assoc(mysqli_query(
                $conn,
                "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='{$donor['id']}'"
            ))['total'];
        ?>
        <tr>
            <td><?php echo $donor['id']; ?></td>
            <td><?php echo $donor['full_name']; ?></td>
            <td><?php echo $donor['email']; ?></td>
            <td><?php echo $count; ?></td>
            <td>
                <a href="manage_donors.php?delete_id=<?php echo $donor['id']; ?>"
                   class="btn delete"
                   onclick="return confirm('Delete this donor?')">
                   Delete
                </a>

                <a href="view_scholarships.php?donor_id=<?php echo $donor['id']; ?>"
                   class="btn view">
                   View
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</div>

</body>
</html>
<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Delete student
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']); 
    mysqli_query($conn, "DELETE FROM users WHERE id='$delete_id' AND role='student'");
    header("Location: manage_students.php");
    exit();
}

// Update application status
if(isset($_GET['app_id']) && isset($_GET['status'])){
    $app_id = intval($_GET['app_id']);
    $status = $_GET['status'];

    $allowed_status = ['Pending','Approved','Rejected'];

    if(in_array($status, $allowed_status)){
        mysqli_query($conn, "UPDATE applications SET status='$status' WHERE id='$app_id'");
        header("Location: manage_students.php");
        exit();
    }
}

$students = mysqli_query($conn, "
    SELECT DISTINCT u.* FROM users u
    INNER JOIN applications a ON a.user_id = u.id
    WHERE u.role = 'student'
    ORDER BY u.full_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Students | Admin</title>
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
    margin-top: 64px;
    padding: 32px;
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
    padding: 14px 16px;
    text-align: left;
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

td a {
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
}

td a:hover { text-decoration: underline; }

/* ─── BADGES ─── */
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: white;
    display: inline-block;
}

.status-badge.approved { background: var(--teal-400); }
.status-badge.rejected { background: #D85A30; }
.status-badge.pending  { background: var(--amber-200); color: var(--blue-900); }

/* ─── ACTION BUTTONS ─── */
.action-btn {
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    margin-right: 5px;
    margin-bottom: 4px;
    display: inline-block;
    transition: opacity 0.15s, transform 0.15s;
}

.action-btn.approve { background: var(--teal-400); color: white; }
.action-btn.reject  { background: #D85A30; color: white; }
.action-btn.pending { background: var(--amber-200); color: var(--blue-900); }
.action-btn.delete  { background: var(--blue-900); color: white; }

.action-btn:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

.no-applications {
    text-align: center;
    color: var(--gray-200);
    font-size: 13.5px;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .table-box { overflow-x: auto; }
    table { min-width: 720px; }
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
        <a href="manage_students.php" class="active">Students Applications</a>
        <a href="manage_donors.php">Manage Donor Scholarships</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    Manage Students &amp; Applications
</header>

<!-- MAIN -->
<div class="main">

<h2>Student Applications</h2>

<div class="table-box">
<table>
    <tr>
        <th>Student</th>
        <th>Email</th>
        <th>Scholarship</th>
        <th>Status</th>
        <th>Documents</th>
        <th>Actions</th>
    </tr>

    <?php while($student = mysqli_fetch_assoc($students)):

        $apps = mysqli_query($conn,
            "SELECT a.id AS app_id, s.title AS scholarship, a.status, a.documents
             FROM applications a
             JOIN scholarships s ON a.scholarship_id = s.id
             WHERE a.user_id='{$student['id']}'"
        );

        if(mysqli_num_rows($apps) > 0):

            while($app = mysqli_fetch_assoc($apps)):
    ?>

    <tr>
        <td><?php echo $student['full_name']; ?></td>
        <td><?php echo $student['email']; ?></td>
        <td><?php echo $app['scholarship']; ?></td>

        <td>
            <span class="status-badge <?php echo strtolower($app['status']); ?>">
                <?php echo ucfirst($app['status']); ?>
            </span>
        </td>

        <td>
            <?php if($app['documents']): ?>
                <a href="../student/<?php echo $app['documents']; ?>" target="_blank">View</a>
            <?php else: ?>
                None
            <?php endif; ?>
        </td>

        <td>
            <a class="action-btn approve" href="?app_id=<?php echo $app['app_id']; ?>&status=Approved">Approve</a>
            <a class="action-btn reject" href="?app_id=<?php echo $app['app_id']; ?>&status=Rejected">Reject</a>
            <a class="action-btn pending" href="?app_id=<?php echo $app['app_id']; ?>&status=Pending">Pending</a>

            <a class="action-btn delete"
               href="?delete_id=<?php echo $student['id']; ?>"
               onclick="return confirm('Delete this student?')">
               Delete
            </a>
        </td>
    </tr>

    <?php endwhile; else: ?>

    <tr>
        <td><?php echo $student['full_name']; ?></td>
        <td><?php echo $student['email']; ?></td>
        <td colspan="4" class="no-applications">No applications</td>
    </tr>

    <?php endif; endwhile; ?>

</table>
</div>

</div>

</body>
</html>
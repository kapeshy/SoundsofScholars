<?php
session_start();
include("../config/db.php");

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

// Get donor ID
if(!isset($_GET['donor_id'])){
    header("Location: manage_donors.php");
    exit();
}

$donor_id = intval($_GET['donor_id']);

// =============================
// HANDLE STATUS UPDATE (FIXED)
// =============================
if(isset($_GET['sch_id']) && isset($_GET['status'])){
    $sch_id = intval($_GET['sch_id']);
    $status = $_GET['status'];

    $allowed_status = ['Pending','Approved','Rejected'];

    if(in_array($status, $allowed_status)){

        // Get scholarship
        $result = mysqli_query($conn, "SELECT * FROM scholarships WHERE id='$sch_id'");
        $sch = mysqli_fetch_assoc($result);

        if($sch){

            // Update status
            mysqli_query($conn, "
                UPDATE scholarships 
                SET status='$status' 
                WHERE id='$sch_id'
            ");

            $donor_id_fk = intval($sch['donor_id']);
            $title = $sch['title'];

            $message = "Your scholarship '{$title}' status has been updated to '{$status}'";

            // =========================
            // FIXED NOTIFICATION INSERT
            // =========================
            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, message, is_read, created_at)
                VALUES (?, ?, 0, NOW())
            ");

            $stmt->bind_param("is", $donor_id_fk, $message);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: view_scholarships.php?donor_id=$donor_id");
        exit();
    }
}

// =============================
// HANDLE DELETE
// =============================
if(isset($_GET['delete_sch'])){
    $sch_id = intval($_GET['delete_sch']);

    mysqli_query($conn, "DELETE FROM scholarships WHERE id='$sch_id'");

    header("Location: view_scholarships.php?donor_id=$donor_id");
    exit();
}

// Fetch donor
$donor = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM users WHERE id='$donor_id'"
));

// Fetch scholarships
$scholarships = mysqli_query(
    $conn,
    "SELECT * FROM scholarships WHERE donor_id='$donor_id'"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donor Scholarships | Admin</title>
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
    margin-top: 64px;
    padding: 32px;
}

.back-link {
    display: inline-block;
    font-size: 13.5px;
    color: var(--blue-600);
    text-decoration: none;
    margin-bottom: 16px;
    font-weight: 500;
}

.back-link:hover { text-decoration: underline; }

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
    vertical-align: top;
}

tr:last-child td { border-bottom: none; }

tbody tr {
    transition: background 0.15s;
}

tr:hover {
    background: var(--blue-50);
}

/* ─── STATUS BADGES ─── */
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 0.02em;
    color: white;
    display: inline-block;
    white-space: nowrap;
}

.status-badge.approved { background: var(--teal-400); }
.status-badge.rejected { background: #D85A30; }
.status-badge.pending  { background: var(--amber-200); color: var(--blue-900); }

/* ─── ACTIONS ─── */
.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: white;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    transition: opacity 0.15s, transform 0.15s;
}

.action-btn:hover {
    opacity: 0.85;
    transform: translateY(-1px);
}

.action-btn.approve { background: var(--teal-400); }
.action-btn.reject  { background: #D85A30; }
.action-btn.pending { background: var(--amber-200); color: var(--blue-900); }
.action-btn.delete  { background: var(--blue-900); }

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .table-box { overflow-x: auto; }
    table { min-width: 760px; }
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">🎓</div>
        <span>SoundsOfScholars</span>
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_students.php">Students</a>
        <a href="manage_donors.php" class="active">Donors</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<header>
    Scholarships
</header>

<div class="main">

<a href="manage_donors.php" class="back-link">← Back to Manage Donors</a>

<h2>Scholarships created by <?php echo $donor['full_name']; ?></h2>

<div class="table-box">
<table>
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Description</th>
    <th>Eligibility</th>
    <th>Deadline</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while($sch = mysqli_fetch_assoc($scholarships)): ?>
<tr>
    <td><?php echo $sch['id']; ?></td>
    <td><?php echo $sch['title']; ?></td>
    <td><?php echo $sch['description']; ?></td>
    <td><?php echo $sch['eligibility']; ?></td>
    <td><?php echo $sch['deadline']; ?></td>

    <td>
        <span class="status-badge <?php echo strtolower($sch['status']); ?>">
            <?php echo $sch['status']; ?>
        </span>
    </td>

    <td>
        <div class="actions">

            <a class="action-btn approve"
               href="?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Approved">
               Approve
            </a>

            <a class="action-btn reject"
               href="?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Rejected">
               Reject
            </a>

            <a class="action-btn pending"
               href="?donor_id=<?php echo $donor_id; ?>&sch_id=<?php echo $sch['id']; ?>&status=Pending">
               Pending
            </a>

            <a class="action-btn delete"
               href="?donor_id=<?php echo $donor_id; ?>&delete_sch=<?php echo $sch['id']; ?>"
               onclick="return confirm('Delete scholarship?')">
               Delete
            </a>

        </div>
    </td>
</tr>
<?php endwhile; ?>

</table>
</div>

</div>

</body>
</html>
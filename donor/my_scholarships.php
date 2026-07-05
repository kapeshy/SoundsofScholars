<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor'){
    header("Location: ../auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];

/* Donor Info */
$donor = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id='$donor_id'")
);

/* Statistics */
$total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id'")
)['total'];

$approved = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id' AND status='Approved'")
)['total'];

$pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id' AND status='Pending'")
)['total'];

$rejected = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE donor_id='$donor_id' AND status='Rejected'")
)['total'];

/* Scholarships */
$scholarships = mysqli_query(
    $conn,
    "SELECT * FROM scholarships
     WHERE donor_id='$donor_id'
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Scholarships | SoundsOfScholars</title>
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

.page-title {
    font-family: 'Playfair Display', serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--blue-900);
    margin-bottom: 24px;
}

.success-msg {
    background: var(--teal-50);
    border: 1px solid var(--teal-400);
    color: var(--teal-600);
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* ─── CARDS ─── */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
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

.card.approved { border-left-color: var(--teal-400); }
.card.pending  { border-left-color: var(--amber-200); }
.card.rejected { border-left-color: #D85A30; }

.card h3 {
    color: var(--gray-600);
    font-size: 13.5px;
    font-weight: 500;
    letter-spacing: 0.02em;
    margin-bottom: 10px;
}

.card p {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 700;
    color: var(--blue-900);
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
    padding: 16px;
    border-bottom: 1px solid var(--gray-100);
    font-size: 14.5px;
    color: var(--gray-900);
}

tr:last-child td { border-bottom: none; }

tbody tr {
    transition: background 0.15s;
}

tbody tr:hover {
    background: var(--blue-50);
}

/* ─── STATUS BADGES ─── */
.badge {
    padding: 6px 14px;
    border-radius: 20px;
    color: white;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    display: inline-block;
}

.badge.approved { background: var(--teal-400); }
.badge.pending  { background: var(--amber-200); color: var(--blue-900); }
.badge.rejected { background: #D85A30; }

.empty {
    text-align: center;
    padding: 40px;
    color: var(--gray-200);
    font-size: 14.5px;
}

.view-link {
    font-size: 13.5px;
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
}

.view-link:hover { text-decoration: underline; }

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .table-box { overflow-x: auto; }
    table { min-width: 600px; }
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
        <a href="create_scholarship.php">Create Scholarship</a>
        <a href="my_scholarships.php" class="active">My Scholarships</a>
        <a href="view_applicants.php">View Applicants</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    My Scholarships
</header>

<div class="main">

    <h2 class="page-title">
        Scholarships Created by <?php echo htmlspecialchars($donor['full_name']); ?>
    </h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success-msg">Scholarship deleted successfully.</div>
    <?php endif; ?>

    <!-- STATISTICS -->
    <div class="cards">

        <div class="card">
            <h3>Total Scholarships</h3>
            <p><?php echo $total; ?></p>
        </div>

        <div class="card approved">
            <h3>Approved</h3>
            <p><?php echo $approved; ?></p>
        </div>

        <div class="card pending">
            <h3>Pending</h3>
            <p><?php echo $pending; ?></p>
        </div>

        <div class="card rejected">
            <h3>Rejected</h3>
            <p><?php echo $rejected; ?></p>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-box">

        <table>

            <tr>
                <th>Title</th>
                <th>Eligibility</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Applicants</th>
                <th>Actions</th>
            </tr>

            <?php if(mysqli_num_rows($scholarships) > 0): ?>

                <?php while($sch = mysqli_fetch_assoc($scholarships)): ?>

                <tr>
                    <td>
                        <?php echo htmlspecialchars($sch['title']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($sch['eligibility']); ?>
                    </td>

                    <td>
                        <?php echo date("d M Y", strtotime($sch['deadline'])); ?>
                    </td>

                    <td>

                        <?php
                        $status = strtolower($sch['status']);
                        ?>

                        <span class="badge <?php echo $status; ?>">
                            <?php echo ucfirst($sch['status']); ?>
                        </span>

                    </td>

                    <td>
                        <a href="view_applicants.php?scholarship_id=<?php echo $sch['id']; ?>" class="view-link">
                            View applicants
                        </a>
                    </td>

                    <td>
                        <a href="edit_scholarship.php?id=<?php echo $sch['id']; ?>" class="view-link">
                            Edit
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="empty">
                        You have not created any scholarships yet.
                    </td>
                </tr>

            <?php endif; ?>

        </table>

    </div>

</div>

</body>
</html>
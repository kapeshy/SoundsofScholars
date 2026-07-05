<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: ../auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];

/* Donor Info */
$donor = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id='$donor_id'")
);

/* Optional filter: scholarship_id from query string */
$filter_id = isset($_GET['scholarship_id']) ? (int) $_GET['scholarship_id'] : 0;

/* All scholarships belonging to this donor (for the filter dropdown) */
$myScholarships = mysqli_query(
    $conn,
    "SELECT id, title, status FROM scholarships WHERE donor_id='$donor_id' ORDER BY id DESC"
);

/* Build the applicants query — only applications tied to THIS donor's scholarships */
$sql = "
    SELECT
        a.id            AS application_id,
        a.status        AS application_status,
        a.applied_at,
        a.background,
        a.gpa,
        a.documents,
        s.id            AS scholarship_id,
        s.title         AS scholarship_title,
        u.full_name     AS student_name,
        u.email         AS student_email
    FROM applications a
    INNER JOIN scholarships s ON a.scholarship_id = s.id
    INNER JOIN users u        ON a.user_id = u.id
    WHERE s.donor_id = '$donor_id'
";

if ($filter_id > 0) {
    $sql .= " AND s.id = '$filter_id'";
}

$sql .= " ORDER BY a.applied_at DESC";

$applicants = mysqli_query($conn, $sql);

/* Quick counts for the stat cards (scoped to current filter) */
$countSql = "
    SELECT
        COUNT(*) AS total,
        SUM(a.status = 'Approved') AS approved,
        SUM(a.status = 'Pending')  AS pending,
        SUM(a.status = 'Rejected') AS rejected
    FROM applications a
    INNER JOIN scholarships s ON a.scholarship_id = s.id
    WHERE s.donor_id = '$donor_id'
";
if ($filter_id > 0) {
    $countSql .= " AND s.id = '$filter_id'";
}
$counts = mysqli_fetch_assoc(mysqli_query($conn, $countSql));
$total    = $counts['total'] ?? 0;
$approved = $counts['approved'] ?? 0;
$pending  = $counts['pending'] ?? 0;
$rejected = $counts['rejected'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Applicants | SoundsOfScholars</title>
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
    left: 230px;
    top: 0;
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

/* ─── FILTER BAR ─── */
.filter-bar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
    padding: 16px 20px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.filter-bar label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-900);
}

.filter-bar select {
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid var(--gray-100);
    background: var(--gray-50);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--gray-900);
    min-width: 240px;
}

.filter-bar select:focus {
    outline: none;
    border-color: var(--blue-400);
    background: white;
}

.filter-bar .clear-link {
    font-size: 13px;
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
}

.filter-bar .clear-link:hover { text-decoration: underline; }

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

/* ─── APPLICANT LIST ─── */
.applicant-card {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
    padding: 22px 26px;
    margin-bottom: 16px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 16px;
    align-items: start;
}

.applicant-main h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--blue-900);
    margin-bottom: 4px;
}

.applicant-main .scholarship-tag {
    display: inline-block;
    font-size: 12px;
    font-weight: 500;
    color: var(--blue-600);
    background: var(--blue-50);
    padding: 3px 10px;
    border-radius: 6px;
    margin-bottom: 10px;
}

.applicant-main .email {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 12px;
}

.applicant-meta {
    display: flex;
    gap: 22px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.applicant-meta div {
    font-size: 13px;
}

.applicant-meta span {
    display: block;
    font-size: 11px;
    color: var(--gray-200);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 2px;
}

.applicant-meta strong {
    color: var(--blue-900);
    font-weight: 600;
}

.background-text {
    font-size: 13.5px;
    color: var(--gray-600);
    line-height: 1.6;
    background: var(--gray-50);
    border-radius: 8px;
    padding: 12px 14px;
}

.applicant-side {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.badge {
    padding: 6px 14px;
    border-radius: 20px;
    color: white;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.badge.approved { background: var(--teal-400); }
.badge.pending  { background: var(--amber-200); color: var(--blue-900); }
.badge.rejected { background: #D85A30; }

.doc-link {
    font-size: 13px;
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
    border: 1px solid var(--blue-200);
    padding: 7px 14px;
    border-radius: 8px;
    transition: background 0.15s;
    white-space: nowrap;
}

.doc-link:hover { background: var(--blue-50); }

.applied-date {
    font-size: 12px;
    color: var(--gray-200);
}

.empty-state {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
    padding: 50px 30px;
    text-align: center;
    color: var(--gray-200);
    font-size: 14.5px;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .main { margin-left: 200px; padding: 20px; }
    .applicant-card { grid-template-columns: 1fr; }
    .applicant-side { flex-direction: row; align-items: center; justify-content: space-between; }
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
        <a href="my_scholarships.php">My Scholarships</a>
        <a href="view_applicants.php" class="active">View Applicants</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    View Applicants
</header>

<div class="main">

    <h2 class="page-title">
        Applicants to <?php echo htmlspecialchars($donor['full_name']); ?>'s Scholarships
    </h2>

    <!-- FILTER -->
    <form class="filter-bar" method="GET">
        <label for="scholarship_id">Filter by scholarship:</label>
        <select name="scholarship_id" id="scholarship_id" onchange="this.form.submit()">
            <option value="0">All scholarships</option>
            <?php
            mysqli_data_seek($myScholarships, 0);
            while ($s = mysqli_fetch_assoc($myScholarships)):
            ?>
                <option value="<?php echo $s['id']; ?>" <?php echo ($filter_id == $s['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['title']); ?> (<?php echo $s['status']; ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <?php if ($filter_id > 0): ?>
            <a href="view_applicants.php" class="clear-link">Clear filter</a>
        <?php endif; ?>
    </form>

    <!-- STATS -->
    <div class="cards">
        <div class="card">
            <h3>Total Applicants</h3>
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

    <!-- APPLICANT LIST -->
    <?php if (mysqli_num_rows($applicants) > 0): ?>

        <?php while ($app = mysqli_fetch_assoc($applicants)): ?>
            <?php $status = strtolower($app['application_status']); ?>

            <div class="applicant-card">
                <div class="applicant-main">
                    <span class="scholarship-tag"><?php echo htmlspecialchars($app['scholarship_title']); ?></span>
                    <h4><?php echo htmlspecialchars($app['student_name']); ?></h4>
                    <div class="email"><?php echo htmlspecialchars($app['student_email']); ?></div>

                    <div class="applicant-meta">
                        <div>
                            <span>GPA</span>
                            <strong><?php echo htmlspecialchars($app['gpa']); ?></strong>
                        </div>
                        <div>
                            <span>Applied</span>
                            <strong><?php echo date("d M Y", strtotime($app['applied_at'])); ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($app['background'])): ?>
                        <div class="background-text">
                            <?php echo nl2br(htmlspecialchars($app['background'])); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="applicant-side">
                    <span class="badge <?php echo $status; ?>">
                        <?php echo ucfirst($app['application_status']); ?>
                    </span>

                    <?php if (!empty($app['documents'])): ?>
                        <a href="../student/<?php echo htmlspecialchars($app['documents']); ?>" target="_blank" class="doc-link">
                            View Document
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="empty-state">
            No applicants yet for <?php echo $filter_id > 0 ? 'this scholarship' : 'your scholarships'; ?>.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
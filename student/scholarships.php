<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

/* ── Search query ── */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_esc = mysqli_real_escape_string($conn, $search);

/* ── All approved, open scholarships (with optional search filter) ── */
$baseSql = "SELECT * FROM scholarships WHERE status='Approved' AND deadline >= CURDATE()";

if ($search !== '') {
    $baseSql .= " AND (title LIKE '%$search_esc%' OR description LIKE '%$search_esc%' OR eligibility LIKE '%$search_esc%')";
}

$baseSql .= " ORDER BY deadline ASC";
$scholarships = mysqli_query($conn, $baseSql);

/* ── Already applied ── */
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scholarships | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --blue-400:#378ADD;
    --blue-50:#E6F1FB;
    --amber-200:#EF9F27;
    --teal-600:#0F6E56;
    --teal-400:#1D9E75;
    --teal-50:#E1F5EE;
    --gray-900:#2C2C2A;
    --gray-600:#5F5E5A;
    --gray-200:#B4B2A9;
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
    margin-bottom:14px;
}

/* SEARCH BAR */
.search-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 28px;
    max-width: 520px;
}

.search-bar input {
    flex: 1;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid var(--gray-100);
    font-size: 14px;
    font-family: 'DM Sans', sans-serif;
    background: white;
}

.search-bar input:focus {
    outline: none;
    border-color: var(--blue-400);
}

.search-bar button {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    background: var(--blue-800);
    color: white;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
}

.search-bar button:hover { background: var(--blue-600); }

.search-bar .clear-btn {
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid var(--gray-100);
    background: white;
    color: var(--gray-600);
    font-size: 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.search-result-note {
    font-size: 13.5px;
    color: var(--gray-600);
    margin: -16px 0 20px;
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
    color:var(--gray-600);
    line-height:1.5;
}

/* META */
.meta {
    font-size:13px;
    color:var(--gray-600);
}

.criteria-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.criteria-chip {
    font-size: 11.5px;
    color: var(--gray-600);
    background: var(--gray-100);
    padding: 3px 9px;
    border-radius: 12px;
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
    color:var(--gray-600);
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

.empty-state {
    background: white;
    border-radius: 14px;
    padding: 40px 24px;
    text-align: center;
    color: var(--gray-200);
    font-size: 14.5px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    .container { margin-left: 200px; padding: 16px; }
    .search-bar { max-width: 100%; }
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

    <h2>Available Scholarships</h2>

    <!-- SEARCH -->
    <form class="search-bar" method="GET">
        <input type="text" name="search" placeholder="Search by title, description, or eligibility..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="scholarships.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($search !== ''): ?>
        <p class="search-result-note">
            Showing results for "<?php echo htmlspecialchars($search); ?>" — <?php echo mysqli_num_rows($scholarships); ?> found.
        </p>
    <?php endif; ?>


    <!-- ALL / SEARCH RESULTS -->
    <h2><?php echo $search !== '' ? 'Search Results' : 'All Scholarships'; ?></h2>

    <?php if (mysqli_num_rows($scholarships) > 0): ?>
        <div class="grid">

            <?php while($s = mysqli_fetch_assoc($scholarships)): ?>

            <div class="card">

                <h3><?php echo htmlspecialchars($s['title']); ?></h3>

                <span class="badge">Deadline: <?php echo date("d M Y", strtotime($s['deadline'])); ?></span>

                <p><?php echo htmlspecialchars($s['description']); ?></p>

                <div class="meta">
                    <strong>Eligibility:</strong> <?php echo htmlspecialchars($s['eligibility']); ?>
                </div>

                <div class="criteria-row">
                    <?php if (!empty($s['min_kcse_grade'])): ?>
                        <span class="criteria-chip">Min KCSE: <?php echo htmlspecialchars($s['min_kcse_grade']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($s['min_gpa'])): ?>
                        <span class="criteria-chip">Min GPA: <?php echo htmlspecialchars($s['min_gpa']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($s['income_level']) && $s['income_level'] !== 'any'): ?>
                        <span class="criteria-chip"><?php echo htmlspecialchars(ucfirst($s['income_level'])); ?> income</span>
                    <?php endif; ?>
                    <?php if (!empty($s['focus_areas'])): ?>
                        <span class="criteria-chip"><?php echo htmlspecialchars(ucwords(str_replace(['_', ','], [' ', ', '], $s['focus_areas']))); ?></span>
                    <?php endif; ?>
                </div>

                <?php if(in_array($s['id'], $applied_ids)): ?>
                    <div class="btn applied">Already Applied</div>
                <?php else: ?>
                    <a class="btn apply" href="apply.php?scholarship_id=<?php echo $s['id']; ?>">Apply Now</a>
                <?php endif; ?>

            </div>

            <?php endwhile; ?>

        </div>
    <?php else: ?>
        <div class="empty-state">
            <?php echo $search !== '' ? 'No scholarships match your search.' : 'No scholarships are currently available.'; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
<?php
session_start();
include("../config/db.php");
include("../matching_helpers.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id'"));

$applications = mysqli_query($conn, "
    SELECT a.id, s.title, a.status, a.documents
    FROM applications a
    JOIN scholarships s ON a.scholarship_id = s.id
    WHERE a.user_id='$student_id'
    ORDER BY a.applied_at DESC
");

$totalScholarships = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships WHERE status='Approved' AND deadline >= CURDATE()"))['total'];
$totalApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id'"))['total'];
$approvedApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Approved'"))['total'];
$rejectedApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Rejected'"))['total'];
$pendingApplications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM applications WHERE user_id='$student_id' AND status='Pending'"))['total'];

/* ── PERSONALIZED MATCHING ────────────────────────────────────────────
   Same logic as scholarships.php but capped at 4 for the dashboard.
   Shows scholarships matching the student's KCSE grade / GPA,
   income level, and activities. Falls back to a "browse all" nudge
   if the student hasn't filled in their matching profile yet.
*/
$hasMatchingProfile = !empty($student['kcse_grade']) || !empty($student['gpa'])
    || !empty($student['income_level']) || !empty($student['activities']);

$recommended = [];

if ($hasMatchingProfile) {
    $student_kcse_points = !empty($student['kcse_grade']) ? kcse_grade_points($student['kcse_grade']) : null;
    $student_gpa         = is_numeric($student['gpa']) ? floatval($student['gpa']) : null;
    $student_income      = $student['income_level'] ?? null;
    $student_activities  = activities_to_array($student['activities'] ?? '');

    $allOpen = mysqli_query($conn, "SELECT * FROM scholarships WHERE status='Approved' AND deadline >= CURDATE() ORDER BY deadline ASC");

    $scored = [];

    while ($s = mysqli_fetch_assoc($allOpen)) {
        $score    = 0;
        $possible = 0;
        $hardFail = false; // hard fail = student explicitly doesn't meet a requirement they CAN be evaluated against

        /* ── ACADEMIC (worth 3 points, hard fail if below minimum) ── */
        $kcseReq = !empty($s['min_kcse_grade']);
        $gpaReq  = !empty($s['min_gpa']);

        if ($kcseReq || $gpaReq) {
            $possible += 3;
            $meetsKcse = null;
            $meetsGpa  = null;

            if ($kcseReq && $student_kcse_points !== null) {
                $meetsKcse = ($student_kcse_points >= kcse_grade_points($s['min_kcse_grade']));
            }
            if ($gpaReq && $student_gpa !== null) {
                $meetsGpa = ($student_gpa >= floatval($s['min_gpa']));
            }

            if ($meetsKcse === true || $meetsGpa === true) {
                $score += 3; // meets at least one evaluable academic criterion
            } elseif ($meetsKcse === false || $meetsGpa === false) {
                $hardFail = true; // explicitly fails a requirement we could actually check
            }
            // if both are null (no data to compare), score 0 but don't hard-fail
        }

        if ($hardFail) continue; // grade too low — never recommend

        /* ── INCOME (worth 2 points, soft — missing info doesn't disqualify) ── */
        if (!empty($s['income_level']) && $s['income_level'] !== 'any') {
            $possible += 2;
            if ($student_income !== null && $student_income === $s['income_level']) {
                $score += 2;
            }
            // if student hasn't set income, score 0 but don't hard-fail
        }

        /* ── ACTIVITIES (worth 2 points, soft — no overlap just means lower score) ── */
        $focusAreas = activities_to_array($s['focus_areas'] ?? '');
        if (!empty($focusAreas)) {
            $possible += 2;
            if (activities_overlap($student_activities, $focusAreas)) {
                $score += 2;
            }
        }

        /* ── THRESHOLD: recommend if score > 0 OR no criteria were set at all ── */
        if ($possible === 0 || $score > 0) {
            $s['_score'] = $score;
            $s['_possible'] = $possible;
            $scored[] = $s;
        }
    }

    /* Sort by score descending (best match first), then by deadline */
    usort($scored, function($a, $b) {
        if ($b['_score'] !== $a['_score']) {
            return $b['_score'] - $a['_score'];
        }
        return strtotime($a['deadline']) - strtotime($b['deadline']);
    });

    $recommended = array_slice($scored, 0, 4);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --blue-50:#E6F1FB;
    --amber-200:#EF9F27;
    --gray-900:#2C2C2A;
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

.logout {
    color:white;
    text-decoration:none;
    font-size:14px;
    padding:8px 14px;
    border:1px solid rgba(255,255,255,0.2);
    border-radius:6px;
}

.logout:hover {
    background:rgba(255,255,255,0.1);
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
    transition:0.2s;
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

.sidebar .logout {
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,0.1);
    padding-top:12px;
}

/* MAIN */
.container {
    margin-top:80px;
    margin-left:250px;
    padding:24px;
    max-width:1200px;
}

/* CARDS */
.cards {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:16px;
    margin-bottom:30px;
}

.card {
    background:white;
    padding:18px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    border-left:4px solid var(--blue-600);
}

.card h3 {
    font-size:13px;
    color:#666;
}

.card p {
    font-size:26px;
    font-weight:700;
    margin-top:8px;
    color:var(--blue-900);
}

/* TABLES */
h2 {
    font-family:'Playfair Display', serif;
    margin:20px 0 10px;
    color:var(--blue-900);
}

table {
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

th {
    background:var(--blue-900);
    color:white;
    text-align:left;
    padding:12px;
    font-size:13px;
}

td {
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

tr:hover {
    background:#fafafa;
}

/* BUTTON */
.apply {
    background:var(--amber-200);
    color:var(--blue-900);
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.apply:hover { background:#f7b224; }

/* STATUS */
.status {
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.pending  { background:#fff3cd; color:#856404; }
.approved { background:#d4edda; color:#155724; }
.rejected { background:#f8d7da; color:#721c24; }

/* RECOMMENDED SECTION */
.nudge-box {
    background: var(--blue-50);
    border: 1px solid var(--blue-100, #B5D4F4);
    border-radius: 12px;
    padding: 18px 22px;
    font-size: 14px;
    color: var(--blue-900);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
}

.nudge-link {
    background: var(--blue-800, #0C447C);
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.nudge-link:hover { background: var(--blue-600); }

.nudge-link-inline {
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
}

.nudge-link-inline:hover { text-decoration: underline; }

.rec-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 16px;
    margin-bottom: 12px;
}

.rec-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    border-left: 4px solid var(--amber-200);
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
}

.rec-match-tag {
    position: absolute;
    top: 14px;
    right: 14px;
    background: var(--amber-200);
    color: var(--blue-900);
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 3px 9px;
    border-radius: 20px;
}

.rec-card h3 {
    font-size: 15px;
    font-weight: 600;
    color: var(--blue-900);
    padding-right: 54px;
}

.rec-card p {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
}

.rec-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.chip {
    font-size: 11px;
    background: #eee;
    color: #555;
    padding: 3px 9px;
    border-radius: 12px;
    font-weight: 500;
}

.chip-amber {
    background: var(--amber-200);
    color: var(--blue-900);
    font-weight: 600;
}

.rec-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.deadline {
    font-size: 12px;
    color: #888;
}

.see-all-link {
    font-size: 13.5px;
    margin-bottom: 24px;
}

.see-all-link a {
    color: var(--blue-600);
    text-decoration: none;
    font-weight: 500;
}

.see-all-link a:hover { text-decoration: underline; }

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>Welcome, <?php echo htmlspecialchars($student['full_name']); ?></h1>
    <a class="logout" href="../auth/logout.php">Logout</a>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="dot"></div>
        <span>Student Portal</span>
    </div>

    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility</a>

    <a href="../auth/logout.php" class="logout">Logout</a>
</div>

<!-- MAIN -->
<div class="container">

    <div class="cards">
        <div class="card">
            <h3>Open Scholarships</h3>
            <p><?php echo $totalScholarships; ?></p>
        </div>

        <div class="card">
            <h3>Applications</h3>
            <p><?php echo $totalApplications; ?></p>
        </div>

        <div class="card">
            <h3>Approved</h3>
            <p><?php echo $approvedApplications; ?></p>
        </div>

        <div class="card">
            <h3>Pending</h3>
            <p><?php echo $pendingApplications; ?></p>
        </div>

        <div class="card">
            <h3>Rejected</h3>
            <p><?php echo $rejectedApplications; ?></p>
        </div>
    </div>

    <h2>Recommended for You</h2>

    <?php if (!$hasMatchingProfile): ?>
        <div class="nudge-box">
            <p>✏️ <strong>Complete your profile</strong> to get personalized scholarship recommendations based on your KCSE grade, GPA, income level, and activities.</p>
            <a href="profile.php" class="nudge-link">Update Profile →</a>
        </div>

    <?php elseif (empty($recommended)): ?>
        <div class="nudge-box">
            <p>No scholarships matched your profile right now. <a href="scholarships.php" class="nudge-link-inline">Browse all scholarships</a> to find one to apply for.</p>
        </div>

    <?php else: ?>
        <div class="rec-grid">
            <?php foreach ($recommended as $s): ?>
            <div class="rec-card">
                <div class="rec-match-tag">Match</div>
                <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                <p><?php echo htmlspecialchars($s['description']); ?></p>
                <div class="rec-chips">
                    <?php if (!empty($s['min_kcse_grade'])): ?>
                        <span class="chip">Min KCSE: <?php echo htmlspecialchars($s['min_kcse_grade']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($s['min_gpa'])): ?>
                        <span class="chip">Min GPA: <?php echo htmlspecialchars($s['min_gpa']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($s['income_level']) && $s['income_level'] !== 'any'): ?>
                        <span class="chip"><?php echo htmlspecialchars(ucfirst($s['income_level'])); ?> income</span>
                    <?php endif; ?>
                    <?php foreach (activities_to_array($s['focus_areas'] ?? '') as $area): ?>
                        <span class="chip chip-amber"><?php echo htmlspecialchars(activity_options()[$area] ?? $area); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="rec-footer">
                    <span class="deadline">Deadline: <?php echo date("d M Y", strtotime($s['deadline'])); ?></span>
                    <a class="apply" href="apply.php?scholarship_id=<?php echo $s['id']; ?>">Apply</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <p class="see-all-link"><a href="scholarships.php">Browse all available scholarships →</a></p>
    <?php endif; ?>

    <h2>My Applications</h2>
    <table>
        <tr>
            <th>Scholarship</th>
            <th>Status</th>
            <th>Documents</th>
        </tr>

        <?php while($a = mysqli_fetch_assoc($applications)){ ?>
        <tr>
            <td><?php echo htmlspecialchars($a['title']); ?></td>
            <td>
                <span class="status <?php echo strtolower($a['status']); ?>">
                    <?php echo $a['status']; ?>
                </span>
            </td>
            <td>
                <?php if($a['documents']){ ?>
                    <a href="../student/<?php echo $a['documents']; ?>">View</a>
                <?php } else { echo "None"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>
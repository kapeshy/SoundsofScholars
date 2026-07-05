<?php
session_start();
include("../config/db.php");
include("../matching_helpers.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: ../auth/login.php");
    exit();
}

$donor_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $eligibility = mysqli_real_escape_string($conn, $_POST['eligibility']);
    $deadline = $_POST['deadline'];

    /* Matching criteria — used for "Recommended for you" on the student side */

    /* Academic: a scholarship can require a minimum KCSE grade, a minimum GPA,
       both, or neither. Leave blank for "no requirement". */
    $min_kcse_grade = in_array($_POST['min_kcse_grade'], kcse_grade_options(), true)
        ? $_POST['min_kcse_grade'] : null;
    $min_kcse_sql = $min_kcse_grade ? "'" . mysqli_real_escape_string($conn, $min_kcse_grade) . "'" : 'NULL';

    $min_gpa = ($_POST['min_gpa'] !== '') ? floatval($_POST['min_gpa']) : null;
    $min_gpa_sql = is_numeric($min_gpa) ? "'$min_gpa'" : 'NULL';

    $income_level = mysqli_real_escape_string($conn, $_POST['income_level']);

    /* Activities/talents this scholarship is aimed at (e.g. sports, leadership) */
    $focus_areas = isset($_POST['focus_areas']) ? activities_to_csv($_POST['focus_areas']) : '';
    $focus_areas_sql = "'" . mysqli_real_escape_string($conn, $focus_areas) . "'";

    mysqli_query($conn, "
        INSERT INTO scholarships (donor_id, title, description, eligibility, deadline, min_kcse_grade, min_gpa, income_level, focus_areas, status)
        VALUES ('$donor_id', '$title', '$description', '$eligibility', '$deadline', $min_kcse_sql, $min_gpa_sql, '$income_level', $focus_areas_sql, 'Pending')
    ");

    $success = "Scholarship submitted successfully and is pending approval.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Scholarship | SoundsOfScholars</title>
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
.wrapper {
    margin-left: 230px;
    margin-top: 64px;
    padding: 32px;
    max-width: 900px;
}

/* ─── TOP BANNER ─── */
.impact-box {
    background: linear-gradient(135deg, var(--blue-900) 0%, var(--blue-800) 60%, #1a5e8c 100%);
    color: white;
    padding: 26px 28px;
    border-radius: 14px;
    margin-bottom: 24px;
}

.impact-box h2 {
    font-family: 'Playfair Display', serif;
    font-size: 21px;
    font-weight: 700;
    margin-bottom: 8px;
}

.impact-box p {
    font-size: 14px;
    color: rgba(255,255,255,0.8);
    line-height: 1.6;
}

/* ─── CARD ─── */
.card {
    background: white;
    padding: 30px 32px;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(4,44,83,0.06);
}

.card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 19px;
    font-weight: 700;
    color: var(--blue-900);
    margin-bottom: 20px;
}

/* ─── SUCCESS ─── */
.success {
    background: var(--teal-50);
    border: 1px solid var(--teal-400);
    color: var(--teal-600);
    padding: 14px 18px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* ─── FORM ─── */
.form-group {
    margin-bottom: 18px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

label {
    font-weight: 600;
    font-size: 13px;
    color: var(--gray-900);
    display: block;
    margin-bottom: 6px;
}

input, textarea, select {
    width: 100%;
    padding: 11px 14px;
    border-radius: 8px;
    border: 1px solid var(--gray-100);
    background: var(--gray-50);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--gray-900);
    transition: border-color 0.2s, background 0.2s;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

input:focus, textarea:focus, select:focus {
    border-color: var(--blue-400);
    background: white;
    outline: none;
}

.field-hint {
    font-size: 12px;
    color: var(--gray-200);
    margin-top: -8px;
    margin-bottom: 18px;
    line-height: 1.5;
}

.optional-note {
    font-weight: 400;
    color: var(--gray-200);
    font-size: 12px;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 10px;
    margin-top: 8px;
    margin-bottom: 8px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 400;
    color: var(--gray-900);
    background: var(--gray-50);
    border: 1px solid var(--gray-100);
    border-radius: 8px;
    padding: 9px 12px;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}

.checkbox-label:hover {
    border-color: var(--blue-400);
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
    accent-color: var(--blue-600);
}

/* ─── BUTTON ─── */
button {
    width: 100%;
    padding: 13px;
    background: var(--blue-800);
    color: white;
    border: none;
    border-radius: 8px;
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.2s, transform 0.15s;
}

button:hover {
    background: var(--blue-600);
    transform: translateY(-1px);
}

/* ─── FOOTNOTE ─── */
.note {
    text-align: center;
    margin-top: 16px;
    font-size: 13px;
    color: var(--gray-200);
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    header { left: 200px; }
    .wrapper { margin-left: 200px; padding: 20px; }
    .card { padding: 22px 20px; }
    .form-row { grid-template-columns: 1fr; }
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
        <a href="create_scholarship.php" class="active">Create Scholarship</a>
        <a href="my_scholarships.php">My Scholarships</a>
        <a href="view_applicants.php">View Applicants</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
</div>

<!-- HEADER -->
<header>
    Create Scholarship
</header>

<div class="wrapper">

    <!-- IMPACT BANNER -->
    <div class="impact-box">
        <h2>Thank you for supporting education</h2>
        <p>
            Every scholarship you create gives a student a chance to change their future.
        </p>
    </div>

    <!-- FORM -->
    <div class="card">

        <h3>Scholarship Details</h3>

        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>Eligibility</label>
                <textarea name="eligibility" placeholder="Describe any other requirements (e.g. leadership skills, community involvement, area of study)..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Minimum KCSE Grade</label>
                    <?php echo render_kcse_select('min_kcse_grade'); ?>
                </div>

                <div class="form-group">
                    <label>Minimum GPA <span class="optional-note">(university / A-levels / IB)</span></label>
                    <input type="number" name="min_gpa" step="0.1" min="0" max="4" placeholder="e.g. 3.5 (leave blank if none)">
                </div>
            </div>

            <p class="field-hint">A student matches academically if they meet <strong>either</strong> requirement that applies to them — KCSE grade or GPA, whichever they have on their profile.</p>

            <div class="form-group">
                <label>Target Income Level</label>
                <select name="income_level">
                    <option value="any" selected>Any (no restriction)</option>
                    <option value="low">Low income</option>
                    <option value="medium">Medium income</option>
                    <option value="high">High income</option>
                </select>
            </div>

            <div class="form-group">
                <label>Focus Areas <span class="optional-note">(optional — select any that apply)</span></label>
                <div class="checkbox-grid">
                    <?php echo render_activity_checkboxes(); ?>
                </div>
                <p class="field-hint">If selected, students with matching activities/talents on their profile will see this as a recommended match — even if their grades are average.</p>
            </div>

            <div class="form-group">
                <label>Deadline</label>
                <input type="date" name="deadline" required>
            </div>

            <button name="submit">Create Scholarship</button>
        </form>

        <div class="note">
             Your contribution helps shape futures.
        </div>

    </div>

</div>

</body>
</html>
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

$message = "";

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    /* Matching attributes — used to power "Recommended for you" on scholarships.php */
    $kcse_grade = in_array($_POST['kcse_grade'], kcse_grade_options(), true) ? $_POST['kcse_grade'] : null;
    $kcse_grade_sql = $kcse_grade ? "'" . mysqli_real_escape_string($conn, $kcse_grade) . "'" : 'NULL';

    $gpa = ($_POST['gpa'] !== '') ? floatval($_POST['gpa']) : null;
    $gpa_sql = is_numeric($gpa) ? "'$gpa'" : 'NULL';

    $income_level = mysqli_real_escape_string($conn, $_POST['income_level']);
    $income_level_sql = ($income_level !== '') ? "'$income_level'" : 'NULL';

    $activities = isset($_POST['activities']) ? activities_to_csv($_POST['activities']) : '';
    $activities_sql = "'" . mysqli_real_escape_string($conn, $activities) . "'";

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "
            UPDATE users 
            SET full_name='$name', email='$email', password='$password',
                kcse_grade=$kcse_grade_sql, gpa=$gpa_sql, income_level=$income_level_sql, activities=$activities_sql
            WHERE id='$student_id'
        ");
    } else {
        mysqli_query($conn, "
            UPDATE users 
            SET full_name='$name', email='$email',
                kcse_grade=$kcse_grade_sql, gpa=$gpa_sql, income_level=$income_level_sql, activities=$activities_sql
            WHERE id='$student_id'
        ");
    }

    $message = "Profile updated successfully!";
    $student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$student_id'"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | SoundsOfScholars</title>

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
    max-width:800px;
}

/* CARD */
.card {
    background:white;
    padding:30px;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,0.06);
}

h2 {
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:10px;
}

p.subtitle {
    font-size:14px;
    color:var(--gray-600);
    margin-bottom:20px;
}

/* FORM */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.section-divider {
    margin: 26px 0 16px;
    padding-top: 18px;
    border-top: 1px solid var(--gray-100);
}

.section-divider h3 {
    font-size: 15px;
    font-weight: 600;
    color: var(--blue-900);
    margin-bottom: 4px;
}

.section-divider p {
    font-size: 12.5px;
    color: var(--gray-200);
    margin-bottom: 0;
}

label {
    font-size:13px;
    font-weight: 600;
    color:var(--gray-900);
    display:block;
    margin-bottom:6px;
    margin-top:14px;
}

input, select {
    width:100%;
    padding:12px;
    border:1px solid var(--gray-100);
    border-radius:8px;
    font-size:14px;
    font-family: 'DM Sans', sans-serif;
    background: white;
    outline:none;
}

input:focus, select:focus {
    border-color:var(--blue-400);
}

.optional-note {
    font-weight: 400;
    color: var(--gray-200);
    font-size: 12px;
}

.field-hint {
    font-size: 12px;
    color: var(--gray-200);
    margin-top: 4px;
    margin-bottom: 18px;
    line-height: 1.5;
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
    background: var(--gray-50, #f4f6f8);
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

/* BUTTON */
button {
    margin-top:22px;
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:var(--amber-200);
    color:var(--blue-900);
    font-weight:600;
    font-size:15px;
    cursor:pointer;
}

button:hover {
    background:#f7b224;
}

/* MESSAGE */
.message {
    background: var(--teal-50);
    color: var(--teal-600);
    border: 1px solid var(--teal-400);
    padding:10px 14px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .sidebar { width: 200px; }
    .topbar { left: 0; }
    .container { margin-left: 200px; padding: 16px; }
    .form-row { grid-template-columns: 1fr; }
}

</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <h1>My Profile</h1>
    <a class="logout" href="../auth/logout.php">Logout</a>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">
        <div class="dot"></div>
        <span>Student Portal</span>
    </div>

    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php" class="active">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
    <a href="eligibility.php">Eligibility</a>
</div>

<!-- MAIN -->
<div class="container">

    <div class="card">
        <h2>Update Profile</h2>
        <p class="subtitle">Keep your personal information up to date</p>

        <?php if($message) echo "<div class='message'>" . htmlspecialchars($message) . "</div>"; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password">

            <div class="section-divider">
                <h3>Matching Information</h3>
                <p>Used to show you "Recommended for you" scholarships you're more likely to qualify for.</p>
            </div>

            <div class="form-row">
                <div>
                    <label>KCSE Grade <span class="optional-note">(if applicable)</span></label>
                    <?php echo render_kcse_select('kcse_grade', $student['kcse_grade'] ?? ''); ?>
                </div>

                <div>
                    <label>Current GPA <span class="optional-note">(university / A-levels / IB)</span></label>
                    <input type="number" name="gpa" step="0.1" min="0" max="4"
                        value="<?php echo htmlspecialchars($student['gpa'] ?? ''); ?>"
                        placeholder="e.g. 3.7">
                </div>
            </div>

            <label>Household Income Level</label>
            <select name="income_level">
                <option value="">Prefer not to say</option>
                <?php
                $current = $student['income_level'] ?? '';
                $levels = ['low' => 'Low income', 'medium' => 'Medium income', 'high' => 'High income'];
                foreach ($levels as $val => $label):
                ?>
                    <option value="<?php echo $val; ?>" <?php echo $current === $val ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Activities &amp; Talents <span class="optional-note">(select all that apply)</span></label>
            <div class="checkbox-grid">
                <?php echo render_activity_checkboxes(activities_to_array($student['activities'] ?? '')); ?>
            </div>
            <p class="field-hint">These help match you to scholarships focused on specific talents or involvement — not just grades.</p>

            <button type="submit" name="update">Update Profile</button>
        </form>
    </div>

</div>

</body>
</html>
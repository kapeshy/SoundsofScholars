<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

if(!isset($_GET['scholarship_id'])){
    header("Location: dashboard.php");
    exit();
}

$scholarship_id = intval($_GET['scholarship_id']);

$scholarship = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM scholarships WHERE id='$scholarship_id'"
));

if(!$scholarship){
    die("Scholarship not found.");
}

$message = "";
$error = "";

// prevent duplicate application
$check = mysqli_query($conn,
    "SELECT id FROM applications WHERE user_id='$student_id' AND scholarship_id='$scholarship_id'"
);

if(mysqli_num_rows($check) > 0){
    $error = "You already applied for this scholarship.";
}

// handle submit
if(isset($_POST['apply']) && $error == ""){

    $background = mysqli_real_escape_string($conn, $_POST['background']);
    $gpa = mysqli_real_escape_string($conn, $_POST['gpa']);

    $documents = "";

    if(isset($_FILES['transcript']) && $_FILES['transcript']['error'] == 0){
        $ext = pathinfo($_FILES['transcript']['name'], PATHINFO_EXTENSION);
        $filename = "uploads/transcript_".$student_id."_".time().".".$ext;

        if(!is_dir("uploads")){
            mkdir("uploads", 0777, true);
        }

        move_uploaded_file($_FILES['transcript']['tmp_name'], $filename);
        $documents = $filename;
    }

    mysqli_query($conn, "
        INSERT INTO applications (user_id, scholarship_id, background, gpa, documents)
        VALUES ('$student_id','$scholarship_id','$background','$gpa','$documents')
    ");

    $message = "Application submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Apply | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root{
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --amber:#EF9F27;
    --gray:#f4f6f8;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'DM Sans', sans-serif;
}

/* TOP BAR */
.topbar{
    position:fixed;
    top:0;
    left:0;
    right:0;
    height:64px;
    background:var(--blue-900);
    color:white;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 24px;
    z-index:1000;
}

.topbar h1{
    font-family:'Playfair Display', serif;
    font-size:18px;
}

/* SIDEBAR */
.sidebar{
    position:fixed;
    top:64px;
    left:0;
    width:230px;
    bottom:0;
    background:var(--blue-900);
    padding:20px 14px;
}

.sidebar a{
    display:block;
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    padding:10px;
    border-radius:8px;
    font-size:14px;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.08);
    color:white;
}

/* MAIN */
.container{
    margin-left:250px;
    margin-top:80px;
    padding:24px;
    max-width:900px;
}

/* LAYOUT */
.grid{
    display:grid;
    grid-template-columns:1fr;
    gap:20px;
}

/* INFO CARD */
.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
}

.card h2{
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:10px;
}

.meta p{
    margin:6px 0;
    color:#555;
    font-size:14px;
}

/* FORM */
label{
    font-size:13px;
    font-weight:600;
    color:#333;
}

input, textarea{
    width:100%;
    padding:12px;
    margin-top:6px;
    margin-bottom:16px;
    border:1px solid #ddd;
    border-radius:8px;
    font-size:14px;
}

textarea{
    min-height:120px;
    resize:none;
}

button{
    width:100%;
    padding:12px;
    background:var(--amber);
    border:none;
    color:var(--blue-900);
    font-weight:600;
    border-radius:8px;
    cursor:pointer;
    font-size:15px;
}

button:hover{
    opacity:0.9;
}

/* MESSAGES */
.success{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}

.error{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    border-radius:8px;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="topbar">
    <h1>Apply for Scholarship</h1>
</div>

<div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="scholarships.php">Scholarships</a>
    <a href="my_applications.php">My Applications</a>
</div>

<div class="container">

    <div class="grid">

        <!-- SCHOLARSHIP INFO -->
        <div class="card">
            <h2><?php echo htmlspecialchars($scholarship['title']); ?></h2>

            <div class="meta">
                <p><strong>Description:</strong> <?php echo htmlspecialchars($scholarship['description']); ?></p>
                <p><strong>Eligibility:</strong> <?php echo htmlspecialchars($scholarship['eligibility']); ?></p>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($scholarship['deadline']); ?></p>
            </div>
        </div>

        <!-- MESSAGES -->
        <?php if($message): ?>
            <div class="success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- FORM -->
        <?php if(!$message && !$error): ?>
        <div class="card">
            <h2>Application Form</h2>

            <form method="POST" enctype="multipart/form-data">

                <label>Tell us about yourself</label>
                <textarea name="background" required></textarea>

                <label>Academic Score / GPA</label>
                <input type="text" name="gpa" required>

                <label>Upload Supporting Document</label>
                <input type="file" name="transcript" accept=".pdf,.doc,.docx,.jpg,.png" required>

                <button name="apply">Submit Application</button>
            </form>
        </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
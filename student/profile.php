<?php
session_start();
include("../config/db.php");

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

    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "
            UPDATE users 
            SET full_name='$name', email='$email', password='$password' 
            WHERE id='$student_id'
        ");
    } else {
        mysqli_query($conn, "
            UPDATE users 
            SET full_name='$name', email='$email' 
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
<title>My Profile | SoundsOfScholars</title>

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
    color:#666;
    margin-bottom:20px;
}

/* FORM */
label {
    font-size:13px;
    color:#555;
    display:block;
    margin-bottom:6px;
    margin-top:10px;
}

input {
    width:100%;
    padding:12px;
    border:1px solid var(--gray-100);
    border-radius:8px;
    font-size:14px;
    outline:none;
}

input:focus {
    border-color:var(--blue-600);
}

/* BUTTON */
button {
    margin-top:18px;
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
    background:#e7f7ec;
    color:#1e7e34;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
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

        <?php if($message) echo "<div class='message'>$message</div>"; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo $student['full_name']; ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo $student['email']; ?>" required>

            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password">

            <button type="submit" name="update">Update Profile</button>
        </form>
    </div>

</div>

</body>
</html>
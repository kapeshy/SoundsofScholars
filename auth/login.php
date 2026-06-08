<?php
session_start();
include("../config/db.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] == 'donor') {
            header("Location: ../donor/dashboard.php");
        } else {
            header("Location: ../student/dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | SoundsOfScholars</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --blue-900:#042C53;
    --blue-800:#0C447C;
    --blue-600:#185FA5;
    --blue-50:#E6F1FB;
    --amber-200:#EF9F27;
    --gray-900:#2C2C2A;
    --gray-200:#B4B2A9;
    --gray-100:#D3D1C7;
    --gray-50:#F1EFE8;
}

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'DM Sans', sans-serif;
}

body {
    background: linear-gradient(135deg, var(--blue-900), var(--blue-800));
    min-height: 100vh;
    display:flex;
    flex-direction:column;
}

/* simple header like landing page */
.topbar {
    padding: 18px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo {
    font-family:'Playfair Display', serif;
    color:white;
    font-size:20px;
    text-decoration:none;
    font-weight:700;
}

.back-link {
    color:rgba(255,255,255,0.8);
    text-decoration:none;
    font-size:14px;
}

.back-link:hover {
    color:white;
}

/* login box */
.wrapper {
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:20px;
}

.card {
    background:white;
    width:100%;
    max-width:420px;
    border-radius:14px;
    padding:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

.card h2 {
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:8px;
    font-size:28px;
}

.card p {
    color:var(--gray-900);
    font-size:20px;
    margin-bottom:20px;
    opacity:0.7;
}

.error {
    background:#ffe5e5;
    color:#b00020;
    padding:10px;
    border-radius:8px;
    font-size:13px;
    margin-bottom:15px;
}

input {
    width:100%;
    padding:12px 14px;
    margin-bottom:12px;
    border:1px solid var(--gray-100);
    border-radius:8px;
    font-size:15px;
    outline:none;
}

input:focus {
    border-color:var(--blue-600);
}

button {
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

.bottom {
    margin-top:15px;
    text-align:center;
    font-size:14px;
}

.bottom a {
    color:var(--blue-600);
    text-decoration:none;
}

.bottom a:hover {
    text-decoration:underline;
}
</style>
</head>

<body>

<div class="topbar">
    <a href="../index.php" class="logo">SoundsOfScholars</a>
    <a href="../index.php" class="back-link">← Back to home</a>
</div>

<div class="wrapper">
    <div class="card">
        <p>Login to continue to your dashboard</p>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <div class="bottom">
            Don’t have an account? <a href="../auth/register.php">Create one</a>
        </div>
    </div>
</div>

</body>
</html>
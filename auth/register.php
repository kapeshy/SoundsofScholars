<?php
include("../config/db.php");

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(full_name,email,password,role)
            VALUES('$name','$email','$password','$role')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | SoundsOfScholars</title>

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
    --gray-50:#F1EFE8;
}

* {
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'DM Sans', sans-serif;
}

body {
    min-height:100vh;
    display:flex;
    flex-direction:column;
    background: linear-gradient(135deg, var(--blue-900), var(--blue-800));
}

/* top bar */
.topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 24px;
}

.logo {
    font-family:'Playfair Display', serif;
    color:white;
    text-decoration:none;
    font-size:20px;
    font-weight:700;
}

.back {
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    font-size:14px;
}

.back:hover {
    color:white;
}

/* center card */
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
    max-width:450px;
    border-radius:14px;
    padding:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

.card h2 {
    font-family:'Playfair Display', serif;
    color:var(--blue-900);
    margin-bottom:6px;
    font-size:28px;
}

.card p {
    color:var(--gray-900);
    opacity:0.7;
    font-size:14px;
    margin-bottom:20px;
}

/* inputs */
input, select {
    width:100%;
    padding:12px 14px;
    margin-bottom:12px;
    border:1px solid var(--gray-100);
    border-radius:8px;
    font-size:15px;
    outline:none;
}

input:focus, select:focus {
    border-color:var(--blue-600);
}

/* button */
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

/* error */
.error {
    background:#ffe5e5;
    color:#b00020;
    padding:10px;
    border-radius:8px;
    font-size:13px;
    margin-bottom:15px;
}

/* footer text */
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
    <a href="../index.php" class="back">← Back to home</a>
</div>

<div class="wrapper">
    <div class="card">
        <h2>Create account</h2>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="Email address" required>

            <select name="role" required>
                <option value="">Select role</option>
                <option value="student">Student</option>
                <option value="donor">Donor</option>
            </select>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="register">Create account</button>
        </form>

        <div class="bottom">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</div>

</body>
</html>
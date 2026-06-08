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
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SoundsOfScholars | Login</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #2980b9;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #e67e22;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        .login-container button:hover {
            background-color: #cf711f;
        }

        .login-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        @media(max-width: 480px){
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to SoundsOfScholars</h2>
    <?php if(isset($error)){ echo "<div class='error'>$error</div>"; } ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="../auth/register.php">Register</a></p>
</div>

</body>
</html>

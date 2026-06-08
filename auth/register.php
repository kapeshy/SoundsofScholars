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
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SoundsOfScholars | Register</title>
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

        .register-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #2980b9;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container select {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .register-container button {
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

        .register-container button:hover {
            background-color: #cf711f;
        }

        .register-container p {
            margin-top: 15px;
            font-size: 14px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        @media(max-width: 480px){
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Create Your Account</h2>
    <?php if(isset($error)){ echo "<div class='error'>$error</div>"; } ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="donor">Donor</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>

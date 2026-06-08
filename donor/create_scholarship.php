<?php
session_start();
include("../config/db.php");

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

    mysqli_query($conn, "
        INSERT INTO scholarships (donor_id, title, description, eligibility, deadline, status)
        VALUES ('$donor_id', '$title', '$description', '$eligibility', '$deadline', 'Pending')
    ");

    $success = "Thank you for your generosity. Your scholarship has been submitted for approval.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Scholarship | EduFund</title>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f8;
    margin: 0;
}

/* Header */
header {
    background-color: #2980b9;
    color: white;
    padding: 16px 25px;
    font-size: 20px;
    font-weight: bold;
}

/* Wrapper */
.wrapper {
    max-width: 820px;
    margin: 30px auto;
    padding: 0 15px;
}

/* Impact Message */
.impact-box {
    background: linear-gradient(135deg, #2980b9, #1f6391);
    color: white;
    padding: 22px 25px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.impact-box h2 {
    margin: 0;
    font-size: 22px;
}

.impact-box p {
    margin-top: 10px;
    font-size: 14.5px;
    line-height: 1.6;
}

/* Card */
.card {
    background-color: white;
    padding: 25px 30px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.card h3 {
    margin-top: 0;
    color: #2980b9;
    font-size: 20px;
}

.card small {
    color: #777;
    font-size: 13px;
}

/* Success */
.success {
    background-color: #27ae60;
    color: white;
    padding: 10px 14px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 14px;
    text-align: center;
}

/* Form */
.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 6px;
    color: #444;
}

input[type="text"],
input[type="date"],
textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

textarea {
    min-height: 80px;
    resize: vertical;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #2980b9;
}

/* Button */
button {
    width: 100%;
    padding: 13px;
    background-color: #2980b9;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background-color: #1f6391;
}

/* Appreciation footer */
.appreciation {
    margin-top: 20px;
    font-size: 13.5px;
    color: #555;
    text-align: center;
}
</style>
</head>

<body>

<header>Create Scholarships • Change Lives</header>

<div class="wrapper">

    <!-- 🌍 Impact & Appreciation -->
    <div class="impact-box">
        <h2>Thank You for Investing in Education 🎓</h2>
        <p>
            Your generosity has the power to open doors, restore hope, and transform futures.
            By creating a scholarship today, you are not just funding education —
            you are empowering dreams, reducing inequality, and shaping tomorrow’s leaders.
        </p>
    </div>

    <!-- 📄 Form Card -->
    <div class="card">

        <h3>Scholarship Information</h3>
        <small>
            Every detail you provide helps us match deserving students with life-changing opportunities.
        </small>

        <br><br>

        <?php if(isset($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Scholarship Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Scholarship Description</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>Eligibility Criteria</label>
                <textarea name="eligibility" required></textarea>
            </div>

            <div class="form-group">
                <label>Application Deadline</label>
                <input type="date" name="deadline" required>
            </div>

            <button name="submit">Create Scholarship</button>
        </form>

        <div class="appreciation">
            💙 Your support creates opportunities that last a lifetime. Thank you for making education accessible.
        </div>

    </div>

</div>

</body>
</html>

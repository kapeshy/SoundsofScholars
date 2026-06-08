<?php
session_start();
include("../config/db.php");

// Only students
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Get scholarship ID
if(!isset($_GET['scholarship_id'])){
    header("Location: dashboard.php");
    exit();
}

$scholarship_id = intval($_GET['scholarship_id']);

// Fetch scholarship info WITHOUT checking status
$scholarship = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM scholarships WHERE id='$scholarship_id'"));

if(!$scholarship){
    die("Scholarship not found."); // Keep this only if the ID is invalid
}

$message = "";

// Check if student already applied
$check = mysqli_query($conn, "SELECT * FROM applications WHERE user_id='$student_id' AND scholarship_id='$scholarship_id'");
if(mysqli_num_rows($check) > 0){
    $message = "You have already applied for this scholarship.";
}

// Handle form submission
if(isset($_POST['apply']) && $message == ""){
    $background = mysqli_real_escape_string($conn, $_POST['background']);
    $gpa = mysqli_real_escape_string($conn, $_POST['gpa']);
    
    // Handle file upload
    $documents = "";
    if(isset($_FILES['transcript']) && $_FILES['transcript']['error'] == 0){
        $ext = pathinfo($_FILES['transcript']['name'], PATHINFO_EXTENSION);
        $filename = "uploads/transcript_".$student_id."_".time().".".$ext;
        if(!is_dir("uploads")) mkdir("uploads", 0777, true);
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
<title>Apply Scholarship | EduFund</title>
<style>
body {font-family: Arial; background:#f4f6f8; margin:0; padding:0;}
.container {max-width:700px; margin:50px auto; background:#fff; padding:40px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 {color:#2980b9; margin-bottom:20px;}
p {font-size:16px; line-height:1.6;}
form input, form textarea, form button {width:100%; padding:12px 15px; margin-bottom:20px; border-radius:6px; border:1px solid #ccc; font-size:16px; box-sizing:border-box;}
form textarea {min-height:100px;}
form button {background:#2980b9; color:#fff; border:none; font-size:18px; cursor:pointer; transition:0.3s;}
form button:hover {background:#1f6391;}
.message {background:#2ecc71; color:#fff; padding:12px 20px; border-radius:6px; margin-bottom:20px; text-align:center;}
.error {background:#e74c3c; color:#fff; padding:12px 20px; border-radius:6px; margin-bottom:20px; text-align:center;}
</style>
</head>
<body>

<div class="container">
<h2>Apply for: <?php echo htmlspecialchars($scholarship['title']); ?></h2>
<p><strong>Description:</strong> <?php echo htmlspecialchars($scholarship['description']); ?></p>
<p><strong>Eligibility:</strong> <?php echo htmlspecialchars($scholarship['eligibility']); ?></p>
<p><strong>Deadline:</strong> <?php echo htmlspecialchars($scholarship['deadline']); ?></p>

<?php if($message): ?>
    <div class="<?php echo (strpos($message,'already')!==false) ? 'error' : 'message'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if($message == "" || strpos($message,'already')===false): ?>
<form method="POST" enctype="multipart/form-data">
    <textarea name="background" placeholder="Tell us about yourself, your background, and why you deserve this scholarship" required></textarea>
    <input type="text" name="gpa" placeholder="Current GPA / Academic Score" required>
    <label>Upload Transcript / Supporting Document:</label>
    <input type="file" name="transcript" accept=".pdf,.doc,.docx,.jpg,.png" required>
    <button name="apply">Submit Application</button>
</form>
<?php endif; ?>
</div>

</body>
</html>

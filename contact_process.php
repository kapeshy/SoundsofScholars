<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: contact.php");
    exit();
}

// Collect and sanitize input
$full_name = trim($_POST['full_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$role       = trim($_POST['role'] ?? '');
$subject    = trim($_POST['subject'] ?? '');
$message    = trim($_POST['message'] ?? '');

$allowed_roles = ['student', 'donor', 'admin', 'other'];
$allowed_subjects = ['application', 'donation', 'account', 'privacy', 'other'];

$errors = [];

// Validation
if ($full_name == '' || strlen($full_name) > 100) {
    $errors[] = "Please enter your full name.";
}

if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $errors[] = "Please enter a valid email address.";
}

if (!in_array($role, $allowed_roles)) {
    $errors[] = "Invalid role selected.";
}

if (!in_array($subject, $allowed_subjects)) {
    $errors[] = "Invalid subject selected.";
}

if ($message == '' || strlen($message) > 5000) {
    $errors[] = "Please enter your message.";
}

// Optional spam check
if (preg_match('/(https?:\/\/[^\s]+){3,}/i', $message)) {
    $errors[] = "Spam detected.";
}

// If validation fails
if (!empty($errors)) {

    $_SESSION['contact_errors'] = $errors;

    $_SESSION['contact_old'] = [
        'full_name' => $full_name,
        'email' => $email,
        'role' => $role,
        'subject' => $subject,
        'message' => $message
    ];

    header("Location: contact.php?status=error");
    exit();
}

// Insert into database
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO contact_messages
    (full_name, email, role, subject, message, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())"
);

if (!$stmt) {
    error_log(mysqli_error($conn));
    header("Location: contact.php?status=server_error");
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $full_name,
    $email,
    $role,
    $subject,
    $message
);

if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    unset($_SESSION['contact_errors']);
    unset($_SESSION['contact_old']);

    header("Location: contact.php?status=success");
    exit();

} else {

    error_log(mysqli_error($conn));

    $_SESSION['contact_old'] = [
        'full_name' => $full_name,
        'email' => $email,
        'role' => $role,
        'subject' => $subject,
        'message' => $message
    ];

    header("Location: contact.php?status=server_error");
    exit();
}
?>
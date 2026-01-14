<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) {
    die("Connection Error");
}

$email = $_POST['email'];
$password = $_POST['password'];

if ($email === 'admin@bank.com' && $password === 'admin123') {
    $_SESSION['email'] = $email;
    header("Location: ../admin/dashboard.php");
    exit;
}
$query = "SELECT * FROM user_accounts WHERE Email = '$email'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if ($password === $user['Password']) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['email'] = $user['Email'];

        if ($user['Status'] === 'Approved') {
        // Normal flow
        header("Location: ../user/user_dashboard.php");
    } else {
        // Redirect to Landing Page (Pending or Rejected)
        header("Location: ../php/account_status.php");
    } exit;
    } 
    
    
    else {
        header("Location: ../login.php?error=password");
        exit;
    }
} else {
    header("Location: ../login.php?error=notfound");
    exit;
}

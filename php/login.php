<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT ID, Email, Password, Role, Status FROM user_accounts WHERE Email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($password === $user['Password']) {
            
            // CRITICAL: Always set the session ID first so status page works
            $_SESSION['user_id'] = $user['ID'];
            $_SESSION['email']   = $user['Email'];
            $_SESSION['role']    = $user['Role'];

            // Check Status
            if ($user['Status'] === 'Pending' || $user['Status'] === 'Rejected') {
                header("Location: ../user/account_status.php");
                exit;
            }

            if ($user['Status'] === 'Approved') {
                session_regenerate_id(true);
                if ($user['Role'] === 'Admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../user/user_dashboard.php");
                }
                exit;
            }
        }
    }
    header("Location: ../login.php?error=failed");
    exit;
}
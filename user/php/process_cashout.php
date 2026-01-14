<?php
session_start(); // only here, safe

// check if user is logged in
$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    die("Invalid user. Please log in.");
}

// connect to DB
$conn = new mysqli('localhost', 'root', '', 'bank_db');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

// check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    // basic validation
    if (!$method || !$description || !$icon || $amount <= 0) {
        die("Invalid input.");
    }

    // fetch current balance
    $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $currentBalance = floatval($user['Balance']); // current balance before cash out

    // calculate new balance
    $newBalance = $currentBalance - $amount;

    // update user balance
    $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
    $stmt->bind_param("di", $newBalance, $userId);
    $stmt->execute();

    // insert transaction with balance_after
    $transactionType = 'Cash Out';
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isddss", $userId, $transactionType, $amount, $newBalance, $description, $icon);
    $stmt->execute();

    // redirect back to dashboard
    header("Location: ../user_dashboard.php?success=cashout");
    exit;
}
?>
<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bank_db');


$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    $_SESSION['send_money_error'] = "Invalid user. Please log in.";
    header("Location: ../user_dashboard.php");
    exit;
}


$recipientNumber = $_POST['recipient_number'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);


// Validate cellphone format
if (!preg_match('/^09[0-9]{9}$/', $recipientNumber)) {
    $_SESSION['send_money_error'] = "Invalid cellphone format. Use: 09XXXXXXXXX";
    header("Location: ../user_dashboard.php");
    exit;
}


if ($amount <= 0) {
    $_SESSION['send_money_error'] = "Invalid amount.";
    header("Location: ../user_dashboard.php");
    exit;
}


// Fetch sender
$stmt = $conn->prepare("SELECT Balance, FirstName, LastName FROM user_accounts WHERE ID=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$sender = $stmt->get_result()->fetch_assoc();
$stmt->close();


if (!$sender) {
    $_SESSION['send_money_error'] = "Sender account not found.";
    $conn->close();
    header("Location: ../user_dashboard.php");
    exit;
}


if ($amount > $sender['Balance']) {
    $_SESSION['send_money_error'] = "Insufficient balance. Available: ₱" . number_format($sender['Balance'], 2);
    $conn->close();
    header("Location: ../user_dashboard.php");
    exit;
}


// Fetch recipient and validate status (same logic as cash out)
$stmt = $conn->prepare("SELECT ID, Balance, FirstName, LastName, Status FROM user_accounts WHERE Phone=?");
$stmt->bind_param("s", $recipientNumber);
$stmt->execute();
$recipient = $stmt->get_result()->fetch_assoc();
$stmt->close();


if (!$recipient) {
    $_SESSION['send_money_error'] = "Recipient number not found.";
    $conn->close();
    header("Location: ../user_dashboard.php");
    exit;
}


// Check if recipient account is approved
if (strcasecmp($recipient['Status'], 'Approved') !== 0) {
    $_SESSION['send_money_error'] = "Recipient account (" . $recipient['FirstName'] . " " . $recipient['LastName'] . ") is not approved yet.";
    $conn->close();
    header("Location: ../user_dashboard.php");
    exit;
}


// Begin transaction
$conn->begin_transaction();


try {
    // Deduct sender
    $newSenderBalance = $sender['Balance'] - $amount;
    $stmt = $conn->prepare("UPDATE user_accounts SET Balance=? WHERE ID=?");
    $stmt->bind_param("di", $newSenderBalance, $userId);


    if (!$stmt->execute()) {
        throw new Exception("Failed to update sender balance");
    }
    $stmt->close();


    // Add recipient
    $newRecipientBalance = $recipient['Balance'] + $amount;
    $stmt = $conn->prepare("UPDATE user_accounts SET Balance=? WHERE ID=?");
    $stmt->bind_param("di", $newRecipientBalance, $recipient['ID']);


    if (!$stmt->execute()) {
        throw new Exception("Failed to update recipient balance");
    }
    $stmt->close();


    // Record sender transaction
    $descSender = "Sent to {$recipient['FirstName']} {$recipient['LastName']}";
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, balance_after, transaction_type, icon, created_at) VALUES (?, ?, ?, ?, 'Send Money', 'bi-send', NOW())");
    $stmt->bind_param("isdd", $userId, $descSender, $amount, $newSenderBalance);


    if (!$stmt->execute()) {
        throw new Exception("Failed to insert sender transaction");
    }
    $stmt->close();


    // Record recipient transaction
    $descRecipient = "Received from {$sender['FirstName']} {$sender['LastName']}";
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, balance_after, transaction_type, icon, created_at) VALUES (?, ?, ?, ?, 'Received Money', 'bi-currency-exchange', NOW())");
    $stmt->bind_param("isdd", $recipient['ID'], $descRecipient, $amount, $newRecipientBalance);


    if (!$stmt->execute()) {
        throw new Exception("Failed to insert recipient transaction");
    }
    $stmt->close();


    // Commit transaction
    $conn->commit();


    $_SESSION['send_money_success'] = "Successfully sent ₱" . number_format($amount, 2) . " to " . $recipient['FirstName'] . " " . $recipient['LastName'];


} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['send_money_error'] = "Transaction failed: " . $e->getMessage();
}


$conn->close();
header("Location: ../user_dashboard.php");
exit;
?>


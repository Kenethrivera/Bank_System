<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'bank_db');

$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) die("Invalid user. Please log in.");

$recipientNumber = $_POST['recipient_number'] ?? '';
$amount = floatval($_POST['amount'] ?? 0);

if (!$recipientNumber || $amount <= 0) {
    die("Invalid input.");
}

// fetch sender
$stmt = $conn->prepare("SELECT Balance, FirstName FROM user_accounts WHERE ID=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$sender = $stmt->get_result()->fetch_assoc();
if (!$sender) die("Sender not found.");

if ($amount > $sender['Balance']) die("Insufficient balance.");

// fetch recipient
$stmt = $conn->prepare("SELECT ID, Balance, FirstName FROM user_accounts WHERE Phone=?");
$stmt->bind_param("s", $recipientNumber);
$stmt->execute();
$recipient = $stmt->get_result()->fetch_assoc();
if (!$recipient) die("Recipient not found.");

// Deduct sender
$newSenderBalance = $sender['Balance'] - $amount;
$stmt = $conn->prepare("UPDATE user_accounts SET Balance=? WHERE ID=?");
$stmt->bind_param("di", $newSenderBalance, $userId);
$stmt->execute();

// Add recipient
$newRecipientBalance = $recipient['Balance'] + $amount;
$stmt = $conn->prepare("UPDATE user_accounts SET Balance=? WHERE ID=?");
$stmt->bind_param("di", $newRecipientBalance, $recipient['ID']);
$stmt->execute();

// Record sender transaction
$descSender = "Sent to {$recipient['FirstName']}";
$stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, transaction_type, icon, created_at) VALUES (?, ?, ?, 'Send Money', 'bi-send', NOW())");
$stmt->bind_param("isd", $userId, $descSender, $amount);
$stmt->execute();

// Record recipient transaction
$descRecipient = "Received from {$sender['FirstName']}";
$stmt = $conn->prepare("INSERT INTO transactions (user_id, description, amount, transaction_type, icon, created_at) VALUES (?, ?, ?, 'Received Money', 'bi-receive', NOW())");
$stmt->bind_param("isd", $recipient['ID'], $descRecipient, $amount);
$stmt->execute();

header("Location: ../user_dashboard.php?success=sendmoney");
exit;
?>

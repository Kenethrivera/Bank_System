<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "bank_db");
if ($conn->connect_error) {
    echo json_encode(['exists' => false]);
    exit;
}

$email = $_GET['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT ID FROM user_accounts WHERE Email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    'exists' => $stmt->num_rows > 0
]);

$stmt->close();
$conn->close();

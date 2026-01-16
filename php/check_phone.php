<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "bank_db");
if ($conn->connect_error) {
    echo json_encode(['exists' => false]);
    exit;
}

$phone = $_GET['phone'] ?? '';

// PH format: 09XXXXXXXXX
if (!preg_match('/^09[0-9]{9}$/', $phone)) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT ID FROM user_accounts WHERE Phone = ? LIMIT 1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    'exists' => $stmt->num_rows > 0
]);

$stmt->close();
$conn->close();

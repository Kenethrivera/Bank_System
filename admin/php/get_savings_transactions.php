<?php
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$savings_id = $_GET['savings_id'] ?? '';

$transactions = [];
$total_interest = 0;

if ($savings_id) {
    // Transactions query
    $stmt = $conn->prepare("
        SELECT created_at AS date, transaction_type AS type, amount, balance_after
        FROM savings_transactions
        WHERE savings_id = ?
        ORDER BY created_at
    ");
    $stmt->bind_param("s", $savings_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['amount'] = floatval($row['amount']);
        $row['balance_after'] = floatval($row['balance_after']);
        $transactions[] = $row;
    }

    // Total Interest Earned
    $stmt2 = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) AS total_interest
        FROM savings_transactions
        WHERE savings_id = ? AND transaction_type = 'Interest'
    ");
    $stmt2->bind_param("s", $savings_id);
    $stmt2->execute();
    $interestResult = $stmt2->get_result()->fetch_assoc();
    $total_interest = floatval($interestResult['total_interest']);
}

header('Content-Type: application/json');
echo json_encode([
    'transactions' => $transactions,
    'total_interest' => $total_interest
]);

<?php
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn)
    die("Connection Error: " . mysqli_connect_error());

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch loans for this user with balances and next due
$loans = [];
$loan_query = "
    SELECT 
        l.loan_id,
        l.loan_type,
        l.Status,
        l.application_date,
        l.amount AS total_amount,
        COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount END), 0) AS paid,
        l.amount - COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount END), 0) AS balance,
        MIN(CASE WHEN p.status='Pending' THEN p.due_date END) AS next_due
    FROM loans l
    LEFT JOIN loan_payments p ON l.loan_id = p.loan_id
    WHERE l.customer_id = ?
    GROUP BY l.loan_id
    ORDER BY l.application_date DESC
";

$stmt = mysqli_prepare($conn, $loan_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $loans[] = $row;
}

mysqli_stmt_close($stmt);
?>
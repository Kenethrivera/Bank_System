<?php
// File: php/get_loan_details.php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0); 

try {
    $conn = mysqli_connect('localhost', 'root', '', 'bank_db');

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $loan_id = intval($_GET['loan_id'] ?? 0);
    $user_id = intval($_SESSION['user_id'] ?? 0);
    // Assuming you have a session variable that identifies the user type
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';

    if (!$loan_id) {
        throw new Exception('Loan ID is required');
    }

    if (!$user_id) {
        throw new Exception('User not logged in');
    }

    // BASE QUERY
    $loan_query = "SELECT 
                    l.loan_id, 
                    l.loan_type, 
                    l.Status, 
                    l.application_date, 
                    l.reason, 
                    l.amount AS total_amount,
                    COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount ELSE 0 END), 0) AS paid,
                    l.amount - COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount ELSE 0 END), 0) AS balance
                   FROM loans l
                   LEFT JOIN loan_payments p ON l.loan_id = p.loan_id
                   WHERE l.loan_id = ?";

    // IF NOT ADMIN: Add security check so users can only see their own loans
    if (!$is_admin) {
        $loan_query .= " AND l.customer_id = ?";
    }

    $loan_query .= " GROUP BY l.loan_id";

    $stmt = mysqli_prepare($conn, $loan_query);

    if ($is_admin) {
        mysqli_stmt_bind_param($stmt, 'i', $loan_id);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $loan_id, $user_id);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $loan = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$loan) {
        throw new Exception('Loan not found or access denied');
    }

    // Format numbers
    $loan['total_amount'] = number_format(floatval($loan['total_amount']), 2, '.', '');
    $loan['paid'] = number_format(floatval($loan['paid']), 2, '.', '');
    $loan['balance'] = number_format(floatval($loan['balance']), 2, '.', '');

    // Get payment breakdown
    $payment_query = "SELECT payment_id, due_date, payment_amount, status
                      FROM loan_payments
                      WHERE loan_id = ?
                      ORDER BY due_date ASC";

    $stmt = mysqli_prepare($conn, $payment_query);
    mysqli_stmt_bind_param($stmt, 'i', $loan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['payment_amount'] = number_format(floatval($row['payment_amount']), 2, '.', '');
        $payments[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode([
        'success' => true,
        'loan' => $loan,
        'payments' => $payments
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
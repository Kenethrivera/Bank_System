<?php
// File: php/process_payment.php
session_start();

// Prevent any output before JSON
ob_start();

header('Content-Type: application/json');

// Disable error display (log only)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    // Clear any previous output
    ob_clean();

    $conn = mysqli_connect('localhost', 'root', '', 'bank_db');

    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Get POST data
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data received');
    }

    $loan_id = intval($data['loan_id'] ?? 0);
    $payment_id = intval($data['payment_id'] ?? 0);
    $type = $data['type'] ?? '';
    $user_id = intval($_SESSION['user_id'] ?? 0);

    if (!$loan_id || !$user_id) {
        throw new Exception('Invalid request: Missing loan ID or user ID');
    }

    if (!in_array($type, ['monthly', 'early', 'full'])) {
        throw new Exception('Invalid payment type');
    }

    // Get user balance
    $user_query = "SELECT Balance FROM user_accounts WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $user_query);

    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_row = mysqli_fetch_assoc($result);

    if (!$user_row) {
        mysqli_stmt_close($stmt);
        throw new Exception('User not found');
    }

    $user_balance = floatval($user_row['Balance'] ?? 0);
    mysqli_stmt_close($stmt);

    // Begin transaction
    mysqli_begin_transaction($conn);

    if ($type === 'full') {
        // Full payment: Get all pending payments
        $query = "SELECT payment_id, payment_amount FROM loan_payments 
                  WHERE loan_id = ? AND status = 'Pending' 
                  ORDER BY due_date ASC";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'i', $loan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $total_payment = 0;
        $payment_ids = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $total_payment += floatval($row['payment_amount']);
            $payment_ids[] = intval($row['payment_id']);
        }
        mysqli_stmt_close($stmt);

        if (empty($payment_ids)) {
            throw new Exception('No pending payments found');
        }

        if ($user_balance < $total_payment) {
            throw new Exception('Insufficient funds. Need ₱' . number_format($total_payment, 2) . ', available ₱' . number_format($user_balance, 2));
        }

        $new_balance = $user_balance - $total_payment;

        // Update user balance
        $update_query = "UPDATE user_accounts SET Balance = ? WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $update_query);

        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'di', $new_balance, $user_id);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update balance: ' . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);

        // Mark all payments as Paid
        $placeholders = implode(',', array_fill(0, count($payment_ids), '?'));
        $update_payment = "UPDATE loan_payments SET status = 'Paid' WHERE payment_id IN ($placeholders)";
        $stmt = mysqli_prepare($conn, $update_payment);

        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }

        $types = str_repeat('i', count($payment_ids));
        mysqli_stmt_bind_param($stmt, $types, ...$payment_ids);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update payments: ' . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);

        // Insert transaction record
        $trans_query = "INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon)
                        VALUES (?, 'Cash Out', ?, ?, 'Full Loan Payment', 'bi-cash-stack')";
        $stmt = mysqli_prepare($conn, $trans_query);

        if (!$stmt) {
            throw new Exception('Database error: ' . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, 'idd', $user_id, $total_payment, $new_balance);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to insert transaction: ' . mysqli_stmt_error($stmt));
        }
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);
        mysqli_close($conn);

        // Clear buffer and send response
        ob_end_clean();
        echo json_encode([
            'success' => true,
            'message' => 'Full loan payment successful',
            'amount_paid' => number_format($total_payment, 2)
        ]);
        exit;
    }

    // Monthly or early payment
    if (!$payment_id) {
        throw new Exception('Payment ID is required');
    }

    // CRITICAL FIX: Get ONLY the pending payments, ordered by due date
    $query = "SELECT payment_id, payment_amount, status FROM loan_payments 
              WHERE loan_id = ? AND status = 'Pending' 
              ORDER BY due_date ASC LIMIT 2";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $loan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $allowed = [];
    while ($r = mysqli_fetch_assoc($result)) {
        $allowed[intval($r['payment_id'])] = $r;
    }
    mysqli_stmt_close($stmt);

    if (!isset($allowed[$payment_id])) {
        throw new Exception('This payment is not available or already paid');
    }

    $amount_to_pay = floatval($allowed[$payment_id]['payment_amount']);

    if ($user_balance < $amount_to_pay) {
        throw new Exception('Insufficient funds. Need ₱' . number_format($amount_to_pay, 2) . ', available ₱' . number_format($user_balance, 2));
    }

    $new_balance = $user_balance - $amount_to_pay;

    // Update user balance
    $update_query = "UPDATE user_accounts SET Balance = ? WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $update_query);

    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'di', $new_balance, $user_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update balance: ' . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // Mark payment as Paid - CRITICAL: Ensure this updates correctly
    $update_payment = "UPDATE loan_payments SET status = 'Paid' WHERE payment_id = ? AND status = 'Pending'";
    $stmt = mysqli_prepare($conn, $update_payment);

    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $payment_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to update payment: ' . mysqli_stmt_error($stmt));
    }

    // Verify the update worked
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected_rows === 0) {
        throw new Exception('Payment could not be updated - it may have already been paid');
    }

    $payment_desc = ($type === 'early') ? 'Early Loan Payment' : 'Monthly Loan Payment';

    // Insert transaction record
    $trans_query = "INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon)
                    VALUES (?, 'Cash Out', ?, ?, ?, 'bi-cash-stack')";
    $stmt = mysqli_prepare($conn, $trans_query);

    if (!$stmt) {
        throw new Exception('Database error: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'idds', $user_id, $amount_to_pay, $new_balance, $payment_desc);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to insert transaction: ' . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);
    mysqli_close($conn);

    // Clear buffer and send response
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Payment successful',
        'amount_paid' => number_format($amount_to_pay, 2)
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        mysqli_rollback($conn);
        mysqli_close($conn);
    }

    // Clear buffer and send error response
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
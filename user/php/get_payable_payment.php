<?php
// File: php/get_payable_payment.php
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

    if (!$loan_id) {
        throw new Exception('Loan ID is required');
    }

    if (!$user_id) {
        throw new Exception('User not logged in');
    }

    // Get user balance
    $user_query = "SELECT Balance FROM user_accounts WHERE ID = ?";
    $stmt = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_row = mysqli_fetch_assoc($result);

    if (!$user_row) {
        throw new Exception('User not found');
    }

    $user_balance = floatval($user_row['Balance'] ?? 0);
    mysqli_stmt_close($stmt);

    // Get current date
    $today = date('Y-m-d');

    // Get ONLY pending payments for this loan, ordered by due date
    $payment_query = "SELECT payment_id, due_date, payment_amount, status 
                      FROM loan_payments 
                      WHERE loan_id = ? AND status = 'Pending' 
                      ORDER BY due_date ASC";
    $stmt = mysqli_prepare($conn, $payment_query);
    mysqli_stmt_bind_param($stmt, 'i', $loan_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $payments[] = $row;
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Check if there are any pending payments
    if (empty($payments)) {
        echo json_encode([
            'success' => false,
            'error' => 'No pending payments - loan is fully paid',
            'all_paid' => true
        ]);
        exit;
    }

    // CORRECT LOGIC:
    // - Payment #1 is for JANUARY (current month), due Feb 14
    // - Payment #2 is for FEBRUARY (next month), due Mar 14
    // - etc.

    $current = null;
    $early = null;

    // Get the current month to determine which payment period we're in
    $current_month = intval(date('m')); // 1-12
    $current_year = intval(date('Y'));

    // Get the loan start month from the first payment's due date
    // If first payment is due Feb 14, the loan starts in January
    $first_due_date = $payments[0]['due_date'];
    $first_due_month = intval(date('m', strtotime($first_due_date)));
    $first_due_year = intval(date('Y', strtotime($first_due_date)));

    // Calculate loan start month (one month before first due date)
    $loan_start_month = $first_due_month - 1;
    $loan_start_year = $first_due_year;
    if ($loan_start_month < 1) {
        $loan_start_month = 12;
        $loan_start_year--;
    }

    foreach ($payments as $index => $payment) {
        // Calculate which month this payment represents
        $payment_represents_month = $loan_start_month + $index;
        $payment_represents_year = $loan_start_year;

        // Handle year overflow
        while ($payment_represents_month > 12) {
            $payment_represents_month -= 12;
            $payment_represents_year++;
        }

        $due_date = $payment['due_date'];

        // CURRENT PAYMENT: Represents current month OR past months (overdue)
        if (
            $payment_represents_year < $current_year ||
            ($payment_represents_year == $current_year && $payment_represents_month <= $current_month)
        ) {

            if ($current === null) {
                $current = $payment;

                // Determine label
                if (
                    $payment_represents_year < $current_year ||
                    ($payment_represents_year == $current_year && $payment_represents_month < $current_month)
                ) {
                    // Overdue
                    $month_name = date('F Y', mktime(0, 0, 0, $payment_represents_month, 1, $payment_represents_year));
                    $current['label'] = 'Overdue Payment for ' . $month_name . ' (Due: ' . $due_date . ')';
                } else {
                    // Current month
                    $current['label'] = 'Current Month Payment (Due: ' . $due_date . ')';
                }

                $current['payment_amount'] = number_format(floatval($current['payment_amount']), 2, '.', '');
            }
        }
        // EARLY PAYMENT: Represents a future month
        else {
            if ($early === null) {
                $early = $payment;
                $month_name = date('F Y', mktime(0, 0, 0, $payment_represents_month, 1, $payment_represents_year));
                $early['label'] = 'Early Payment for ' . $month_name . ' (Due: ' . $due_date . ')';
                $early['payment_amount'] = number_format(floatval($early['payment_amount']), 2, '.', '');
            }
        }

        // Stop once we have both
        if ($current !== null && $early !== null) {
            break;
        }
    }

    // Calculate full balance (sum of all pending payments)
    $full_balance = 0;
    foreach ($payments as $p) {
        $full_balance += floatval($p['payment_amount']);
    }

    echo json_encode([
        'success' => true,
        'current' => $current,
        'early' => $early,
        'full_balance' => number_format($full_balance, 2, '.', ''),
        'user_balance' => number_format($user_balance, 2, '.', ''),
        'all_paid' => false,
        'today' => $today,
        'current_month' => $current_month,
        'current_year' => $current_year,
        'loan_start_month' => $loan_start_month,
        'loan_start_year' => $loan_start_year,
        'pending_count' => count($payments)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
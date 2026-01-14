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
    l.reason,
    COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount END), 0) AS paid,
    l.amount - COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount END), 0) AS balance,
    MIN(CASE WHEN p.status='Pending' THEN p.due_date END) AS next_due,
    SUM(l.amount - COALESCE(SUM(CASE WHEN p.status='Paid' THEN p.payment_amount END), 0)) 
        OVER (PARTITION BY l.customer_id) AS total_balance_due
FROM loans l
LEFT JOIN loan_payments p ON l.loan_id = p.loan_id
WHERE l.customer_id = ?
GROUP BY l.loan_id
ORDER BY l.application_date DESC;
";


$stmt = mysqli_prepare($conn, $loan_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $loans[] = $row;
}

mysqli_stmt_close($stmt);

$totalBalanceDue = 0;

$total_balance_query = "
    SELECT 
        COALESCE(
            SUM(
                l.amount - COALESCE(paid.total_paid, 0)
            ), 0
        ) AS total_balance_due
    FROM loans l
    LEFT JOIN (
        SELECT 
            loan_id,
            SUM(payment_amount) AS total_paid
        FROM loan_payments
        WHERE status = 'Paid'
        GROUP BY loan_id
    ) paid ON l.loan_id = paid.loan_id
    WHERE l.customer_id = ?
      AND l.Status = 'Approved'
";

$stmt = mysqli_prepare($conn, $total_balance_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $totalBalanceDue = $row['total_balance_due'];
}

mysqli_stmt_close($stmt);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_loan'])) {

    // Get the logged-in user's ID from session
    // Adjust this based on how you store user ID in session
    $customer_id = $_SESSION['user_id'] ?? $_SESSION['ID'] ?? null;

    // Validate user is logged in
    if (!$customer_id) {
        $_SESSION['error'] = "You must be logged in to request a loan.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Get form data
    $loan_type = trim($_POST['loan_type']);
    $amount = floatval($_POST['amount']);
    $reason = trim($_POST['reason']);

    // Validate inputs
    if (empty($loan_type) || $amount <= 0 || empty($reason)) {
        $_SESSION['error'] = "All fields are required and amount must be greater than 0.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Set status and application date
    $status = 'Pending';
    $application_date = date('Y-m-d');

    // Insert loan request
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO loans (customer_id, loan_type, amount, Status, application_date, reason)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "isdsss",
            $customer_id,
            $loan_type,
            $amount,
            $status,
            $application_date,
            $reason
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Loan application submitted successfully! Please wait for admin approval.";
        } else {
            $_SESSION['error'] = "Failed to submit loan application. Please try again.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database error. Please try again later.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Display success/error messages in your HTML
if (isset($_SESSION['success'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
    unset($_SESSION['error']);
}
?>
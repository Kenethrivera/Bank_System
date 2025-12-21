<?php
<<<<<<< Updated upstream:admin/php/Dashboard_php.php
$conn = mysqli_connect('localhost', 'root', '', 'schooldb');
if (!$conn) {
  die('Connection Error' . mysqli_connect_error());
}
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Users_account_tbl");
$row = mysqli_fetch_assoc($result);
$totalCustomers = $row['total'];
=======
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) {
    die('Connection Error: ' . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    !empty($_POST['loan_id']) && 
    !empty($_POST['action'])) {
    
    $loan_id = intval($_POST['loan_id']);
    $newStatus = $_POST['action'] === 'Approved' ? 'Approved' : 'Rejected';

    $stmt = mysqli_prepare($conn, "UPDATE loans SET Status = ? WHERE loan_id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $newStatus, $loan_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// ===== Count customers =====
$total_customers_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_accounts");
$row = mysqli_fetch_assoc($total_customers_result);
$totalCustomers = $row['total'];

// ===== Count pending loans =====
$pending_loan = mysqli_query($conn, "SELECT COUNT(*) AS PendingLoans FROM loans WHERE Status = 'Pending'");
$row = mysqli_fetch_assoc($pending_loan);
$pendingLoan = $row['PendingLoans'];

// ===== Get all accounts =====
$accounts_result = mysqli_query($conn, "SELECT * FROM user_accounts");
if (!$accounts_result) {
    die("Accounts Query Error: " . mysqli_error($conn));
}

// ===== Get all loans =====
$loans_result = mysqli_query($conn, "SELECT * FROM loans ORDER BY application_date DESC");
if (!$loans_result) {
    die("Loans Query Error: " . mysqli_error($conn));
}

>>>>>>> Stashed changes:admin/php/Dashboard.php
?>
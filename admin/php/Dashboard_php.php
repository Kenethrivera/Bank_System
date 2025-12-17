<?php

$conn = mysqli_connect('localhost', 'root', '', 'bank_db');

// for button (accept and reject) in ACTION column in LOAN SECTIOn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
    !empty($_POST['loan_id']) && 
    !empty($_POST['action'])) {
    
    $loan_id = intval($_POST['loan_id']);
    $newStatus = $_POST['action'] === 'Approved' ? 'Approved' : 'Rejected';
    // mysqli_prepare() creates a statement template with placeholders instead of directly putting variables into the SQL.
    $stmt = mysqli_prepare($conn, "UPDATE loans SET Status = ? WHERE loan_id = ?");
    // Binds actual values to the placeholders in the prepared statement
    // s means string for status, i for loan_id
    mysqli_stmt_bind_param($stmt, 'si', $newStatus, $loan_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirects the browser to the same page after updating.
    // Prevents the form from being resubmitted if the user refreshes the page.
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (!$conn) {
  die('Connection Error' . mysqli_connect_error());
}
// for counting the customers
$total_customers_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Users_account_tbl");
$row = mysqli_fetch_assoc($total_customers_result);
$totalCustomers = $row['total'];


// for getting all of the loans
$loans_result = mysqli_query($conn, "SELECT * FROM loans ORDER BY application_date DESC");
if (!$loans_result) {
    die("Loans Query Error: " . mysqli_error($conn));
}


?>
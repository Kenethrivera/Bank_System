<?php
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) {
    die('Connection Error: ' . mysqli_connect_error());
}

/* =========================
   HANDLE LOAN APPROVE / REJECT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---- LOAN ACTION ---- */
    if (!empty($_POST['loan_id']) && !empty($_POST['action'])) {

        $loan_id = intval($_POST['loan_id']);

        if ($_POST['action'] === 'Approved') {
            $newStatus = 'Approved';
        } else {
            $newStatus = 'Rejected';
        }

        $stmt = mysqli_prepare($conn, "UPDATE loans SET Status=? WHERE loan_id=?");
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $loan_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    /* ---- ACCOUNT APPROVE / REJECT ---- */
   if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['account_id'], $_POST['update_status'])) {

    $id = (int)$_POST['account_id'];
    $status = $_POST['update_status'] === 'Approved' ? 'Approved' : 'Rejected';

    $stmt = mysqli_prepare($conn,
        "UPDATE user_accounts SET Status=? WHERE ID=?"
    );
    mysqli_stmt_bind_param($stmt, 'si', $status, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ".$_SERVER['PHP_SELF']."#UpdatedStatus");
    exit;
}
/* ---- ACCOUNT DELETE ---- */
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_account_id'])) {

    $accountId = (int)$_POST['delete_account_id'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM user_accounts WHERE ID=?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $accountId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: ".$_SERVER['PHP_SELF']."#accounts");
    exit;
}

/* =========================
   DASHBOARD DATA
========================= */

/* Count customers */
$total_customers_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_accounts");
$row = mysqli_fetch_assoc($total_customers_result);
$totalCustomers = $row['total'];

/* Pending Accounts */
$pending_acc = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_accounts Where Status='Pending'");
$row = mysqli_fetch_assoc($pending_acc);
$pendingAcc = $row['total'];

/* Count pending loans */
$pending_loan = mysqli_query($conn, "SELECT COUNT(*) AS PendingLoans FROM loans WHERE Status='Pending'");
$row = mysqli_fetch_assoc($pending_loan);
$pendingLoan = $row['PendingLoans'];

/* Get accounts */
$accounts_result = mysqli_query($conn, "SELECT * FROM user_accounts");
if (!$accounts_result) {
    die("Accounts Query Error: " . mysqli_error($conn));
}

/* Get loans */
$loans_result = mysqli_query($conn, "SELECT * FROM loans ORDER BY application_date DESC");
if (!$loans_result) {
    die("Loans Query Error: " . mysqli_error($conn));
}
?>

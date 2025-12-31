<?php

$conn = mysqli_connect('localhost', 'root', '', 'bank_db');

if (!$conn) {
  die('Connection Error' . mysqli_connect_error());
}

// for button (accept and reject) in ACTION column in LOAN SECTIOn
if (
  $_SERVER['REQUEST_METHOD'] === 'POST' &&
  !empty($_POST['loan_id']) &&
  !empty($_POST['action'])
) {

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
  header("Location: " . $_SERVER['PHP_SELF'] . '#loan');
  exit();
}

// for counting the customers
$total_customers_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Users_account_tbl");
$row = mysqli_fetch_assoc($total_customers_result);
$totalCustomers = $row['total'];


// for getting all of the loans
$loans_result = mysqli_query($conn, "SELECT * FROM loans ORDER BY Status DESC");
if (!$loans_result) {
  die("Loans Query Error: " . mysqli_error($conn));
}


// adding new loans
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_loan'])) {
  $customer_name = $_POST['customer_name'];
  $loan_type = $_POST['loan_type'];
  $amount = floatval($_POST['amount']);
  $status = 'Pending';
  $application_date = date('Y-m-d');

  $get_id = mysqli_query($conn, "SELECT MAX(loan_id) AS max_id FROM loans");
  $max_id = mysqli_fetch_assoc($get_id);
  $new_loan_id = $max_id["max_id"] + 1;

  $stmt = mysqli_prepare($conn, "INSERT INTO loans(loan_id, customer_name, loan_type, amount, Status, application_date) VALUES (?,?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "issdss", $new_loan_id, $customer_name, $loan_type, $amount, $status, $application_date);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  header("Location: " . $_SERVER['PHP_SELF'] . '#loan');
  exit();
}


// SAVINGS ACCOUNTS QUERY
function getAllSavings($conn)
{
  $sql = "
        SELECT 
            sa.savings_id,
            sa.savings_type,
            sa.status,
            sa.interest_rate,
            sa.customer_id,
            sa.balance,
            ua.first_name,
            ua.last_name
        FROM savings_accounts sa
        LEFT JOIN users_account_tbl ua
            ON sa.customer_id = ua.customer_id
        ORDER BY sa.status ASC, sa.savings_id DESC
    ";

  $result = mysqli_query($conn, $sql);

  if (!$result) {
    die("Savings Query Error: " . mysqli_error($conn));
  }

  $savings = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $row['full_name'] = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
    $savings[] = $row;
  }

  return $savings;
}

$savings_result = getAllSavings($conn);



// TOTAL ASSSET FOR SAVINGS
$result = mysqli_query($conn, "SELECT COALESCE(SUM(balance),0) AS total_assets FROM savings_accounts");
$row = mysqli_fetch_assoc($result);
$totalAssets = floatval($row['total_assets']);

// TOTAL ACTIVE SAVINGS 
$result = mysqli_query($conn, "SELECT COUNT(*) AS active_accounts FROM savings_accounts WHERE status = 'Active'");
$row = mysqli_fetch_assoc($result);
$activeSavings = intval($row['active_accounts']);

// AVERAGE INTEREST
$result = mysqli_query($conn, "SELECT COALESCE(AVG(interest_rate),0) AS avg_interest FROM savings_accounts");
$row = mysqli_fetch_assoc($result);
$avgInterest = floatval($row['avg_interest']) * 100;


// SETTINGS: CHANGE SAVINGS INTEREST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_savings'])) {
  $savings_id = $_POST['savings_id'];
  $savings_type = $_POST['savings_type'];
  $interest_rate = floatval($_POST['interest_rate'] / 100);

  $stmt = mysqli_prepare($conn, "UPDATE savings_accounts SET savings_type=?, interest_rate=? WHERE savings_id=?");
  mysqli_stmt_bind_param($stmt, "sds", $savings_type, $interest_rate, $savings_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
  exit();
}

// LOCK ICON 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
  $savings_id = $_POST['savings_id'];
  $new_status = $_POST['toggle_status']; // Active or Frozen

  $stmt = mysqli_prepare($conn, "UPDATE savings_accounts SET status=? WHERE savings_id=?");
  mysqli_stmt_bind_param($stmt, "ss", $new_status, $savings_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
  exit();
}

// QUERY FOR NEW SAVINGS APPLICATION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_savings'])) {
  $customer_id = $_POST['customer_id'];
  $savings_type = $_POST['savings_type'];
  $initial_deposit = floatval($_POST['initial_deposit']);

  // Set default interest rates
  $rates = ['Regular' => 0.05, 'Fixed' => 0.08, 'Special' => 0.10];
  $interest_rate = $rates[$savings_type] ?? 0.05;

  // New savings_id
  $result = mysqli_query($conn, "SELECT savings_id FROM savings_accounts ORDER BY savings_id DESC LIMIT 1");
  $row = mysqli_fetch_assoc($result);

  if ($row) {
    // Extract numeric part
    $lastNum = intval(substr($row['savings_id'], 4)); // skip 'SAV-'
    $newNum = $lastNum + 1;
  } else {
    $newNum = 1; // first savings account
  }

  // Format with leading zeros
  $new_savings_id = 'SAV-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);


  // Insert
  $stmt = mysqli_prepare($conn, "INSERT INTO savings_accounts (savings_id, customer_id, savings_type, interest_rate, balance) VALUES (?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "sisdd", $new_savings_id, $customer_id, $savings_type, $interest_rate, $initial_deposit);
  mysqli_stmt_execute($stmt);

  header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
  exit();
}

// GET ALL USERS FOR SAVINGS NAMES
$users = [];
$result = mysqli_query($conn, "SELECT customer_id, first_name, last_name FROM users_account_tbl ORDER BY first_name, last_name");
while ($row = mysqli_fetch_assoc($result)) {
  $users[] = $row;
}

//<?php
// REUSABLE SEARCH FUNCTION
function buildNameSearch($search, $columnName)
{
    $search = trim($search);

    if ($search === '') {
        return [
            'where' => '',
            'types' => '',
            'params' => []
        ];
    }

    return [
        'where'  => " AND $columnName LIKE ? ",
        'types'  => 's',
        'params' => ['%' . $search . '%']
    ];
}

// REUSABLE FUNCTION TO EXECUTE QUERY WITH OPTIONAL SEARCH
function executeQuery($conn, $sql, $searchData = null)
{
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        die('MySQL prepare error: ' . mysqli_error($conn));
    }

    if ($searchData && $searchData['types'] !== '') {
        mysqli_stmt_bind_param(
            $stmt,
            $searchData['types'],
            ...$searchData['params']
        );
    }

    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// GET SEARCH INPUT
$search = $_GET['search'] ?? '';

// --- LOANS SEARCH ---
$loanSearchData = buildNameSearch($search, 'l.customer_name');

$loanSql = "
    SELECT *
    FROM loans l
    WHERE 1=1
    {$loanSearchData['where']}
    ORDER BY l.application_date DESC
";

$loans_result = executeQuery($conn, $loanSql, $loanSearchData);

// --- SAVINGS SEARCH ---
$savingsSearchData = buildNameSearch($search, "CONCAT(u.first_name, ' ', u.last_name)");

$savingsSql = "
    SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) AS full_name
    FROM savings_accounts s
    JOIN users_account_tbl u ON s.customer_id = u.customer_id
    WHERE 1=1
    {$savingsSearchData['where']}
";

$savings_result = executeQuery($conn, $savingsSql, $savingsSearchData);






?>
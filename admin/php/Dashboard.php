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
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['account_id'], $_POST['update_status'])
    ) {

        $id = (int) $_POST['account_id'];
        $status = $_POST['update_status'] === 'Approved' ? 'Approved' : 'Rejected';

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE user_accounts SET Status=? WHERE ID=?"
        );
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $_SERVER['PHP_SELF'] . "#accounts");
        exit;
    }
    /* ---- ACCOUNT DELETE ---- */
}
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_account_id'])
) {

    $accountId = (int) $_POST['delete_account_id'];

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM user_accounts WHERE ID=?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $accountId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER['PHP_SELF'] . "#accounts");
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
            sa.ID,
            sa.balance,
            ua.firstname,
            ua.lastname
        FROM savings_accounts sa
        LEFT JOIN user_accounts ua
            ON sa.ID = ua.ID
        ORDER BY sa.status ASC, sa.savings_id DESC
    ";

  $result = mysqli_query($conn, $sql);

  if (!$result) {
    die("Savings Query Error: " . mysqli_error($conn));
  }

  $savings = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $row['full_name'] = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
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
  $customer_id = $_POST['ID'];
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
  $stmt = mysqli_prepare($conn, "INSERT INTO savings_accounts (savings_id, ID, savings_type, interest_rate, balance) VALUES (?,?,?,?,?)");
  mysqli_stmt_bind_param($stmt, "sisdd", $new_savings_id, $customer_id, $savings_type, $interest_rate, $initial_deposit);
  mysqli_stmt_execute($stmt);

  header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
  exit();
}

// GET ALL USERS FOR SAVINGS NAMES
$users = [];
$result = mysqli_query($conn, "SELECT ID, firstname, lastname FROM user_accounts ORDER BY firstname, lastname");
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
$savingsSearchData = buildNameSearch($search, "CONCAT(u.firstname, ' ', u.lastname)");

$savingsSql = "
    SELECT 
        s.*,
        u.FirstName,
        u.LastName,
        u.Email,
        u.Phone,
        u.Address,
        u.Birthdate,
        u.Status AS UserStatus,
        u.Img,
        CONCAT(u.FirstName, ' ', u.LastName) AS full_name
    FROM savings_accounts s
    JOIN user_accounts u ON s.ID = u.ID
    WHERE 1=1
    {$savingsSearchData['where']}
";
$savings_result = executeQuery($conn, $savingsSql, $savingsSearchData);

$faqs = [
    [
        'category' => 'Deposits',
        'questions' => [
            ['q' => 'How do I make a deposit?', 'a' => 'You can make deposits through our ATM network, mobile app, or by visiting any branch location. For checks, use mobile deposit through our app.'],
            ['q' => 'What is the daily deposit limit?', 'a' => 'ATM deposits are limited to $10,000 per day. There are no limits for deposits made at branch locations.'],
        ],
    ],
    [
        'category' => 'Transfers',
        'questions' => [
            ['q' => 'How long do transfers take?', 'a' => 'Internal transfers between SecureBank accounts are instant. External transfers typically take 1-3 business days.'],
            ['q' => 'Are there fees for transfers?', 'a' => 'Internal transfers are free. External transfers may incur a $3 fee depending on your account type.'],
        ],
    ],
    [
        'category' => 'Opening an Account',
        'questions' => [
            ['q' => 'What documents do I need?', 'a' => 'You need a valid government-issued ID, proof of address, and Social Security number or Tax ID.'],
            ['q' => 'Is there a minimum balance requirement?', 'a' => 'Our basic checking account has no minimum balance. Savings accounts require a $100 minimum to open.'],
        ],
    ],
    [
        'category' => 'Card Issues',
        'questions' => [
            ['q' => 'What should I do if my card is lost or stolen?', 'a' => 'Call our 24/7 hotline immediately at 1-800-SECURE-BANK to report and block your card. We will issue a replacement within 3-5 business days.'],
            ['q' => 'How do I activate my new card?', 'a' => 'Activate your card through our mobile app, online banking, or by calling the activation number on the sticker attached to your card.'],
        ],
    ],
];


?>
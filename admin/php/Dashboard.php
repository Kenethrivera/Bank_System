<?php
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) { die('Connection Error: ' . mysqli_connect_error()); }

if (session_status() === PHP_SESSION_NONE) { session_start(); }


$admin_session_id = $_SESSION['user_id'] ?? 0;
$admin_query = mysqli_query($conn, "SELECT FirstName, MiddleName, LastName, Email, Role, ID FROM user_accounts WHERE ID = '$admin_session_id'");
$admin_row = mysqli_fetch_assoc($admin_query);

$adminFullName = $admin_row['FirstName'] . " " . ($admin_row['MiddleName'] ? $admin_row['MiddleName'] . " " : "") . $admin_row['LastName'];
$email = $admin_row['Email'];
$role = $admin_row['Role'];
$empId = "EMP-" . str_pad($admin_row['ID'], 3, '0', STR_PAD_LEFT);

/* =========================
   HANDLE LOAN APPROVE / REJECT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* ---- LOAN ACTION ---- */
    if (!empty($_POST['loan_id']) && !empty($_POST['action'])) {

        $loan_id = intval($_POST['loan_id']);
        $action = $_POST['action'];
        $newStatus = ($action === 'Approved') ? 'Approved' : 'Rejected';
        $reason = $_POST['reason'] ?? null;

        // Get loan details BEFORE updating
        $loanQuery = "SELECT customer_id, amount, loan_type, application_date FROM loans WHERE loan_id = ?";
        $stmt = mysqli_prepare($conn, $loanQuery);
        mysqli_stmt_bind_param($stmt, 'i', $loan_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $loanData = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$loanData) {
            $_SESSION['error'] = "Loan not found.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $customer_id = $loanData['customer_id'];
        $loan_amount = floatval($loanData['amount']);
        $loan_type = $loanData['loan_type'];
        $start_date = $loanData['application_date'];

        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Update loan status + reason
            if (!empty($reason)) {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE loans SET Status = ?, reason = ? WHERE loan_id = ?"
                );
                mysqli_stmt_bind_param($stmt, 'ssi', $newStatus, $reason, $loan_id);
            } else {
                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE loans SET Status = ? WHERE loan_id = ?"
                );
                mysqli_stmt_bind_param($stmt, 'si', $newStatus, $loan_id);
            }
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // If approved, add loan amount to user's balance and generate payment schedule
            if ($newStatus === 'Approved') {
                
                // 1. Get current user balance
                $balanceQuery = "SELECT Balance FROM user_accounts WHERE ID = ?";
                $stmt = mysqli_prepare($conn, $balanceQuery);
                mysqli_stmt_bind_param($stmt, 'i', $customer_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $userRow = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if (!$userRow) {
                    throw new Exception("User account not found.");
                }

                $current_balance = floatval($userRow['Balance']);
                $new_balance = $current_balance + $loan_amount;

                // 2. Update user balance
                $updateBalanceQuery = "UPDATE user_accounts SET Balance = ? WHERE ID = ?";
                $stmt = mysqli_prepare($conn, $updateBalanceQuery);
                mysqli_stmt_bind_param($stmt, 'di', $new_balance, $customer_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // 3. Insert transaction record
                $transQuery = "INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon) 
                               VALUES (?, 'Cash In', ?, ?, ?, 'bi-cash-coin')";
                $stmt = mysqli_prepare($conn, $transQuery);
                $description = "Loan Approved - " . $loan_type;
                mysqli_stmt_bind_param($stmt, 'idds', $customer_id, $loan_amount, $new_balance, $description);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // 4. Determine number of months per loan type
                switch ($loan_type) {
                    case 'Personal':
                        $months = 6;
                        break;
                    case 'Home':
                        $months = 10;
                        break;
                    case 'Auto Loan':
                        $months = 12; // 1 year
                        break;
                    case 'Business':
                        $months = 24; // 2 years
                        break;
                    default:
                        $months = 12;
                }

                // 5. Calculate monthly payment with rounding fix
                $monthly_payment = round($loan_amount / $months, 2);

                // 6. Generate payment schedule
                for ($i = 1; $i <= $months; $i++) {
                    $due_date = date('Y-m-d', strtotime("+$i month", strtotime($start_date)));

                    // Adjust last payment to match total loan amount exactly
                    if ($i === $months) {
                        $total_assigned = $monthly_payment * ($months - 1);
                        $monthly_payment = round($loan_amount - $total_assigned, 2);
                    }

                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO loan_payments (loan_id, due_date, payment_amount, status) 
                         VALUES (?, ?, ?, 'Pending')"
                    );
                    mysqli_stmt_bind_param($stmt, 'isd', $loan_id, $due_date, $monthly_payment);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $_SESSION['success'] = "Loan approved successfully! ₱" . number_format($loan_amount, 2) . " has been added to the customer's balance.";
            } else {
                $_SESSION['success'] = "Loan rejected successfully.";
            }

            // Commit transaction
            mysqli_commit($conn);

        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($conn);
            $_SESSION['error'] = "Error processing loan: " . $e->getMessage();
        }

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
}

// DASHBOARD DATA
/* Count customers */
$total_customers_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_accounts Where Role='User'");
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
$accounts_result = mysqli_query($conn, "SELECT * FROM user_accounts Where Role='User'");
if (!$accounts_result) {
    die("Accounts Query Error: " . mysqli_error($conn));
}

/* Get loans */
$loans_result = mysqli_query(
    $conn,
    "SELECT 
        l.loan_id,
        l.loan_type,
        l.amount,
        l.Status,
        l.reason,
        l.application_date,
        u.FirstName,
        u.MiddleName,
        u.LastName
     FROM loans l
     LEFT JOIN user_accounts u ON l.customer_id = u.ID
     ORDER BY l.application_date DESC"
);

if (!$loans_result) {
    die("Loans Query Error: " . mysqli_error($conn));
}

// adding new loans
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_loan'])) {

    $customer_id = (int) $_POST['customer_id'];
    $loan_type = $_POST['loan_type'];
    $amount = floatval($_POST['amount']);
    $status = 'Pending';
    $application_date = date('Y-m-d');
    $reason = $_POST['reason'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO loans (customer_id, loan_type, amount, Status, application_date, reason)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

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

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: " . $_SERVER['PHP_SELF'] . '#loan');
    exit();
}
// ADMIN CREATE
/* ---- CREATE NEW ADMIN ---- */
if (isset($_POST['create_admin'])) {
    // ASSIGN FORM DATA TO VARIABLES FIRST
    $fname   = mysqli_real_escape_string($conn, $_POST['adm_fname']);
    $mname   = mysqli_real_escape_string($conn, $_POST['adm_mname'] ?? '');
    $lname   = mysqli_real_escape_string($conn, $_POST['adm_lname']);
    $email   = mysqli_real_escape_string($conn, $_POST['adm_email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['adm_phone']);
    $dob     = mysqli_real_escape_string($conn, $_POST['adm_dob']);
    $address = mysqli_real_escape_string($conn, $_POST['adm_address']);
    $pass    = $_POST['adm_password']; 

    // 1. Handle Image Upload Logic
    $imgName = "profile/default.jpg"; 
    
    if (isset($_FILES['adm_img']) && $_FILES['adm_img']['error'] === 0) {
        $targetDir = "../profile/"; 
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = "admin_" . time() . "_" . basename($_FILES["adm_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($fileType), $allowTypes)) {
            if (move_uploaded_file($_FILES["adm_img"]["tmp_name"], $targetFilePath)) {
                $imgName = "profile/" . $fileName; 
            }
        }
    }

    // 2. Insert into Database
    $sql = "INSERT INTO user_accounts (FirstName, MiddleName, LastName, Email, Phone, Birthdate, Address, Password, Img, Role, Status, Balance) 
            VALUES ('$fname', '$mname', '$lname', '$email', '$phone', '$dob', '$address', '$pass', '$imgName', 'Admin', 'Approved', 0.00)";

    if (mysqli_query($conn, $sql)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "#admin-management");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// =======
// ACOUNT MANAGEMENT
// =========

if (isset($_POST['confirm_deposit'])) {
    $accountId = mysqli_real_escape_string($conn, $_POST['deposit_account_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['deposit_amount']);

    if ($amount > 0) {
        // SQL to increment the balance
        $sql = "UPDATE user_accounts SET Balance = Balance + $amount WHERE ID = '$accountId'" ;
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Balance updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error updating balance: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// ============================================
// ADMIN SAVINGS BACKEND - Safe Fixed Version
// Add this AFTER your existing code, don't replace everything
// ============================================

// Enable error reporting temporarily to see what's wrong
error_reporting(E_ALL);
ini_set('display_errors', 1);

// TOTAL ASSET FOR SAVINGS (Keep your original or use this)
$result = mysqli_query($conn, "SELECT COALESCE(SUM(balance),0) AS total_assets FROM savings_accounts WHERE status = 'Active'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalAssets = floatval($row['total_assets']);
} else {
    $totalAssets = 0;
}

// TOTAL ACTIVE SAVINGS 
$result = mysqli_query($conn, "SELECT COUNT(*) AS active_accounts FROM savings_accounts WHERE status = 'Active'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $activeSavings = intval($row['active_accounts']);
} else {
    $activeSavings = 0;
}

// AVERAGE INTEREST (now stored as 2.5, 3.5, 5.0 - no need to multiply)
$result = mysqli_query($conn, "SELECT COALESCE(AVG(interest_rate),0) AS avg_interest FROM savings_accounts WHERE status = 'Active'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $avgInterest = number_format(floatval($row['avg_interest']), 2);
} else {
    $avgInterest = '0.00';
}

// SETTINGS: CHANGE SAVINGS TYPE AND INTEREST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_savings'])) {
    $savings_id = mysqli_real_escape_string($conn, $_POST['savings_id']);
    $savings_type = mysqli_real_escape_string($conn, $_POST['savings_type']);

    // Set interest rate based on savings type
    $interest_rates = [
        'Regular' => 2.5,
        'Fixed' => 3.5,
        'Special' => 5.0
    ];

    $interest_rate = isset($interest_rates[$savings_type]) ? $interest_rates[$savings_type] : 2.5;

    $stmt = mysqli_prepare($conn, "UPDATE savings_accounts SET savings_type=?, interest_rate=? WHERE savings_id=?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sds", $savings_type, $interest_rate, $savings_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
    exit();
}

// STATUS CHANGE (Active, Pending, Frozen, Closed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $savings_id = mysqli_real_escape_string($conn, $_POST['savings_id']);
    $new_status = trim(mysqli_real_escape_string($conn, $_POST['toggle_status']));

    $stmt = mysqli_prepare($conn, "UPDATE savings_accounts SET status=? WHERE savings_id=?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $new_status, $savings_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
    exit();
}
/* 1. Dashboard View (Limited to 5) */
/* 1. DATA FOR THE DASHBOARD CARD (TOP 5 ONLY) */
$recent_query = "SELECT t.*, u.FirstName, u.LastName 
                 FROM transactions t 
                 LEFT JOIN user_accounts u ON t.user_id = u.ID 
                 ORDER BY t.created_at DESC LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);

/* 2. DATA FOR THE MODAL (EVERYTHING) */
$all_query = "SELECT t.*, u.FirstName, u.LastName 
              FROM transactions t 
              LEFT JOIN user_accounts u ON t.user_id = u.ID 
              ORDER BY t.created_at DESC";
$all_result = mysqli_query($conn, $all_query);
//=============================
// DELTE ACCOIUNT
//============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    $id = intval($_POST['delete_account_id']);
    $action = $_POST['action_type'];

    if ($action === 'delete') {
        // PERMANENT DATABASE DELETION
        $query = "DELETE FROM user_accounts WHERE ID = $id";
    } else {
        // REJECTION ONLY
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $query = "UPDATE user_accounts SET Status = 'Rejected', RejectionReason = '$reason' WHERE ID = $id";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "#accounts");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
}


// FETCH TRANSACTION SECTION

$manage_trans_query = "
    SELECT t.*, u.FirstName, u.LastName 
    FROM transactions t
    LEFT JOIN user_accounts u ON t.user_id = u.ID
    ORDER BY t.created_at DESC";
$manage_trans_result = mysqli_query($conn, $manage_trans_query);

// ======================================================
// Dashboard
// ======================================================
// 1. Get Total Balance of all users
$balance_query = mysqli_query($conn, "SELECT SUM(Balance) AS total_sum FROM user_accounts Where Status='Approved'");
$balance_data = mysqli_fetch_assoc($balance_query);
$totalBalance = $balance_data['total_sum'] ?? 0;

// 2. Count Savings Accounts (from your savings_accounts table)
$savings_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM savings_accounts");
$totalSavings = mysqli_fetch_assoc($savings_count_query)['total'];

// 3. Count Regular User Accounts (Checking)
$checking_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_accounts WHERE Role = 'Admin'");
$totalChecking = mysqli_fetch_assoc($checking_count_query)['total'];

// 4. Calculate Progress Bar (Ratio of Savings to Total Accounts)
$total_all = $totalSavings + $totalChecking;
$progressPercent = ($total_all > 0) ? ($totalSavings / $total_all) * 100 : 0;
// =======================================================
// NEW SAVINGS APPLICATION (ADMIN) — FRONTEND SAFE
// =======================================================

// =======================================================
// AJAX: GET CUSTOMER BALANCE (FRONTEND ONLY)
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_balance'], $_GET['id'])) {

    header('Content-Type: application/json');

    $customer_id = intval($_GET['id']);

    $stmt = mysqli_prepare($conn, "SELECT Balance FROM user_accounts WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $balance);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'balance' => $balance !== null ? (float) $balance : 0
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_savings'])) {

    $_SESSION['savings_error'] = null;
    $_SESSION['savings_success'] = null;

    $customer_id = intval($_POST['ID']);
    $savings_type = $_POST['savings_type'];
    $initial_deposit = floatval($_POST['initial_deposit']);

    // -------------------------------
    // Minimum deposit per savings type
    // -------------------------------
    $minimum_deposits = [
        'Regular' => 100,
        'Fixed' => 1000,
        'Special' => 50000
    ];

    $required_min = $minimum_deposits[$savings_type] ?? 100;

    if ($initial_deposit < $required_min) {
        $_SESSION['savings_error'] =
            "Minimum initial deposit for {$savings_type} Savings is ₱" . number_format($required_min, 2);
        header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
        exit();
    }

    // -------------------------------
    // Fetch customer balance
    // -------------------------------
    $stmt = mysqli_prepare($conn, "SELECT Balance FROM user_accounts WHERE ID = ?");
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $current_balance);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($current_balance === null) {
        $_SESSION['savings_error'] = "Customer not found.";
        header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
        exit();
    }

    // -------------------------------
    // Balance sufficiency check
    // -------------------------------
    if ($current_balance < $initial_deposit) {
        $_SESSION['savings_error'] =
            "Insufficient balance. Available balance is ₱" . number_format($current_balance, 2);
        header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
        exit();
    }

    // -------------------------------
    // Interest rates
    // -------------------------------
    $interest_rates = [
        'Regular' => 2.5,
        'Fixed' => 3.5,
        'Special' => 5.0
    ];

    $interest_rate = $interest_rates[$savings_type] ?? 2.5;

    $savings_id = 'SAV' . str_pad($customer_id, 4, '0', STR_PAD_LEFT) . substr(uniqid(), -3);

    // -------------------------------
    // TRANSACTION
    // -------------------------------
    mysqli_begin_transaction($conn);

    try {

        $new_balance = $current_balance - $initial_deposit;

        $stmt = mysqli_prepare($conn, "UPDATE user_accounts SET Balance=? WHERE ID=?");
        mysqli_stmt_bind_param($stmt, "di", $new_balance, $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "
            INSERT INTO savings_accounts 
                (savings_id, ID, savings_type, interest_rate, balance, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')
        ");
        mysqli_stmt_bind_param(
            $stmt,
            "sisdd",
            $savings_id,
            $customer_id,
            $savings_type,
            $interest_rate,
            $initial_deposit
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mysqli_commit($conn);

        $_SESSION['savings_success'] = "Savings account created successfully!";
        header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['savings_error'] = "Failed to create savings account.";
        header("Location: " . $_SERVER['PHP_SELF'] . "#savings");
        exit();
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$savingsSql = "
SELECT 
    s.savings_id,
    s.savings_type,
    s.status,
    s.interest_rate,
    s.balance,
    s.created_at,
    s.ID,
    CONCAT(u.FirstName, ' ', u.LastName) AS full_name,
    u.Email,
    u.Phone,
    u.Address,
    u.Birthdate,
    u.Status AS UserStatus,
    u.Img
FROM savings_accounts s
INNER JOIN user_accounts u ON u.ID = s.ID
WHERE 1=1
";

if (!empty($search)) {
    $search_safe = mysqli_real_escape_string($conn, $search);
    $savingsSql .= " AND CONCAT(u.FirstName, ' ', u.LastName) LIKE '%$search_safe%'";
}

$savingsSql .= " ORDER BY s.created_at DESC";

$result = mysqli_query($conn, $savingsSql);

$savings_result = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $savings_result[] = $row;
    }
}


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
        'where' => " AND $columnName LIKE ? ",
        'types' => 's',
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

    // ✅ ONLY bind if SQL actually has placeholders
    if (
        $searchData &&
        $searchData['types'] !== '' &&
        substr_count($sql, '?') === strlen($searchData['types'])
    ) {
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
$loanSearchData = buildNameSearch(
    $search,
    "CONCAT(u.FirstName,' ',IFNULL(u.MiddleName,''),' ',u.LastName)"
);

$loanSql = "
    SELECT 
        l.loan_id,
        l.loan_type,
        l.amount,
        l.Status,
        l.reason,
        l.application_date,
        u.FirstName,
        u.MiddleName,
        u.LastName
    FROM loans l
    LEFT JOIN user_accounts u ON l.customer_id = u.ID
    WHERE 1=1
    {$loanSearchData['where']}
    ORDER BY l.application_date DESC
";

$loans_result = executeQuery($conn, $loanSql, $loanSearchData);
// --- SAVINGS SEARCH ---
$savingsSearchData = buildNameSearch($search, "CONCAT(u.firstname, ' ', u.lastname)");


if (!empty($search)) {
    $result = executeQuery($conn, $savingsSql, $savingsSearchData);
} else {
    $result = executeQuery($conn, $savingsSql);
}

$faqs = [
    [
        'category' => 'Deposits',
        'questions' => [
            ['q' => 'How do user make a deposit?', 'a' => 'You can make deposits through our ATM network, mobile app, or by visiting any branch location. For checks, use mobile deposit through our app.'],
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
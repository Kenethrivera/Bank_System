<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// IMPORTANT: Set $userId BEFORE including back_end.php
$userId = $_SESSION['user_id'];

// Now include back_end.php - it will see $userId is set
require_once 'back_end.php';

$action = $_POST['action'] ?? '';

// ==========================================
// CREATE NEW SAVINGS ACCOUNT
// ==========================================
if ($action === 'create_account') {
    $savingsType = $_POST['savings_type'] ?? '';
    $initialDeposit = floatval($_POST['initial_deposit'] ?? 0);

    // Validate savings type
    $validTypes = ['Regular', 'Fixed', 'Special'];
    if (!in_array($savingsType, $validTypes)) {
        $_SESSION['error_message'] = "Invalid savings account type";
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Define minimum deposits for each type
    $minimumDeposits = [
        'Regular' => 100,
        'Fixed' => 1000,
        'Special' => 50000
    ];

    $minimumRequired = $minimumDeposits[$savingsType];

    // Validate initial deposit based on savings type
    if ($initialDeposit < $minimumRequired) {
        $_SESSION['error_message'] = $savingsType . " Savings requires a minimum deposit of ₱" . number_format($minimumRequired, 2);
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Check if user has sufficient balance
    $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['error_message'] = "User account not found";
        header("Location: ../user_dashboard.php");
        exit;
    }

    if ($user['Balance'] < $initialDeposit) {
        $_SESSION['error_message'] = "Insufficient balance for initial deposit. Available: ₱" . number_format($user['Balance'], 2);
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Define interest rates based on savings type
    $interestRates = [
        'Regular' => 2.5,
        'Fixed' => 3.5,
        'Special' => 5.0
    ];

    $interestRate = $interestRates[$savingsType] ?? 2.5;

    // Create savings account with Pending status and initial deposit
    $result = createSavingsAccountWithDeposit($userId, $savingsType, $interestRate, $initialDeposit);

    if ($result['success']) {
        $_SESSION['success_message'] = "Savings account created successfully! Waiting for admin approval.";
        $_SESSION['new_savings_id'] = $result['savings_id'];
    } else {
        $_SESSION['error_message'] = "Failed to create savings account: " . $result['error'];
    }

    header("Location: ../user_dashboard.php");
    exit;
}

// ==========================================
// DEPOSIT TO SAVINGS
// ==========================================
if ($action === 'deposit') {
    $savingsId = $_POST['savings_id'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount <= 0) {
        $_SESSION['error_message'] = "Invalid deposit amount";
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Check if user has sufficient balance for deposit
    $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['Balance'] < $amount) {
        $_SESSION['error_message'] = "Insufficient balance in main account. Available: ₱" . number_format($user['Balance'], 2);
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $result = depositToSavings($userId, $savingsId, $amount);

    if ($result['success']) {
        $_SESSION['success_message'] = "Successfully deposited ₱" . number_format($amount, 2) . " to savings";
    } else {
        $_SESSION['error_message'] = "Deposit failed: " . $result['error'];
    }

    header("Location: ../user_dashboard.php");
    exit;
}

// ==========================================
// WITHDRAW FROM SAVINGS
// ==========================================
if ($action === 'withdraw') {
    $savingsId = $_POST['savings_id'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if ($amount <= 0) {
        $_SESSION['error_message'] = "Invalid withdrawal amount";
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Get savings account details for validation
    $stmt = $conn->prepare("SELECT savings_type, balance, created_at FROM savings_accounts WHERE savings_id = ? AND ID = ? AND status = 'Active'");
    $stmt->bind_param("si", $savingsId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $savingsAccount = $result->fetch_assoc();

    if (!$savingsAccount) {
        $_SESSION['error_message'] = "Savings account not found or not active";
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Validate withdrawal based on savings type
    if ($savingsAccount['savings_type'] === 'Fixed') {
        // Check if 3 months have passed
        $createdDate = new DateTime($savingsAccount['created_at']);
        $currentDate = new DateTime();
        $interval = $createdDate->diff($currentDate);
        $monthsPassed = ($interval->y * 12) + $interval->m;

        if ($monthsPassed < 3) {
            $_SESSION['error_message'] = "Fixed Savings has a 3-month lock-in period. You can withdraw after " . (3 - $monthsPassed) . " more month(s)";
            header("Location: ../user_dashboard.php");
            exit;
        }
    }

    if ($savingsAccount['savings_type'] === 'Special') {
        // Must maintain minimum balance of 50,000
        $remainingBalance = $savingsAccount['balance'] - $amount;
        if ($remainingBalance < 50000 && $remainingBalance > 0) {
            $_SESSION['error_message'] = "Special Savings must maintain a minimum balance of ₱50,000. Maximum withdrawal: ₱" . number_format($savingsAccount['balance'] - 50000, 2);
            header("Location: ../user_dashboard.php");
            exit;
        }
    }

    // Check if sufficient balance in savings
    if ($amount > $savingsAccount['balance']) {
        $_SESSION['error_message'] = "Insufficient balance in savings account. Available: ₱" . number_format($savingsAccount['balance'], 2);
        header("Location: ../user_dashboard.php");
        exit;
    }

    $result = withdrawFromSavings($userId, $savingsId, $amount);

    if ($result['success']) {
        $_SESSION['success_message'] = "Successfully withdrew ₱" . number_format($amount, 2) . " from savings";
    } else {
        $_SESSION['error_message'] = "Withdrawal failed: " . $result['error'];
    }

    header("Location: ../user_dashboard.php");
    exit;
}

// ==========================================
// CLOSE SAVINGS ACCOUNT
// ==========================================
if ($action === 'close_account') {
    $savingsId = $_POST['savings_id'] ?? '';

    // First, withdraw all remaining balance
    $stmt = $conn->prepare("SELECT balance FROM savings_accounts WHERE savings_id = ? AND ID = ?");
    $stmt->bind_param("si", $savingsId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account && $account['balance'] > 0) {
        withdrawFromSavings($userId, $savingsId, $account['balance']);
    }

    // Update status to Closed
    $stmt = $conn->prepare("UPDATE savings_accounts SET status = 'Closed' WHERE savings_id = ? AND ID = ?");
    $stmt->bind_param("si", $savingsId, $userId);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Savings account closed successfully";
    } else {
        $_SESSION['error_message'] = "Failed to close account";
    }

    header("Location: ../user_dashboard.php");
    exit;
}

// Invalid action
$_SESSION['error_message'] = "Invalid action";
header("Location: ../user_dashboard.php");
exit;
?>
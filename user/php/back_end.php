<?php
// CONNECTION (keep as is)
$conn = new mysqli('localhost', 'root', '', 'bank_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Make sure $userId is passed from dashboard.php
if (!isset($userId) || $userId == 0) {
    die("Invalid user.");
}

// ================================
// FETCH USER ACCOUNT DATA
// ================================
$sql = "SELECT FirstName, Email, Phone, Balance FROM user_accounts WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) die("User not found.");

// ================================
// ASSIGN VARIABLES (USED BY UI)
// ================================
$userName = $user['FirstName'];
$userEmail = $user['Email'];
$userPhone = $user['Phone'];
$totalBalance = $user['Balance'];
$mainAccountNumber = "ACC-" . str_pad($userId, 10, "0", STR_PAD_LEFT);

// ================================
// FETCH TRANSACTIONS
// ================================
$transactions = [];
$txSql = "SELECT created_at, description, amount, transaction_type, icon
          FROM transactions
          WHERE user_id = ?
          ORDER BY created_at DESC
          LIMIT 5";
$txStmt = $conn->prepare($txSql);
$txStmt->bind_param("i", $userId);
$txStmt->execute();
$txResult = $txStmt->get_result();

while ($row = $txResult->fetch_assoc()) {
    $transactions[] = [
        // FIXED: Don't format here, keep the raw datetime
        'created_at' => $row['created_at'],  // Changed from: date("M d", strtotime($row['created_at']))
        'desc' => $row['description'],
        'amount' => $row['amount'],
        'transaction_type' => $row['transaction_type'],
        'icon' => $row['icon']
    ];
}

// Add these functions to your existing back_end.php file

// ==========================================
// SAVINGS ACCOUNT FUNCTIONS
// ==========================================

/**
 * Get all savings accounts for a user
 */
function getUserSavingsAccounts($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            sa.savings_id,
            sa.savings_type,
            sa.interest_rate,
            sa.balance,
            sa.status,
            sa.last_interest_date,
            sa.created_at
        FROM savings_accounts sa
        WHERE sa.ID = ?
        ORDER BY sa.created_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get total savings balance for a user
 */
function getTotalSavings($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(balance), 0) as total
        FROM savings_accounts
        WHERE ID = ? AND status = 'Active'
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

/**
 * Get total interest earned this year
 */
function getYearlyInterestEarned($userId) {
    global $conn;
    $currentYear = date('Y');
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total_interest
        FROM savings_transactions st
        JOIN savings_accounts sa ON st.savings_id = sa.savings_id
        WHERE sa.ID = ? 
        AND st.transaction_type = 'Interest'
        AND YEAR(st.created_at) = ?
    ");
    $stmt->bind_param("ii", $userId, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_interest'];
}

/**
 * Create a new savings account with initial deposit
 */
function createSavingsAccountWithDeposit($userId, $savingsType, $interestRate, $initialDeposit) {
    global $conn;
    $conn->begin_transaction();
    
    try {
        // Generate unique savings ID
        $savingsId = 'SAV' . str_pad($userId, 4, '0', STR_PAD_LEFT) . substr(uniqid(), -3);
        
        // Check user balance
        $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user['Balance'] < $initialDeposit) {
            throw new Exception("Insufficient balance");
        }
        
        // Deduct from main balance
        $newMainBalance = $user['Balance'] - $initialDeposit;
        $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
        $stmt->bind_param("di", $newMainBalance, $userId);
        $stmt->execute();
        
        // Create savings account with Pending status and initial deposit
        $stmt = $conn->prepare("
            INSERT INTO savings_accounts (savings_id, ID, savings_type, interest_rate, balance, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->bind_param("sisdd", $savingsId, $userId, $savingsType, $interestRate, $initialDeposit);
        $stmt->execute();
        
        // Record savings transaction
        $stmt = $conn->prepare("
            INSERT INTO savings_transactions (savings_id, transaction_type, amount, balance_after, created_at)
            VALUES (?, 'Deposit', ?, ?, NOW())
        ");
        $stmt->bind_param("sdd", $savingsId, $initialDeposit, $initialDeposit);
        if (!$stmt->execute()) {
            throw new Exception("Failed to record initial deposit in savings_transactions: " . $stmt->error);
        }
        
        // Record main account transaction
        $description = "Initial deposit to Savings ($savingsId)";
        $icon = "bi-piggy-bank";
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon)
            VALUES (?, 'Cash Out', ?, ?, ?, ?)
        ");
        $stmt->bind_param("iddss", $userId, $initialDeposit, $newMainBalance, $description, $icon);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'savings_id' => $savingsId];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Deposit money into savings account
 */
function depositToSavings($userId, $savingsId, $amount) {
    global $conn;
    $conn->begin_transaction();
    
    try {
        // Check if user has sufficient balance
        $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user['Balance'] < $amount) {
            throw new Exception("Insufficient balance");
        }
        
        // Deduct from main balance
        $newMainBalance = $user['Balance'] - $amount;
        $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
        $stmt->bind_param("di", $newMainBalance, $userId);
        $stmt->execute();
        
        // Add to savings account
        $stmt = $conn->prepare("
            UPDATE savings_accounts 
            SET balance = balance + ? 
            WHERE savings_id = ? AND ID = ?
        ");
        $stmt->bind_param("dsi", $amount, $savingsId, $userId);
        $stmt->execute();
        
        // Get new savings balance
        $stmt = $conn->prepare("SELECT balance FROM savings_accounts WHERE savings_id = ?");
        $stmt->bind_param("s", $savingsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $savings = $result->fetch_assoc();
        $newSavingsBalance = $savings['balance'];
        
        // Record savings transaction
        $stmt = $conn->prepare("
            INSERT INTO savings_transactions (savings_id, transaction_type, amount, balance_after, created_at)
            VALUES (?, 'Deposit', ?, ?, NOW())
        ");
        $stmt->bind_param("sdd", $savingsId, $amount, $newSavingsBalance);
        if (!$stmt->execute()) {
            error_log("Savings transaction insert failed: " . $stmt->error);
            throw new Exception("Failed to record deposit in savings_transactions: " . $stmt->error);
        }
        
        // Record main account transaction
        $description = "Transfer to Savings ($savingsId)";
        $icon = "bi-piggy-bank";
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon)
            VALUES (?, 'Cash Out', ?, ?, ?, ?)
        ");
        $stmt->bind_param("iddss", $userId, $amount, $newMainBalance, $description, $icon);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'new_balance' => $newSavingsBalance];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Check if withdrawal is allowed for a savings account
 */
function checkWithdrawalEligibility($savingsId, $withdrawAmount) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT savings_type, balance, created_at 
        FROM savings_accounts 
        WHERE savings_id = ? AND status = 'Active'
    ");
    $stmt->bind_param("s", $savingsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    
    if (!$account) {
        return ['allowed' => false, 'reason' => 'Account not found or not active'];
    }
    
    // Check balance
    if ($account['balance'] < $withdrawAmount) {
        return ['allowed' => false, 'reason' => 'Insufficient balance'];
    }
    
    // FIXED: Check 3-month restriction
    if ($account['savings_type'] === 'Fixed') {
        $createdDate = new DateTime($account['created_at']);
        $today = new DateTime();
        $monthsDiff = $createdDate->diff($today)->m + ($createdDate->diff($today)->y * 12);
        
        if ($monthsDiff < 3) {
            $canWithdrawDate = $createdDate->modify('+3 months')->format('F j, Y');
            return [
                'allowed' => false, 
                'reason' => "Fixed savings cannot be withdrawn until 3 months (Available: {$canWithdrawDate})"
            ];
        }
    }
    
    // SPECIAL: Check minimum balance of 50,000
    if ($account['savings_type'] === 'Special') {
        $balanceAfter = $account['balance'] - $withdrawAmount;
        if ($balanceAfter < 50000) {
            return [
                'allowed' => false,
                'reason' => 'Special savings must maintain minimum ₱50,000. Maximum withdrawal: ₱' . number_format($account['balance'] - 50000, 2)
            ];
        }
    }
    
    return ['allowed' => true, 'reason' => ''];
}

/**
 * Withdraw money from savings account
 */
function withdrawFromSavings($userId, $savingsId, $amount) {
    global $conn;
    $conn->begin_transaction();
    
    try {
        // Check savings balance and get account details
        $stmt = $conn->prepare("
            SELECT balance, savings_type, created_at FROM savings_accounts 
            WHERE savings_id = ? AND ID = ? AND status = 'Active'
        ");
        $stmt->bind_param("si", $savingsId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $savings = $result->fetch_assoc();
        
        if (!$savings) {
            throw new Exception("Savings account not found or not active");
        }
        
        // RULE 1: Check if account has sufficient balance
        if ($savings['balance'] < $amount) {
            throw new Exception("Insufficient savings balance");
        }
        
        // RULE 2: FIXED accounts - Cannot withdraw for 3 months
        if ($savings['savings_type'] === 'Fixed') {
            $createdDate = new DateTime($savings['created_at']);
            $today = new DateTime();
            $monthsDiff = $createdDate->diff($today)->m + ($createdDate->diff($today)->y * 12);
            
            if ($monthsDiff < 3) {
                $canWithdrawDate = $createdDate->modify('+3 months')->format('F j, Y');
                throw new Exception("Fixed savings cannot be withdrawn until 3 months. You can withdraw starting {$canWithdrawDate}");
            }
        }
        
        // RULE 3: SPECIAL accounts - Must maintain minimum balance of ₱50,000
        if ($savings['savings_type'] === 'Special') {
            $balanceAfterWithdrawal = $savings['balance'] - $amount;
            if ($balanceAfterWithdrawal < 50000) {
                throw new Exception("Special savings must maintain a minimum balance of ₱50,000.00. Your balance after withdrawal would be ₱" . number_format($balanceAfterWithdrawal, 2));
            }
        }
        
        // Proceed with withdrawal
        $newSavingsBalance = $savings['balance'] - $amount;
        $stmt = $conn->prepare("
            UPDATE savings_accounts 
            SET balance = ? 
            WHERE savings_id = ?
        ");
        $stmt->bind_param("ds", $newSavingsBalance, $savingsId);
        $stmt->execute();
        
        // Add to main balance
        $stmt = $conn->prepare("SELECT Balance FROM user_accounts WHERE ID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        $newMainBalance = $user['Balance'] + $amount;
        $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
        $stmt->bind_param("di", $newMainBalance, $userId);
        $stmt->execute();
        
        // Record savings transaction
        $stmt = $conn->prepare("
            INSERT INTO savings_transactions (savings_id, transaction_type, amount, balance_after, created_at)
            VALUES (?, 'Withdraw', ?, ?, NOW())
        ");
        $stmt->bind_param("sdd", $savingsId, $amount, $newSavingsBalance);
        if (!$stmt->execute()) {
            error_log("Savings transaction insert failed: " . $stmt->error);
            throw new Exception("Failed to record withdrawal in savings_transactions: " . $stmt->error);
        }
        
        // Record main account transaction
        $description = "Withdrawal from Savings ($savingsId)";
        $icon = "bi-arrow-down-circle";
        $stmt = $conn->prepare("
            INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon)
            VALUES (?, 'Cash In', ?, ?, ?, ?)
        ");
        $stmt->bind_param("iddss", $userId, $amount, $newMainBalance, $description, $icon);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'new_balance' => $newSavingsBalance];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Get savings transaction history
 */
function getSavingsTransactions($savingsId, $limit = 50)
{
    global $conn;

    // Validate input
    if (empty($savingsId)) {
        error_log("getSavingsTransactions: Empty savings_id provided");
        return [];
    }

    try {
        $stmt = $conn->prepare("
            SELECT 
                transaction_id,
                transaction_type,
                amount,
                balance_after,
                created_at
            FROM savings_transactions
            WHERE savings_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");

        if (!$stmt) {
            error_log("getSavingsTransactions: Failed to prepare statement - " . $conn->error);
            return [];
        }

        $stmt->bind_param("si", $savingsId, $limit);

        if (!$stmt->execute()) {
            error_log("getSavingsTransactions: Failed to execute statement - " . $stmt->error);
            return [];
        }

        $result = $stmt->get_result();
        $transactions = $result->fetch_all(MYSQLI_ASSOC);

        error_log("getSavingsTransactions: Found " . count($transactions) . " transactions for savings_id: {$savingsId}");

        return $transactions;

    } catch (Exception $e) {
        error_log("getSavingsTransactions exception: " . $e->getMessage());
        return [];
    }
}
/**
 * Calculate and apply interest (should be run monthly via cron job)
 */
function applyInterestToSavings($savingsId) {
    global $conn;
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("
            SELECT balance, interest_rate, last_interest_date
            FROM savings_accounts
            WHERE savings_id = ? AND status = 'Active'
        ");
        $stmt->bind_param("s", $savingsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();
        
        if (!$account) {
            throw new Exception("Savings account not found");
        }
        
        // Calculate monthly interest (annual rate / 12)
        $monthlyRate = $account['interest_rate'] / 12 / 100;
        $interestAmount = $account['balance'] * $monthlyRate;
        $newBalance = $account['balance'] + $interestAmount;
        
        // Update balance and last interest date
        $stmt = $conn->prepare("
            UPDATE savings_accounts 
            SET balance = ?, last_interest_date = CURDATE()
            WHERE savings_id = ?
        ");
        $stmt->bind_param("ds", $newBalance, $savingsId);
        $stmt->execute();
        
        // Record interest transaction
        $stmt = $conn->prepare("
            INSERT INTO savings_transactions (savings_id, transaction_type, amount, balance_after)
            VALUES (?, 'Interest', ?, ?)
        ");
        $stmt->bind_param("sdd", $savingsId, $interestAmount, $newBalance);
        $stmt->execute();
        
        $conn->commit();
        return ['success' => true, 'interest' => $interestAmount];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ==========================================
// INITIALIZE SAVINGS DATA FOR DASHBOARD
// ==========================================

// Get savings data
$savingsAccounts = getUserSavingsAccounts($userId);
$totalSavings = getTotalSavings($userId);
$interestEarned = number_format(getYearlyInterestEarned($userId), 2);

// Calculate next payout date (1st of next month)
$nextMonth = date('F j, Y', strtotime('first day of next month'));
$nextPayoutDate = $nextMonth;


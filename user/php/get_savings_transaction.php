<?php
/**
 * AJAX endpoint to get savings transaction history
 * File: php/get_savings_transaction.php
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

session_start();

// Set JSON header first
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Not authenticated',
        'debug' => 'Session user_id not set'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

// Check if savings_id is provided
$savingsId = $_GET['savings_id'] ?? '';

if (empty($savingsId)) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing savings_id parameter',
        'debug' => 'savings_id not provided in GET request'
    ]);
    exit;
}

// Include database connection
require_once 'back_end.php';

// Check if connection exists
if (!isset($conn)) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'debug' => 'Connection object not available'
    ]);
    exit;
}

try {
    // Verify that the savings account belongs to the user
    $stmt = $conn->prepare("SELECT ID FROM savings_accounts WHERE savings_id = ?");

    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("s", $savingsId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if (!$account) {
        echo json_encode([
            'success' => false,
            'error' => 'Savings account not found',
            'debug' => "No account found with savings_id: {$savingsId}"
        ]);
        exit;
    }

    if ($account['ID'] != $userId) {
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized access',
            'debug' => "Account belongs to user {$account['ID']}, requesting user is {$userId}"
        ]);
        exit;
    }

    // Get transactions using the function from back_end.php
    if (function_exists('getSavingsTransactions')) {
        $transactions = getSavingsTransactions($savingsId, 50);
    } else {
        // Fallback: Query directly if function doesn't exist
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
            LIMIT 50
        ");

        if (!$stmt) {
            throw new Exception("Failed to prepare transaction query: " . $conn->error);
        }

        $stmt->bind_param("s", $savingsId);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
    }

    // Return successful response
    echo json_encode([
        'success' => true,
        'transactions' => $transactions,
        'count' => count($transactions),
        'savings_id' => $savingsId
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("get_savings_transaction.php error: " . $e->getMessage());

    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load transactions',
        'debug' => $e->getMessage()
    ]);
}

exit;
?>
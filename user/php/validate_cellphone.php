<?php
// File: php/validate_cellphone.php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $conn = new mysqli('localhost', 'root', '', 'bank_db');

    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    $cellphone = $_GET['cellphone'] ?? '';

    // Validate format
    if (!preg_match('/^09[0-9]{9}$/', $cellphone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid cellphone format. Use: 09XXXXXXXXX'
        ]);
        exit;
    }

    // Check if cellphone exists in user_accounts
    $stmt = $conn->prepare("SELECT ID, Status, FirstName, LastName FROM user_accounts WHERE Phone = ?");
    $stmt->bind_param("s", $cellphone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Cellphone not found in database
        echo json_encode([
            'success' => false,
            'message' => 'Invalid cellphone number. Store account not found.'
        ]);
    } else {
        // Cellphone exists - check status
        $user = $result->fetch_assoc();

        // Check if Status is 'Approved' (only approved accounts are valid)
        if (strcasecmp($user['Status'], 'Approved') === 0) {
            // Account is approved - this is VALID for cash out
            echo json_encode([
                'success' => true,
                'message' => 'Valid store account: ' . $user['FirstName'] . ' ' . $user['LastName'],
                'recipient_name' => $user['FirstName'] . ' ' . $user['LastName']
            ]);
        } else {
            // Account exists but not approved (Pending/Rejected) - INVALID
            echo json_encode([
                'success' => false,
                'message' => 'Account (' . $user['FirstName'] . ' ' . $user['LastName'] . ') is not approved yet. Status: ' . $user['Status']
            ]);
        }
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Validation error: ' . $e->getMessage()
    ]);
}
?>
<?php
// File: php/validate_cellphone.php
session_start();
header('Content-Type: application/json');


error_reporting(E_ALL);
ini_set('display_errors', 0);


// Function to mask name like GCash (e.g., "Juan Dela Cruz" -> "J*** D*** C***")
function maskName($firstName, $lastName)
{
    $maskedFirst = '';
    $maskedLast = '';


    // Mask first name - show first letter + asterisks
    if (!empty($firstName)) {
        $maskedFirst = mb_substr($firstName, 0, 1) . str_repeat('*', max(3, mb_strlen($firstName) - 1));
    }


    // Mask last name - show first letter + asterisks
    if (!empty($lastName)) {
        $maskedLast = mb_substr($lastName, 0, 1) . str_repeat('*', max(3, mb_strlen($lastName) - 1));
    }


    return trim($maskedFirst . ' ' . $maskedLast);
}


try {
    $conn = new mysqli('localhost', 'root', '', 'bank_db');


    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }


    $cellphone = $_GET['cellphone'] ?? '';
    $userId    = $_SESSION['user_id'] ?? '';

    // Validate format
    if (!preg_match('/^09[0-9]{9}$/', $cellphone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid cellphone format. Use: 09XXXXXXXXX'
        ]);
        exit;
    }


    // Check if cellphone exists in user_accounts
$stmt = $conn->prepare("
        SELECT ID, Status, FirstName, LastName
        FROM user_accounts
        WHERE Phone = ?
        AND ID != ? AND Role = 'User' 
        LIMIT 1
    ");
    $stmt->bind_param("si", $cellphone, $userId);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 0) {
        // Cellphone not found in database
        echo json_encode([
            'success' => false,
            'message' => 'Invalid cellphone number. Account not found.'
        ]);
    } else {
        // Cellphone exists - check status
        $user = $result->fetch_assoc();


        // Check if Status is 'Approved' (only approved accounts are valid)
        if (strcasecmp($user['Status'], 'Approved') === 0) {
            // Account is approved - return masked name
            $maskedName = maskName($user['FirstName'], $user['LastName']);


            echo json_encode([
                'success' => true,
                'message' => 'Valid account: ' . $maskedName,
                'recipient_name' => $maskedName,
                'full_name' => $user['FirstName'] . ' ' . $user['LastName'] // Keep this for backend use
            ]);
        } else {
            // Account exists but not approved (Pending/Rejected) - INVALID
            echo json_encode([
                'success' => false,
                'message' => 'Account is not approved yet. Status: ' . $user['Status']
            ]);
        }
        // Example: "Juan Dela Cruz" -> "Ju** De** Cr**"
        function maskName($firstName, $lastName)
        {
            $maskedFirst = '';
            $maskedLast = '';


            if (!empty($firstName)) {
                $visibleChars = min(2, mb_strlen($firstName));
                $maskedFirst = mb_substr($firstName, 0, $visibleChars) .
                    str_repeat('*', max(0, mb_strlen($firstName) - $visibleChars));
            }


            if (!empty($lastName)) {
                $visibleChars = min(2, mb_strlen($lastName));
                $maskedLast = mb_substr($lastName, 0, $visibleChars) .
                    str_repeat('*', max(0, mb_strlen($lastName) - $visibleChars));
            }


            return trim($maskedFirst . ' ' . $maskedLast);
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


// Example: "Juan Dela Cruz" -> "J**n D**a C**z"
?>




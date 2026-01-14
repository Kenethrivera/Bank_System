<?php
session_start();

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? 0;
if ($userId == 0) {
    $_SESSION['cashout_error'] = "Invalid user. Please log in.";
    header("Location: ../user_dashboard.php");
    exit;
}

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'bank_db');
if ($conn->connect_error) {
    $_SESSION['cashout_error'] = "Connection failed: " . $conn->connect_error;
    header("Location: ../user_dashboard.php");
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method = $_POST['method'] ?? '';
    $description = $_POST['description'] ?? '';
    $icon = $_POST['icon'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    // Basic validation
    if (!$method || !$description || !$icon || $amount <= 0) {
        $_SESSION['cashout_error'] = "Invalid input. Please fill in all required fields.";
        header("Location: ../user_dashboard.php");
        exit;
    }

    $recipientId = null;
    $recipientPhone = null;

    // Validate specific method requirements
    if ($method === 'Over the Counter') {
        $location = $_POST['location'] ?? '';
        if (!$location) {
            $_SESSION['cashout_error'] = "Please select a counter location.";
            header("Location: ../user_dashboard.php");
            exit;
        }
        $description = "Cash Out - Over the Counter - " . $location;
    } else if ($method === 'Cash Machine') {
        $machine = $_POST['machine'] ?? '';
        if (!$machine) {
            $_SESSION['cashout_error'] = "Please select a machine type.";
            header("Location: ../user_dashboard.php");
            exit;
        }
        $description = "Cash Out - Cash Machine - " . $machine;
    } else if ($method === 'Sari-Sari Store') {
        $storeCellphone = $_POST['store_cellphone'] ?? '';

        // Validate cellphone format
        if (!preg_match('/^09[0-9]{9}$/', $storeCellphone)) {
            $_SESSION['cashout_error'] = "Invalid cellphone format. Use: 09XXXXXXXXX";
            header("Location: ../user_dashboard.php");
            exit;
        }

        // Check if cellphone exists and get account status
        $stmt = $conn->prepare("SELECT ID, Status, FirstName, LastName FROM user_accounts WHERE Phone = ?");
        $stmt->bind_param("s", $storeCellphone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Cellphone number doesn't exist in database
            $_SESSION['cashout_error'] = "Invalid cellphone number. Store account not found.";
            $stmt->close();
            $conn->close();
            header("Location: ../user_dashboard.php");
            exit;
        }

        $storeUser = $result->fetch_assoc();

        // Check if account status is NOT 'Approved'
        if (strcasecmp($storeUser['Status'], 'Approved') !== 0) {
            $_SESSION['cashout_error'] = "Invalid cellphone number. The account (" . $storeUser['FirstName'] . " " . $storeUser['LastName'] . ") is not approved yet.";
            $stmt->close();
            $conn->close();
            header("Location: ../user_dashboard.php");
            exit;
        }

        // Account exists and is approved - valid for cash out
        $recipientId = $storeUser['ID'];
        $stmt->close();

        $recipientPhone = $storeCellphone;
        $description = "Cash Out - Sari-Sari Store - " . $storeCellphone;
    }

    // Fetch current user balance
    $stmt = $conn->prepare("SELECT Balance, FirstName, LastName FROM user_accounts WHERE ID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $_SESSION['cashout_error'] = "User account not found.";
        $stmt->close();
        $conn->close();
        header("Location: ../user_dashboard.php");
        exit;
    }

    $currentBalance = floatval($user['Balance']);
    $senderFirstName = $user['FirstName'];
    $senderLastName = $user['LastName'];
    $stmt->close();

    // Check if sufficient balance
    if ($amount > $currentBalance) {
        $_SESSION['cashout_error'] = "Insufficient balance. Available: ₱" . number_format($currentBalance, 2);
        $conn->close();
        header("Location: ../user_dashboard.php");
        exit;
    }

    // Calculate new balance
    $newBalance = $currentBalance - $amount;

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update sender's balance
        $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
        $stmt->bind_param("di", $newBalance, $userId);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update sender balance");
        }
        $stmt->close();

        // Insert sender's transaction
        $transactionType = 'Cash Out';
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isddss", $userId, $transactionType, $amount, $newBalance, $description, $icon);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert sender transaction");
        }
        $stmt->close();

        // If Sari-Sari Store, credit the recipient's balance
        if ($method === 'Sari-Sari Store' && $recipientId) {
            // Get recipient's current balance
            $stmt = $conn->prepare("SELECT Balance, FirstName, LastName FROM user_accounts WHERE ID = ?");
            $stmt->bind_param("i", $recipientId);
            $stmt->execute();
            $result = $stmt->get_result();
            $recipient = $result->fetch_assoc();

            $recipientCurrentBalance = floatval($recipient['Balance']);
            $recipientNewBalance = $recipientCurrentBalance + $amount;
            $stmt->close();

            // Update recipient balance
            $stmt = $conn->prepare("UPDATE user_accounts SET Balance = ? WHERE ID = ?");
            $stmt->bind_param("di", $recipientNewBalance, $recipientId);

            if (!$stmt->execute()) {
                throw new Exception("Failed to update recipient balance");
            }
            $stmt->close();

            // Insert recipient's transaction
            $recipientTransactionType = 'Received Money';
            $recipientDescription = "Received from " . $senderFirstName . " " . $senderLastName;
            $recipientIcon = 'bi-currency-exchange';

            $stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_type, amount, balance_after, description, icon, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isddss", $recipientId, $recipientTransactionType, $amount, $recipientNewBalance, $recipientDescription, $recipientIcon);

            if (!$stmt->execute()) {
                throw new Exception("Failed to insert recipient transaction: " . $stmt->error);
            }
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();

        if ($method === 'Sari-Sari Store' && $recipientId) {
            $_SESSION['cashout_success'] = "Cash out successful! Amount: ₱" . number_format($amount, 2) . " sent to store and credited to recipient's account.";
        } else {
            $_SESSION['cashout_success'] = "Cash out successful! Amount: ₱" . number_format($amount, 2);
        }

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['cashout_error'] = "Transaction failed: " . $e->getMessage();
    }

    $conn->close();
    header("Location: ../user_dashboard.php");
    exit;
}

// If not POST request
$_SESSION['cashout_error'] = "Invalid request method.";
header("Location: ../user_dashboard.php");
exit;
?>
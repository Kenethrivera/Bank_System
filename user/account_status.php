<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'bank_db');

// 1. Security: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Fetch latest status
$userId = $_SESSION['user_id'];
$query = "SELECT FirstName, LastName, Status FROM user_accounts WHERE ID = '$userId'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// 3. Logic: If approved, auto-redirect to dashboard
if ($user['Status'] === 'Approved') {
    header("Location: user_dashboard.php");
    exit;
}

// 4. Handle Logout logic inline
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Status | BangisBank</title>
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex min-vh-100 align-items-center justify-content-center p-4">
        <div class="card card-custom p-5 bg-white shadow-lg text-center" style="max-width: 450px; width: 100%;">
            
            <div class="mb-4">
                <?php if ($user['Status'] === 'Pending'): ?>
                    <div class="mx-auto bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-hourglass-split text-warning fs-1"></i>
                    </div>
                    <h2 class="mt-3 fw-bold text-dark">Waiting for Approval</h2>
                <?php else: ?>
                    <div class="mx-auto bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                    </div>
                    <h2 class="mt-3 fw-bold text-dark">Account Rejected</h2>
                <?php endif; ?>
            </div>

            <p class="text-muted mb-4">
                <?php if ($user['Status'] === 'Pending'): ?>
                    Hello, <strong><?php echo htmlspecialchars($user['FirstName']); ?></strong>. 
                    Your account has been created successfully but is currently under review by our administrators. 
                    Please check back later.
                <?php else: ?>
                    Hello, <strong><?php echo htmlspecialchars($user['FirstName']); ?></strong>. 
                    Unfortunately, your account application has been rejected by our administrators. 
                    Please contact support for more details.
                <?php endif; ?>
            </p>

            <div class="d-grid gap-2">
                <a href="account_status.php" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh Status
                </a>
                
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#contactModal">
                    <i class="bi bi-headset me-2"></i>Contact Support
                </button>

                <a href="?logout=true" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-left me-2"></i>Back to Login
                </a>
            </div>

        </div>
    </div>

    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="contactModalLabel">
                        <i class="bi bi-headset me-2"></i>Customer Support
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <p class="text-muted mb-4">Have questions about your application status? Reach out to us through any of the channels below.</p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3">
                            <i class="bi bi-envelope-fill text-primary fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Email Support</small>
                            <span class="fw-bold">support@bangisbank.com</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light p-3 rounded-circle me-3">
                            <i class="bi bi-telephone-fill text-success fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Hotline (24/7)</small>
                            <span class="fw-bold">1-800-BANGIS-HELP</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="bg-light p-3 rounded-circle me-3">
                            <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Main Branch</small>
                            <span class="fw-bold">123 Finance St, Makati City</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
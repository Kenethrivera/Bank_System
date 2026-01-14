<?php

$cashoutSuccessMessage = '';
$cashoutErrorMessage = '';

if (isset($_SESSION['cashout_success'])) {
    $cashoutSuccessMessage = $_SESSION['cashout_success'];
    unset($_SESSION['cashout_success']);
}

if (isset($_SESSION['cashout_error'])) {
    $cashoutErrorMessage = $_SESSION['cashout_error'];
    unset($_SESSION['cashout_error']);
}

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];  // pass this to back_end.php
include 'php/back_end.php';
require_once 'php/users_loans_backend.php';
?>


<!doctype html>

<head>
    <title>Banko User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    
 <!-- HEADER NAVIGATION BAR -->

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="#">
                <i class="bi bi-bank fs-3"></i> Banko
            </a>

            <div class="d-flex justify-content-center flex-grow-1">
                <a class="nav-link active" onclick="showSection('home', this)">
                    <i class="bi bi-house me-1"></i> Home
                </a>
                <a class="nav-link" onclick="showSection('savings', this)">
                    <i class="bi bi-piggy-bank me-1"></i> Savings
                </a>
                <a class="nav-link" onclick="showSection('loan', this)">
                    <i class="bi bi-cash-stack me-1"></i> Loan
                </a>
            </div>

            <div class="d-flex align-items-center">
                <i class="bi bi-question-circle me-3 fs-5 text-secondary cursor-pointer" data-bs-toggle="modal"
                    data-bs-target="#faqModal" title="Help"></i>

                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer"
                    style="width: 40px; height: 40px;" data-bs-toggle="dropdown">
                    <i class="bi bi-person-fill"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2">
                    <li>
                        <h6 class="dropdown-header">Hello, <?php echo $userName; ?></h6>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal"
                            data-bs-target="#accountsModal">
                            <i class="bi bi-wallet2 me-2"></i>Active Accounts & Loans
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal"
                            data-bs-target="#usernameModal">
                            <i class="bi bi-person-gear me-2"></i>Change Username
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal"
                            data-bs-target="#passwordModal">
                            <i class="bi bi-key me-2"></i>Change Password
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger rounded-2" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<!-- Main dashboard content -->
    <div class="container py-5">

        <div id="home" class="dashboard-section active">
             <h4 class="fw-bold mb-1">Hello, <?php echo $userName; ?>!</h4>
            <p class="text-muted mb-4">Here is your daily financial overview.</p>
            
            <div class="stat-card blue mb-4">
                <div class="balance-card-header">
                    <small class="opacity-75">Available Balance</small>
                    <small class="opacity-75 font-monospace">Acct: <?php echo $mainAccountNumber; ?></small>
                </div>
            
                <div class="balance-card-body">
                    <div class="balance-amount" id="displayBalance">
                        ₱<?php echo number_format($totalBalance, 2); ?>
                    </div>
                    <i class="bi bi-eye balance-eye-icon" id="eyeIcon" onclick="toggleBalance()"></i>
                </div>
            </div>

<!-- Transactions buttons -->
            <div class="row g-4 mb-5">

                <!-- CASH IN  -->
                <div class="col-md-4">
                    <div class="btn-action btn-green-light" data-bs-toggle="modal" data-bs-target="#cashInModal">
                        <i class="bi bi-arrow-down fs-2 mb-2"></i>
                        <span>Cash In</span>
                    </div>
                </div>

                <!-- CASH IN MAIN MODAL -->
                <div class="modal fade" id="cashInModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Cash In</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p class="mb-3">Select a cash-in method:</p>
                                <div class="row g-3">

                                    <!-- Over the Counter -->
                                    <div class="col-12 col-md-4">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow"
                                            data-bs-toggle="modal" data-bs-target="#overCounterModal"
                                            data-bs-dismiss="modal">
                                            <i class="bi bi-building fs-1 text-success mb-2"></i>
                                            <div>Over the Counter</div>
                                        </div>
                                    </div>

                                    <!-- Cash In Machine -->
                                    <div class="col-12 col-md-4">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow"
                                            data-bs-toggle="modal" data-bs-target="#cashMachineModal"
                                            data-bs-dismiss="modal">
                                            <i class="bi bi-bank fs-1 text-primary mb-2"></i>
                                            <div>Cash In Machine</div>
                                        </div>
                                    </div>

                                    <!-- Sari-Sari Store -->
                                    <div class="col-12 col-md-4 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill d-flex flex-column justify-content-center"
                                            data-bs-toggle="modal" data-bs-target="#sariSariModal"
                                            data-bs-dismiss="modal">
                                            <i class="bi bi-shop fs-1 text-warning mb-2"></i>
                                            <div>Sari-Sari Store</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- OVER THE COUNTER MODAL -->
                <div class="modal fade" id="overCounterModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Over the Counter</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p class="mb-3">Choose a store:</p>
                                <div class="row g-3">

                                    <!-- 7-Eleven -->
                                    <div class="col-12 col-md-6 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('7-Eleven')">
                                            <img src="assets/logos/7eleven.png" alt="7-Eleven Logo"
                                                class="d-block mx-auto mb-2" style="height:35px; object-fit:contain;">
                                            <div>7-Eleven</div>
                                        </div>
                                    </div>

                                    <!-- Alfa Mart -->
                                    <div class="col-12 col-md-6 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('Alfa Mart')">
                                            <img src="assets/logos/alfamart.png" alt="Alfa Mart Logo"
                                                class="d-block mx-auto mb-2" style="height:35px; object-fit:contain;">
                                            <div>Alfa Mart</div>
                                        </div>
                                    </div>

                                    <!-- Shell Select -->
                                    <div class="col-12 col-md-6 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('Shell Select')">
                                            <img src="assets/logos/shellselect.png" alt="Shell Select Logo"
                                                class="d-block mx-auto mb-2" style="height:35px; object-fit:contain;">
                                            <div>Shell Select</div>
                                        </div>
                                    </div>

                                    <!-- Uncle John's -->
                                    <div class="col-12 col-md-6 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('Uncle John\'s')">
                                            <img src="assets/logos/unclejohns.png" alt="Uncle John's Logo"
                                                class="d-block mx-auto mb-2" style="height:35px; object-fit:contain;">
                                            <div>Uncle John's</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- CASH IN MACHINE MODAL -->
                <div class="modal fade" id="cashMachineModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Cash In Machine</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p class="mb-3">Choose a machine:</p>
                                <div class="row g-3">

                                    <div class="col-12 col-md-4 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('eTap')">
                                            <i class="bi bi-credit-card-2-front-fill fs-2 text-primary mb-2"></i>
                                            <div>eTap</div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('Pay&Go')">
                                            <i class="bi bi-cash-coin fs-2 text-success mb-2"></i>
                                            <div>Pay&Go</div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-4 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('TouchPay')">
                                            <i class="bi bi-bank fs-2 text-warning mb-2"></i>
                                            <div>TouchPay</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- SARI-SARI STORE MODAL -->
                <div class="modal fade" id="sariSariModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Sari-Sari Store</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <p class="mb-3">Choose a Sari-Sari Store:</p>
                                <div class="row g-3">

                                    <div class="col-12 d-flex">
                                        <div class="card text-center cursor-pointer p-3 shadow-sm hover-shadow flex-fill"
                                            onclick="showCashInInstructions('Sari-Sari Store')">
                                            <i class="bi bi-shop fs-2 text-warning mb-2"></i>
                                            <div>Sari-Sari Store</div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- REUSABLE CASH IN INSTRUCTIONS MODAL -->
                <div class="modal fade" id="cashInInstructionsModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="cashInModalTitle">Cash In Instructions</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body" id="cashInModalBody">
                                <!-- Instructions will be injected via JS -->
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
    <div class="btn-action btn-red-light" data-bs-toggle="modal" data-bs-target="#cashOutModal">
        <i class="bi bi-arrow-up fs-2 mb-2"></i>
        <span>Cash Out</span>
    </div>
</div>

        <!-- CASH OUT MODAL -->
        <div class="modal fade" id="cashOutModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Cash Out</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="fw-bold mb-3">Select Cash Out Method:</p>
                        <div class="d-flex gap-2 mb-3">
                            <div class="card text-center cursor-pointer flex-fill p-3 shadow-sm hover-shadow cashout-method-card" 
                                data-method="Over the Counter" data-description="Cash Out via Counter" data-icon="bi-wallet2">
                                <i class="bi bi-building fs-2 text-success mb-2"></i>
                                <div>Over the Counter</div>
                            </div>

                            <div class="card text-center cursor-pointer flex-fill p-3 shadow-sm hover-shadow cashout-method-card" 
                                data-method="Cash Machine" data-description="ATM Withdrawal" data-icon="bi-arrow-down-circle">
                                <i class="bi bi-bank fs-2 text-primary mb-2"></i>
                                <div>Cash Machine</div>
                            </div>

                            <div class="card text-center cursor-pointer flex-fill p-3 shadow-sm hover-shadow cashout-method-card" 
                                data-method="Sari-Sari Store" data-description="Cash Out via Sari-Sari Store" data-icon="bi-shop">
                                <i class="bi bi-shop fs-2 text-warning mb-2"></i>
                                <div>Sari-Sari Store</div>
                            </div>
                        </div>

                        <form id="cashOutForm" method="POST" action="php/process_cashout.php">
                            <input type="hidden" name="method" id="cashOutMethod" required>
                            <input type="hidden" name="description" id="cashOutDescription" required>
                            <input type="hidden" name="icon" id="cashOutIcon" required>

                            <!-- Selected Method Display -->
                            <div id="selectedMethodDisplay" class="alert alert-info mb-3" style="display:none;">
                                <strong>Selected Method:</strong> <span id="displayMethod"></span>
                            </div>

                            <!-- Over the Counter Options -->
                            <div id="counterOptions" class="mb-3" style="display:none;">
                                <label for="counterLocation" class="form-label fw-bold">Select Location:</label>
                                <select class="form-select" id="counterLocation" name="location">
                                    <option value="">-- Choose Location --</option>
                                    <option value="7-Eleven">7-Eleven</option>
                                    <option value="Uncle John">Uncle John</option>
                                    <option value="Alfamart">Alfamart</option>
                                    <option value="Shell Select">Shell Select</option>
                                </select>
                            </div>

                            <!-- Cash Machine Options -->
                            <div id="machineOptions" class="mb-3" style="display:none;">
                                <label for="machineType" class="form-label fw-bold">Select Machine:</label>
                                <select class="form-select" id="machineType" name="machine">
                                    <option value="">-- Choose Machine --</option>
                                    <option value="ETAP">ETAP</option>
                                    <option value="Pay & Go">Pay & Go</option>
                                    <option value="Touch and Pay">Touch and Pay</option>
                                </select>
                            </div>

                            <!-- Sari-Sari Store Options -->
                            <div id="storeOptions" class="mb-3" style="display:none;">
                                <label for="storeCellphone" class="form-label fw-bold">Store Cellphone Number:</label>
                                <input type="text" class="form-control" id="storeCellphone" name="store_cellphone" 
                                    placeholder="09XXXXXXXXX" pattern="09[0-9]{9}" maxlength="11">
                                <small class="text-muted">Format: 09XXXXXXXXX (11 digits)</small>
                                <div id="cellphoneWarning" class="text-danger mt-1" style="display:none;"></div>
                            </div>

                            <div class="mb-2 text-muted">
                                Available Balance: <span id="availableBalance">₱<?php echo number_format($totalBalance, 2); ?></span>
                            </div>

                            <div class="mb-3">
                                <label for="cashOutAmount" class="form-label fw-bold">Enter amount:</label>
                                <input type="number" min="1" step="0.01" class="form-control" id="cashOutAmount" name="amount" required>
                                <div class="text-danger mt-1" id="cashOutWarning" style="display:none;"></div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-danger w-100" id="confirmCashOutBtn">Confirm Cash Out</button>
                            </div>
                        </form>

                        <script>
                            const availableBalance = <?php echo $totalBalance; ?>;
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Modal -->
        <div class="modal fade" id="cashOutWarningModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Warning</h5>
                        <p class="mb-0" id="cashOutWarningMessage"></p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-warning px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div class="modal fade" id="cashOutErrorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Error</h5>
                        <p class="mb-0" id="cashOutErrorMessage"></p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Out Success Modal -->
<div class="modal fade" id="cashOutSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Cash Out Successful!</h5>
                <p class="mb-0"><?php echo htmlspecialchars($cashoutSuccessMessage); ?>
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cash Out Error Modal -->
        <div class="modal fade" id="cashOutErrorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Cash Out Failed</h5>
                        <p class="mb-0">
                            <?php echo htmlspecialchars($cashoutErrorMessage); ?>
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

                        <div class="col-md-4">
                    <div class="btn-action btn-purple-light" data-bs-toggle="modal" data-bs-target="#sendMoneyModal">
                        <i class="bi bi-send fs-2 mb-2"></i>
                        <span>Send Money</span>
                    </div>
                </div>
                <!-- SEND MONEY MODAL -->
                <div class="modal fade" id="sendMoneyModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 shadow-lg border-0">
                            <div class="modal-header bg-purple text-white">
                                <h5 class="modal-title fw-bold">Send Money</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>


                            <div class="modal-body p-4">
                                <div class="mb-3 text-center">
                                    <p class="mb-1 small text-muted">Available Balance</p>
                                    <h4 class="fw-bold" id="sendMoneyBalance">
                                        ₱<?php echo number_format($totalBalance, 2); ?></h4>
                                </div>

                                <p class="text-danger text-center" id="sendMoneyWarning" style="display:none;"></p>

                                <form id="sendMoneyForm" method="POST" action="php/send_money.php">
                                    <div class="mb-3">
                                        <label for="recipientNumber" class="form-label fw-semibold">Recipient
                                            Phone</label>
                                        <input type="text" class="form-control form-control-lg" id="recipientNumber"
                                            name="recipient_number" placeholder="09XXXXXXXXX" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="sendAmount" class="form-label fw-semibold">Amount</label>
                                        <input type="number" min="1" step="0.01" class="form-control form-control-lg"
                                            id="sendAmount" name="amount" placeholder="0.00" required>
                                    </div>

                                    <button type="button" class="btn btn-purple w-100 py-2 fw-semibold"
                                        id="sendMoneyNext">
                                        Send
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CONFIRMATION MODAL -->
                <div class="modal fade" id="sendMoneyConfirmModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 shadow-lg border-0">
                            <div class="modal-header bg-purple text-white rounded-top-4">
                                <h5 class="modal-title">Confirm Send Money</h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4 text-center">
                                <p class="mb-4">
                                    Send <span class="fw-bold" id="confirmSendAmount"></span> to <span class="fw-bold"
                                        id="confirmRecipient"></span>?
                                </p>
                                <button type="submit" form="sendMoneyForm"
                                    class="btn btn-purple w-100 py-2 fw-semibold">
                                    Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    const balanceDisplay = parseFloat(<?php echo $totalBalance; ?>);
                </script>
            </div>

            <!-- Transaction History -->
            <h5 class="fw-bold mb-3">Recent Transactions</h5>

            <div class="account-item p-0 overflow-hidden">
                <?php foreach ($transactions as $t):
                    if ($t['transaction_type'] === 'Cash In') {
                        $colorClass = 'text-success';
                        $iconClass = 'icon-green';
                        $sign = '+';
                        $icon = 'bi-wallet2';
                    } elseif ($t['transaction_type'] === 'Cash Out') {
                        $colorClass = 'text-danger';
                        $iconClass = 'icon-red';
                        $sign = '-';
                        $icon = 'bi-arrow-down-circle';
                    } elseif ($t['transaction_type'] === 'Send Money') {
                        $colorClass = 'text-primary';
                        $iconClass = 'icon-blue';
                        $sign = '-';
                        $icon = 'bi-send';
                    } elseif ($t['transaction_type'] === 'Received Money') {
                        $colorClass = 'text-success'; 
                        $iconClass = 'icon-green';
                        $sign = '+';
                        $icon = 'bi-currency-exchange';
                    } else {
                        $colorClass = '';
                        $iconClass = '';
                        $sign = '';
                        $icon = '';
                    }
                    ?>
                    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box <?php echo $iconClass; ?>" style="width: 40px; height: 40px;">
                                <i class="bi <?php echo $icon; ?> fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($t['desc']); ?></div>
                                <small
                                    class="text-muted"><?php echo date("M d, Y H:i", strtotime($t['created_at'])); ?></small>
                            </div>
                        </div>

                        <div class="fw-bold <?php echo $colorClass; ?>">
                            <?php echo $sign; ?>₱<?php echo number_format(abs($t['amount']), 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- SAVINGS SECTIONNN -->
        <?php
        // Display success/error messages
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['success_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['error_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <div id="savings" class="dashboard-section">
            <h4 class="fw-bold mb-1">My Savings</h4>
            <p class="text-muted mb-4">Grow your wealth with our high-yield savings options</p>

            <!-- Summary Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-5">
                    <div class="stat-card green d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-piggy-bank-fill fs-4"></i>
                            <span class="fw-bold">Total Savings</span>
                        </div>
                        <h1 class="fw-bold display-5 mb-1">₱<?php echo number_format($totalSavings, 2); ?></h1>
                        <small class="opacity-75">High-yield interest earning</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-graph-up-arrow text-success fs-3 mb-2"></i>
                        <small class="text-muted">Interest Earned</small>
                        <h2 class="fw-bold mb-1">₱<?php echo $interestEarned; ?></h2>
                        <small class="text-muted">Year to date</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-calendar-check text-primary fs-3 mb-2"></i>
                        <small class="text-muted">Next Interest Payout</small>
                        <h2 class="fw-bold mb-1"><?php echo $nextPayoutDate; ?></h2>
                        <small class="text-muted">Monthly automatic</small>
                    </div>
                </div>
            </div>

            <!-- Create New Savings Account Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">My Savings Accounts</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSavingsModal">
                    <i class="bi bi-plus-circle me-2"></i>Create New Account
                </button>
            </div>

            <!-- Savings Accounts List -->
            <?php if (empty($savingsAccounts)): ?>
                <div class="account-item text-center py-5">
                    <i class="bi bi-piggy-bank fs-1 text-muted mb-3"></i>
                    <p class="text-muted">You don't have any savings accounts yet.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSavingsModal">
                        Create Your First Savings Account
                    </button>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($savingsAccounts as $account):
                        // Calculate withdrawal restrictions
                        $canWithdraw = true;
                        $withdrawMessage = '';

                        if ($account['savings_type'] === 'Fixed') {
                            $createdDate = new DateTime($account['created_at']);
                            $today = new DateTime();
                            $monthsDiff = $createdDate->diff($today)->m + ($createdDate->diff($today)->y * 12);

                            if ($monthsDiff < 3) {
                                $canWithdraw = false;
                                $canWithdrawDate = (clone $createdDate)->modify('+3 months')->format('M j, Y');
                                $withdrawMessage = "Locked until {$canWithdrawDate} (3-month fixed term)";
                            }
                        }

                        if ($account['savings_type'] === 'Special' && $account['balance'] <= 50000) {
                            $canWithdraw = false;
                            $withdrawMessage = "Must maintain ₱50,000 minimum balance";
                        }

                        $maxWithdraw = $account['balance'];
                        if ($account['savings_type'] === 'Special') {
                            $maxWithdraw = max(0, $account['balance'] - 50000);
                        }
                        ?>
                        <div class="col-md-6">
                            <div class="account-item p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <?php echo htmlspecialchars($account['savings_type']); ?>
                                            <?php if ($account['savings_type'] === 'Fixed'): ?>
                                                <i class="bi bi-lock-fill text-warning ms-1" title="Fixed Term"></i>
                                            <?php elseif ($account['savings_type'] === 'Special'): ?>
                                                <i class="bi bi-star-fill text-warning ms-1" title="Special Account"></i>
                                            <?php endif; ?>
                                        </h6>
                                        <small
                                            class="text-muted font-monospace"><?php echo htmlspecialchars($account['savings_id']); ?></small>
                                    </div>
                                    <span
                                        class="badge bg-<?php echo $account['status'] === 'Active' ? 'success' : 'secondary'; ?>">
                                        <?php echo htmlspecialchars($account['status']); ?>
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted">Current Balance</small>
                                    <h3 class="fw-bold mb-0">₱<?php echo number_format($account['balance'], 2); ?></h3>
                                    <?php if ($account['savings_type'] === 'Special'): ?>
                                        <small class="text-muted">Min. Balance: ₱50,000.00</small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <small class="text-muted">Interest Rate</small>
                                        <div class="fw-bold text-success">
                                            <?php echo number_format($account['interest_rate'], 2); ?>% APY</div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">Created</small>
                                        <div class="fw-bold"><?php echo date("M d, Y", strtotime($account['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!$canWithdraw && $withdrawMessage): ?>
                                    <div class="alert alert-warning py-2 mb-3">
                                        <small><i
                                                class="bi bi-exclamation-triangle me-1"></i><?php echo $withdrawMessage; ?></small>
                                    </div>
                                <?php endif; ?>

                                <?php if ($account['status'] === 'Active'): ?>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success btn-sm flex-fill"
                                            onclick="openDepositModal('<?php echo $account['savings_id']; ?>', '<?php echo $account['savings_type']; ?>', <?php echo $account['balance']; ?>)">
                                            <i class="bi bi-arrow-down-circle me-1"></i>Deposit
                                        </button>
                                        <button
                                            class="btn btn-warning btn-sm flex-fill <?php echo !$canWithdraw ? 'disabled' : ''; ?>"
                                            onclick="openWithdrawModal('<?php echo $account['savings_id']; ?>', '<?php echo $account['savings_type']; ?>', <?php echo $account['balance']; ?>, <?php echo $maxWithdraw; ?>, <?php echo $canWithdraw ? 'true' : 'false'; ?>)"
                                            <?php echo !$canWithdraw ? 'disabled title="' . htmlspecialchars($withdrawMessage) . '"' : ''; ?>>
                                            <i class="bi bi-arrow-up-circle me-1"></i>Withdraw
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm"
                                            onclick="viewTransactions('<?php echo $account['savings_id']; ?>')">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- SAVINGS ACCOUNT MODAL -->
<div class="modal fade" id="createSavingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Create New Savings Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-4">Choose the type of savings account you want to open:</p>
                <form method="POST" action="php/process_savings.php" id="createSavingsForm">
                    <input type="hidden" name="action" value="create_account">
                    <!-- Hidden input for user balance - ADD THIS -->
                    <input type="hidden" id="userMainBalance" value="<?php echo $totalBalance; ?>">
        
                            <div class="row g-3 mb-4">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="savings_type" id="regular" value="Regular"
                                        required>
                                    <label class="btn btn-outline-success w-100 p-3" for="regular">
                                        <i class="bi bi-piggy-bank fs-2 d-block mb-2"></i>
                                        <div class="fw-bold">Regular</div>
                                        <small class="d-block mb-1">2.5% APY</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">Min: ₱100</small>
                                    </label>
                                </div>
        
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="savings_type" id="fixed" value="Fixed" required>
                                    <label class="btn btn-outline-success w-100 p-3" for="fixed">
                                        <i class="bi bi-lock-fill fs-2 d-block mb-2"></i>
                                        <div class="fw-bold">Fixed</div>
                                        <small class="d-block mb-1">3.5% APY</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">Min: ₱1,000</small>
                                    </label>
                                </div>
        
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="savings_type" id="special" value="Special"
                                        required>
                                    <label class="btn btn-outline-success w-100 p-3" for="special">
                                        <i class="bi bi-star-fill fs-2 d-block mb-2"></i>
                                        <div class="fw-bold">Special</div>
                                        <small class="d-block mb-1">5.0% APY</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">Min: ₱50,000</small>
                                    </label>
                                </div>
                            </div>
        
                            <div class="alert alert-info mb-3" id="savingsTypeInfo" style="display:none;">
                                <small><i class="bi bi-info-circle me-1"></i><span id="savingsTypeInfoText"></span></small>
                            </div>
        
                            <div class="mb-3">
                                <label for="initialDeposit" class="form-label fw-semibold">
                                    Initial Deposit <span id="minimumDepositLabel" class="text-muted">(Select account type
                                        first)</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" id="initialDeposit"
                                    name="initial_deposit" step="0.01" placeholder="Select account type first" disabled
                                    required>
                                <div class="form-text">Available Balance: ₱<?php echo number_format($totalBalance, 2); ?></div>
                                <div class="text-danger mt-1" id="initialDepositWarning" style="display:none;"></div>
                            </div>
        
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <small>Your account will be pending until admin approval.</small>
                            </div>
        
                            <div class="mt-4">
                                <button type="submit" class="btn btn-success w-100" id="createSavingsSubmit" disabled>Create
                                    Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- DEPOSIT MODAL -->
        <div class="modal fade" id="depositModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Deposit to Savings</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <small class="text-muted">Depositing to:</small>
                            <div class="fw-bold" id="depositAccountName"></div>
                            <small class="text-muted font-monospace" id="depositAccountId"></small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Available Balance (Main Account)</small>
                            <h5 class="fw-bold">₱<?php echo number_format($totalBalance, 2); ?></h5>
                        </div>

                        <form method="POST" action="php/process_savings.php" id="depositForm">
                            <input type="hidden" name="action" value="deposit">
                            <input type="hidden" name="savings_id" id="depositSavingsId">

                            <div class="mb-3">
                                <label for="depositAmount" class="form-label">Amount to Deposit</label>
                                <input type="number" class="form-control form-control-lg" id="depositAmount"
                                    name="amount" min="1" step="0.01" max="<?php echo $totalBalance; ?>"
                                    placeholder="0.00" required>
                                <div class="text-danger mt-1" id="depositWarning" style="display:none;"></div>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Confirm Deposit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- WITHDRAW MODAL -->
        <div class="modal fade" id="withdrawModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Withdraw from Savings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <small class="text-muted">Withdrawing from:</small>
                            <div class="fw-bold" id="withdrawAccountName"></div>
                            <small class="text-muted font-monospace" id="withdrawAccountId"></small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Available in Savings</small>
                            <h5 class="fw-bold" id="withdrawAvailable"></h5>
                        </div>

                        <div class="alert alert-info" id="withdrawRestrictionInfo" style="display:none;">
                            <small><i class="bi bi-info-circle me-1"></i><span
                                    id="withdrawRestrictionText"></span></small>
                        </div>

                        <form method="POST" action="php/process_savings.php" id="withdrawForm">
                            <input type="hidden" name="action" value="withdraw">
                            <input type="hidden" name="savings_id" id="withdrawSavingsId">
                            <input type="hidden" id="withdrawMaxAmount">
                            <input type="hidden" id="withdrawSavingsType">

                            <div class="mb-3">
                                <label for="withdrawAmount" class="form-label">Amount to Withdraw</label>
                                <input type="number" class="form-control form-control-lg" id="withdrawAmount"
                                    name="amount" min="1" step="0.01" placeholder="0.00" required>
                                <div class="text-danger mt-1" id="withdrawWarning" style="display:none;"></div>
                            </div>

                            <button type="submit" class="btn btn-warning w-100">Confirm Withdrawal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TRANSACTION HISTORY MODAL -->
        <div class="modal fade" id="transactionHistoryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Transaction History</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="transactionHistoryContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Loan section dashboard -->
<div id="loan" class="dashboard-section">
    <h4 class="fw-bold mb-1">My Loans</h4>
    <p class="text-muted mb-4">Manage your active loans and payments</p>

    <div class="row g-4 mb-5">
        <!-- Total Balance Due -->
        <div class="col-md-5">
            <div class="stat-card red d-flex flex-column justify-content-center">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-circle-fill fs-4"></i>
                    <span class="fw-bold">Total Balance Due</span>
                </div>
                <h1 class="fw-bold display-5 mb-1">
                    ₱<?= number_format($totalBalanceDue, 2); ?>
                </h1>
            </div>
        </div>

        <!-- Amount Paid (sum of all paid amounts) -->
        <div class="col-md-3">
            <div class="stat-card white d-flex flex-column justify-content-center text-center">
                <i class="bi bi-check-circle text-success fs-3 mb-2"></i>
                <small class="text-muted">Amount Paid</small>
                <h5 class="fw-bold mb-1">
                    ₱<?= isset($loans[0]) ? number_format(array_sum(array_column($loans, 'paid')), 2) : '0.00'; ?>
                </h5>
            </div>
        </div>

        <!-- Next Due Date (earliest pending due date) -->
        <div class="col-md-4">
            <div class="stat-card white d-flex flex-column justify-content-center text-center">
                <i class="bi bi-calendar-event text-danger fs-3 mb-2"></i>
                <small class="text-muted">Next Due Date</small>
                <?php
                $nextDueDates = array_filter(array_column($loans, 'next_due'));
                $nextDue = !empty($nextDueDates) ? min($nextDueDates) : 'N/A';
                echo '<h5 class="fw-bold mb-1">' . $nextDue . '</h5>';
                ?>
            </div>
        </div>
    </div>


<!-- Request loans and other functions -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0">Active Loans</h5>
        <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" data-bs-toggle="modal"
            data-bs-target="#requestLoanModal">
            <i class="bi bi-plus-lg me-1"></i> Request Loan
        </button>
    </div>

    <div class="d-flex flex-column gap-3">
        <?php
        // Define icons for each loan type
        $loanIcons = [
            'Personal' => 'bi-person-circle',
            'Home' => 'bi-house-door',
            'Auto Loan' => 'bi-car-front',
            'Business' => 'bi-briefcase'
        ];
        ?>

        <?php foreach ($loans as $loan):
            // Check if loan is fully paid
            $isFullyPaid = (floatval($loan['balance']) <= 0);
            ?>
                <div class="account-item">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box icon-red">
                                    <i class="bi <?= $loanIcons[$loan['loan_type']] ?? 'bi-wallet2'; ?>"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">
                                        <?= htmlspecialchars($loan['loan_type']); ?>
                                    </h6>
                                    <small class="text-muted">
                                        ID: <?= $loan['loan_id']; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-center">
                            <h5 class="fw-bold mb-0">
                                ₱<?= number_format($loan['balance'], 2); ?>
                            </h5>
                            <small class="fw-bold <?= ($loan['Status'] === 'Approved') ? 'text-success' : (($loan['Status'] === 'Rejected') ? 'text-danger' : 'text-warning'); ?>">
                                <?= $loan['Status']; ?>
                            </small>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <button 
                                class="btn btn-custom-outline py-2 px-3 rounded-3 me-2 view-loan-btn"
                                data-loan-id="<?= $loan['loan_id']; ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#loanDetailsModal">
                                View Details
                            </button>

                            <?php if ($isFullyPaid): ?>
                                    <span class="badge bg-success py-2 px-3 fs-6">
                                        <i class="bi bi-check-circle me-1"></i> Fully Paid
                                    </span>
                            <?php elseif ($loan['Status'] === 'Approved'): ?>
                                    <button 
                                        class="btn btn-success fw-bold py-2 px-3 rounded-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#payLoanModal"
                                        data-loan-id="<?= $loan['loan_id']; ?>">
                                        Pay Now
                                    </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- LOAN DETAILS MODAL -->
<div class="modal fade" id="loanDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Loan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Loading -->
                <div id="loanDetailsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>

                <!-- Content -->
                <div id="loanDetailsContent" class="d-none">
                    <!-- Loan Info -->
                    <h6 class="fw-bold mb-2">Loan Information</h6>
                    <table class="table table-sm">
                        <tr><th>Loan ID</th><td id="ld-loan-id"></td></tr>
                        <tr><th>Loan Type</th><td id="ld-loan-type"></td></tr>
                        <tr><th>Status</th><td id="ld-status"></td></tr>
                        <tr><th>Application Date</th><td id="ld-date"></td></tr>
                        <tr><th>Reason</th><td id="ld-reason"></td></tr>
                        <tr><th>Total Amount</th><td id="ld-total"></td></tr>
                        <tr><th>Total Paid</th><td id="ld-paid"></td></tr>
                        <tr><th>Remaining Balance</th><td id="ld-balance"></td></tr>
                    </table>

                    <hr>

                    <!-- Payment Breakdown -->
                    <h6 class="fw-bold mb-2">Payment Breakdown</h6>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="paymentBreakdownTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REQUEST LOAN MODAL -->
<div class="modal fade" id="requestLoanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Request Loan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Loan Type:</label>
                        <select name="loan_type" class="form-select" required>
                            <option value="">--Select Loan Type--</option>
                            <option value="Personal">Personal</option>
                            <option value="Home">Home</option>
                            <option value="Auto Loan">Auto Loan</option>
                            <option value="Business">Business</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount Needed:</label>
                        <input type="number" name="amount" class="form-control" placeholder="Amount Needed" step="0.01" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason:</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Reason for loan request" required></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <input type="hidden" name="request_loan" value="1">
                    <button type="submit" class="btn btn-primary w-100">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- PAY LOAN MODAL -->
<div class="modal fade" id="payLoanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Pay Loan</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="pay-loan-id">
                <input type="hidden" id="pay-payment-id">

                <!-- Payment Type Selection -->
                <div class="mb-3">
                    <label class="fw-bold mb-2">Payment Type</label>
                    
                    <!-- Current Month Option (will be shown/hidden dynamically) -->
                    <div class="form-check" id="currentMonthOption" style="display: none;">
                        <input type="radio" class="form-check-input" name="paymentType" id="payMonthly" value="monthly">
                        <label class="form-check-label" for="payMonthly">
                            <strong>Pay Current Month</strong>
                        </label>
                    </div>
                    
                    <!-- Early Payment Option (will be shown/hidden dynamically) -->
                    <div class="form-check" id="earlyPaymentOption" style="display: none;">
                        <input type="radio" class="form-check-input" name="paymentType" id="payEarly" value="early">
                        <label class="form-check-label" for="payEarly">
                            <strong>Pay Next Month (Early Payment)</strong>
                        </label>
                    </div>
                    
                    <!-- Full Payment Option (always available) -->
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="paymentType" id="payFull" value="full">
                        <label class="form-check-label" for="payFull">
                            <strong>Pay Full Balance</strong>
                        </label>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="mb-3">
                    <label class="fw-bold mb-1">Payment Amount</label>
                    <input 
                        type="number"
                        id="paymentAmount"
                        class="form-control"
                        min="0"
                        step="0.01"
                        readonly
                    >
                </div>

                <!-- Payment Info -->
                <div class="alert alert-info py-2" id="paymentInfo"></div>

                <!-- User Balance Display -->
                <div class="alert alert-warning py-2" id="userBalanceInfo"></div>
            </div>

            <div class="modal-footer">
                <button id="confirmPaymentBtn" class="btn btn-success w-100 fw-bold">
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Payment Successful!</h5>
                <p class="mb-0" id="successMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-success px-4" onclick="location.reload()">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="paymentErrorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Payment Failed</h5>
                <p class="mb-0" id="errorMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="paymentConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-question-circle-fill text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Confirm Payment</h5>
                <p class="mb-0" id="confirmMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-success px-4" id="confirmPaymentAction">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Info/Warning Modal -->
<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-info-circle-fill text-info" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2" id="infoModalTitle">Notice</h5>
                <p class="mb-0" id="infoMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal" id="infoModalCloseBtn">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- 
// ==========================================
// ACCOUNT VIEW BUTTON COMMAND
// ========================================== 
-->

    <div class="modal fade" id="accountsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Active Accounts & Loans</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-muted mb-3">Accounts</h6>
                    <div class="list-group mb-4">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Checking Account</div>
                                <small class="text-muted">**** 4582</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">$3,000.00</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Savings Account</div>
                                <small class="text-muted">**** 9921</small>
                            </div>
                            <span class="badge bg-success rounded-pill">$1,200.00</span>
                        </div>
                    </div>
                    <h6 class="text-muted mb-3">Active Loans</h6>
                    <div class="alert alert-light border text-center text-muted">No active loans found.</div>
                </div>
            </div>
        </div>
    </div>


    <!-- 
// ==========================================
// EDIT USERNAME & PASSWORD
// ========================================== 
-->

    <div class="modal fade" id="usernameModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Change Username</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">New Username</label>
                            <input type="text" class="form-control" placeholder="Enter new username">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Username</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- 
// ==========================================
// FAQ DETAILS
// ========================================== 
-->

    <div class="modal fade" id="faqModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Frequently Asked Questions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    How do I transfer money to another bank?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Go to the <strong>Home</strong> tab, click the purple <strong>Send
                                        Money</strong>
                                    button.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    What is the maintaining balance?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">The maintaining balance is <strong>$500.00</strong>.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    How can I apply for a loan?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Navigate to the <strong>Loan</strong> tab and click
                                    "Request
                                    Loan".</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Need more help? Call us at 1-800-BANKO-HELP</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($cashoutSuccessMessage): ?>
                    const successModal = new bootstrap.Modal(document.getElementById('cashOutSuccessModal'));
                    successModal.show();
                <?php endif; ?>

                <?php if ($cashoutErrorMessage): ?>
                    const errorModal = new bootstrap.Modal(document.getElementById('cashOutErrorModal'));
                    errorModal.show();
                <?php endif; ?>
            });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/script.js"></script>
    <script src="script/loan_payment.js"></script>

</body>

</html>
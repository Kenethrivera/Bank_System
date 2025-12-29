<?php
// ==========================================
// SECTION 1: DATA SIMULATION (BACKEND LOGIC)
// ==========================================
// In a real application, this data would come from a database (MySQL).
// We use hardcoded variables here for frontend demonstration purposes.

// --- User Profile Data ---
$userName = "Kenneth";
$userEmail = "kenneth@example.com";
$userPhone = "+63 917 123 4567";
$mainAccountNumber = "123-456-7890";
$totalBalance = 3000.00; // Main Wallet Balance

// --- Savings Accounts Data ---
// Array of savings accounts to be displayed in the "Savings" tab
$savingsAccounts = [
    [
        'name' => 'High Yield Savings',
        'acct' => '32163821',
        'balance' => 1200.00,
        'status' => 'Active',
        'interest' => '2.5%'
    ],
    [
        'name' => 'Vacation Fund',
        'acct' => '55442211',
        'balance' => 500.00,
        'status' => 'Active',
        'interest' => '1.5%'
    ]
];
$totalSavings = 1700.00; // Total combined savings
$interestEarned = 134.00;
$nextPayoutDate = "Oct 23";

// --- Loan Data ---
// Array of active loans for the "Loan" tab
$loans = [
    [
        'name' => 'Home Renovation',
        'id' => 'LN-8821',
        'amount' => 5000.00,
        'paid' => 2500.00,
        'balance' => 2500.00,
        'status' => 'Active',
        'next_due' => 'Oct 30'
    ]
];

// --- Transaction History ---
// Used in the "Home" tab to show recent activity
$transactions = [
    ['date' => 'Oct 24', 'desc' => 'Payroll Deposit', 'amount' => 1500.00, 'type' => 'credit', 'icon' => 'bi-briefcase'],
    ['date' => 'Oct 23', 'desc' => 'Starbucks Coffee', 'amount' => -5.50, 'type' => 'debit', 'icon' => 'bi-cup-hot'],
];
?>

<!doctype html>
<head>

    <title>Banko User Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Global Reset & Typography --- */
        body { 
            background-color: #fcf8f5; /* Light beige background */
            font-family: 'Inter', sans-serif; 
            color: #1f2937; /* Dark gray text */
        }
        
        /* --- Navigation Bar --- */
        .navbar { 
            background: white; 
            border-bottom: 1px solid #f3f4f6; 
            padding: 15px 0; 
        }
        .nav-link { 
            color: #6b7280; 
            font-weight: 600; 
            margin: 0 15px; 
            cursor: pointer; 
            position: relative; 
            transition: color 0.2s; 
        }
        /* Active Link Styling (Blue underline) */
        .nav-link.active { color: #2563eb; }
        .nav-link.active::after { 
            content: ''; 
            position: absolute; 
            bottom: -22px; 
            left: 0; 
            width: 100%; 
            height: 3px; 
            background-color: #2563eb; 
            border-radius: 2px 2px 0 0; 
        }
        
        /* --- Dashboard Cards (Stats) --- */
        .stat-card { 
            border-radius: 16px; 
            padding: 30px; 
            position: relative; 
            height: 100%; 
            border: 1px solid #f3f4f6; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
        }
        /* Color Variants */
        .stat-card.green { background-color: #22c55e; color: white; border: none; }
        .stat-card.blue { background-color: #1e3a8a; color: white; border: none; }
        .stat-card.red { background-color: #ef4444; color: white; border: none; }
        .stat-card.white { background-color: white; color: #1f2937; }
        
        /* --- List Items (Savings/Loan Rows) --- */
        .account-item { 
            background: white; 
            border-radius: 12px; 
            padding: 20px; 
            border: 1px solid #f3f4f6; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
            transition: transform 0.2s; 
        }
        .account-item:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); 
        }
        
        /* --- Icon Boxes (Square icons inside lists) --- */
        .icon-box { 
            width: 48px; 
            height: 48px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 20px; 
        }
        .icon-green { background-color: #dcfce7; color: #166534; }
        .icon-blue { background-color: #dbeafe; color: #1e40af; }
        .icon-red { background-color: #fee2e2; color: #991b1b; }

        /* --- Action Buttons (Cash In/Out, Send) --- */
        .btn-action { 
            border-radius: 12px; 
            padding: 25px 20px; 
            text-align: center; 
            border: none; 
            transition: all 0.2s; 
            font-weight: 600; 
            height: 100%; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center; 
        }
        .btn-action:hover { transform: translateY(-3px); }
        .btn-green-light { background-color: #dcfce7; color: #166534; }
        .btn-red-light   { background-color: #fee2e2; color: #991b1b; }
        .btn-purple-light{ background-color: #ede9fe; color: #5b21b6; }
        
        .btn-custom-outline { 
            border: 1px solid #e5e7eb; 
            color: #374151; 
            font-weight: 600; 
            background: white; 
        }
        .btn-custom-outline:hover { 
            background: #f9fafb; 
            border-color: #d1d5db; 
        }
        
        /* --- Utilities --- */
        .dashboard-section { 
            display: none; /* Hidden by default */
            animation: fadeIn 0.3s ease-in-out; 
        }
        .dashboard-section.active { 
            display: block; /* Visible when active */
        }
        .cursor-pointer { cursor: pointer; }
        
        /* Fade In Animation */
        @keyframes fadeIn { 
            from { opacity: 0; transform: translateY(10px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
    </style>
</head>
<body>

<!-- 
// ==========================================
// HEADER NAVIGATION BAR
// ========================================== 
-->
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

            <!-- 
// ==========================================
// FAQ & USER DROPDOWN MENU
// ========================================== 
-->

            <div class="d-flex align-items-center">
                <i class="bi bi-question-circle me-3 fs-5 text-secondary cursor-pointer" 
                   data-bs-toggle="modal" data-bs-target="#faqModal" title="Help"></i>
                
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer" 
                     style="width: 40px; height: 40px;" data-bs-toggle="dropdown">
                    <i class="bi bi-person-fill"></i>
                </div>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2">
                    <li><h6 class="dropdown-header">Hello, <?php echo $userName; ?></h6></li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal" data-bs-target="#accountsModal">
                            <i class="bi bi-wallet2 me-2"></i>Active Accounts & Loans
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal" data-bs-target="#usernameModal">
                            <i class="bi bi-person-gear me-2"></i>Change Username
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2" href="#" data-bs-toggle="modal" data-bs-target="#passwordModal">
                            <i class="bi bi-key me-2"></i>Change Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger rounded-2" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<!-- 
// ==========================================
// MAIN DASHBOARD CONTENT
// ========================================== 
-->

    <div class="container py-5">

        <div id="home" class="dashboard-section active">
            <h4 class="fw-bold mb-1">Hello, <?php echo $userName; ?>!</h4>
            <p class="text-muted mb-4">Here is your daily financial overview.</p>

            <div class="stat-card blue mb-4">
                <div class="d-flex justify-content-between">
                    <small class="opacity-75">Main Wallet Balance</small>
                    <small class="opacity-75 font-monospace">Acct: <?php echo $mainAccountNumber; ?></small>
                </div>
                <h1 class="fw-bold display-4 my-3" id="displayBalance">$<?php echo number_format($totalBalance, 2); ?></h1>
                <i class="bi bi-eye position-absolute top-0 end-0 m-4 fs-4 opacity-50 cursor-pointer" 
                   id="eyeIcon" onclick="toggleBalance()"></i>
            </div>

            <!-- 
// ==========================================
// TRANSACTION ACTION BUTTONS
// ========================================== 
-->

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="btn-action btn-green-light">
                        <i class="bi bi-arrow-down fs-2 mb-2"></i><span>Cash In</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="btn-action btn-red-light">
                        <i class="bi bi-arrow-up fs-2 mb-2"></i><span>Cash Out</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="btn-action btn-purple-light">
                        <i class="bi bi-send fs-2 mb-2"></i><span>Send Money</span>
                    </div>
                </div>
            </div>


            <!-- 
// ==========================================
// TRANSACTION HISTORY
// ========================================== 
-->

            <h5 class="fw-bold mb-3">Recent Transactions</h5>
            <div class="account-item p-0 overflow-hidden">
                <?php foreach ($transactions as $t): ?>
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box <?php echo $t['type'] == 'credit' ? 'icon-green' : 'icon-red'; ?>" 
                             style="width: 40px; height: 40px;">
                            <i class="bi <?php echo $t['icon']; ?>"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo $t['desc']; ?></div>
                            <small class="text-muted"><?php echo $t['date']; ?></small>
                        </div>
                    </div>
                    <div class="fw-bold <?php echo $t['type'] == 'credit' ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $t['type'] == 'credit' ? '+' : ''; ?>$<?php echo number_format(abs($t['amount']), 2); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- 
// ==========================================
// SAVINGS SECTION DASHBOARD
// ========================================== 
-->

        <div id="savings" class="dashboard-section">
            <h4 class="fw-bold mb-1">My Savings</h4>
            <p class="text-muted mb-4">Grow your wealth with our high-yield savings options</p>

            <div class="row g-4 mb-5">
                <div class="col-md-5">
                    <div class="stat-card green d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-piggy-bank-fill fs-4"></i>
                            <span class="fw-bold">Total Savings</span>
                        </div>
                        <h1 class="fw-bold display-5 mb-1">$<?php echo number_format($totalSavings, 2); ?></h1>
                        <small class="opacity-75">+2.5% APY Interest Rate</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-graph-up-arrow text-success fs-3 mb-2"></i>
                        <small class="text-muted">Interest Earned</small>
                        <h2 class="fw-bold mb-1">$<?php echo $interestEarned; ?></h2>
                        <small class="text-muted">Year to date</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-calendar-check text-primary fs-3 mb-2"></i>
                        <small class="text-muted">Next Payout</small>
                        <h2 class="fw-bold mb-1"><?php echo $nextPayoutDate; ?></h2>
                        <small class="text-muted">Estimated $12.40</small>
                    </div>
                </div>
            </div>


            <!-- 
// ==========================================
// OPEN SAVINGS ACCOUNT
// ========================================== 
-->

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Your savings accounts</h5>
                <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" 
                        data-bs-toggle="modal" data-bs-target="#openSavingsModal">
                    <i class="bi bi-plus-lg me-1"></i> Open New
                </button>
            </div>

            <div class="d-flex flex-column gap-3">
                <?php foreach($savingsAccounts as $acct): ?>
                <div class="account-item">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box icon-green">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0"><?php echo $acct['name']; ?></h6>
                                    <small class="text-muted">Account: <?php echo $acct['acct']; ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-center">
                            <h5 class="fw-bold mb-0">$<?php echo number_format($acct['balance'], 2); ?></h5>
                            <small class="text-success fw-bold"><?php echo $acct['status']; ?></small>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <button class="btn btn-custom-outline py-2 px-3 rounded-3 me-2" 
                                    data-bs-toggle="modal" data-bs-target="#savingsDetailsModal">
                                View Details
                            </button>
                            <button class="btn btn-primary fw-bold py-2 px-3 rounded-3" 
                                    style="background-color: #0d47a1;" 
                                    data-bs-toggle="modal" data-bs-target="#openSavingsModal">
                                Add Funds
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- 
// ==========================================
// LOAN SECTION DASHBOARD
// ========================================== 
-->

        <div id="loan" class="dashboard-section">
            <h4 class="fw-bold mb-1">My Loans</h4>
            <p class="text-muted mb-4">Manage your active loans and payments</p>

            <div class="row g-4 mb-5">
                <div class="col-md-5">
                    <div class="stat-card red d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-exclamation-circle-fill fs-4"></i>
                            <span class="fw-bold">Total Balance Due</span>
                        </div>
                        <h1 class="fw-bold display-5 mb-1">$<?php echo number_format($loans[0]['balance'], 2); ?></h1>
                        <small class="opacity-75">Next payment due in 5 days</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-check-circle text-success fs-3 mb-2"></i>
                        <small class="text-muted">Amount Paid</small>
                        <h2 class="fw-bold mb-1">$<?php echo number_format($loans[0]['paid'], 2); ?></h2>
                        <small class="text-muted">50% Completed</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card white d-flex flex-column justify-content-center text-center">
                        <i class="bi bi-calendar-event text-danger fs-3 mb-2"></i>
                        <small class="text-muted">Next Due Date</small>
                        <h2 class="fw-bold mb-1"><?php echo $loans[0]['next_due']; ?></h2>
                        <small class="text-muted">Min Payment: $200.00</small>
                    </div>
                </div>
            </div>


            <!-- 
// ==========================================
// ACTIVE LOANS LIST AND REQUEST LOAN BUTTON
// ========================================== 
-->

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0">Active Loans</h5>
                <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3" 
                        data-bs-toggle="modal" data-bs-target="#requestLoanModal">
                    <i class="bi bi-plus-lg me-1"></i> Request Loan
                </button>
            </div>

            <div class="d-flex flex-column gap-3">
                <?php foreach($loans as $loan): ?>
                <div class="account-item">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box icon-red">
                                    <i class="bi bi-house-door"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0"><?php echo $loan['name']; ?></h6>
                                    <small class="text-muted">ID: <?php echo $loan['id']; ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-md-center">
                            <h5 class="fw-bold mb-0">$<?php echo number_format($loan['balance'], 2); ?></h5>
                            <small class="text-danger fw-bold"><?php echo $loan['status']; ?></small>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <button class="btn btn-custom-outline py-2 px-3 rounded-3 me-2">
                                View Details
                            </button>
                            <button class="btn btn-success fw-bold py-2 px-3 rounded-3" 
                                    data-bs-toggle="modal" data-bs-target="#payLoanModal">
                                Pay Now
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>


    <!-- 
// ==========================================
// NEW SAVINGS ACCOUNT COMMAND
// ========================================== 
-->

    <div class="modal fade" id="openSavingsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Savings Account</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" placeholder="Account Name (e.g. Vacation)">
                    <input class="form-control" type="number" placeholder="Initial Deposit">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Create</button>
                </div>
            </div>
        </div>
    </div>


    <!-- 
// ==========================================
// NEW LOAN REQUEST COMMAND
// ========================================== 
-->

    <div class="modal fade" id="requestLoanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Loan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" placeholder="Amount Needed">
                    <textarea class="form-control" rows="3" placeholder="Reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Submit Application</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 
// ==========================================
//PAYLOAN COMMAND
// ========================================== 
-->
    
    <div class="modal fade" id="payLoanModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pay Loan</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" placeholder="Payment Amount">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="payFull">
                        <label class="form-check-label" for="payFull">Pay Full Balance</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>


    <!-- 
// ==========================================
// SAVINGS VIEW BUTTON COMMAND
// ========================================== 
-->

    <div class="modal fade" id="savingsDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Manage Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Select an action for <strong>High Yield Savings</strong></p>
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary fw-bold py-3 text-start px-4">
                            <i class="bi bi-arrow-return-left me-2"></i> Withdraw to Main Wallet
                        </button>
                        <button class="btn btn-outline-dark fw-bold py-3 text-start px-4">
                            <i class="bi bi-arrow-left-right me-2"></i> Transfer to other Savings
                        </button>
                        <hr>
                        <button class="btn btn-outline-danger fw-bold py-3 text-start px-4">
                            <i class="bi bi-x-circle me-2"></i> Close Account
                        </button>
                    </div>
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
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How do I transfer money to another bank?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Go to the <strong>Home</strong> tab, click the purple <strong>Send Money</strong> button.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    What is the maintaining balance?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">The maintaining balance is <strong>$500.00</strong>.</div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    How can I apply for a loan?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Navigate to the <strong>Loan</strong> tab and click "Request Loan".</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- Tab Switching Logic ---
        // Hides all sections and shows only the clicked one (SPA feel)
        function showSection(sectionId, linkElement) {
            // Hide all sections
            document.querySelectorAll('.dashboard-section').forEach(sec => sec.classList.remove('active'));
            // Remove 'active' class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            
            // Show target section and highlight nav link
            document.getElementById(sectionId).classList.add('active');
            linkElement.classList.add('active');
        }

        // --- Balance Toggle Logic ---
        // Toggles between showing the actual amount and asterisks ($****)
        let isHidden = false;
        const balanceElement = document.getElementById('displayBalance');
        const originalBalance = balanceElement.innerText; // Store original PHP value

        function toggleBalance() {
            const eyeIcon = document.getElementById('eyeIcon');
            if (isHidden) {
                // Show Numbers
                balanceElement.innerText = originalBalance;
                eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
                isHidden = false;
            } else {
                // Hide with Asterisks
                balanceElement.innerText = "$****";
                eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
                isHidden = true;
            }
        }
    </script>
</body>
</html>

<?php
session_start();

// Redirect if admin is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Prevent caching so browser back button doesn't show logged-in content
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>


<?php require_once 'php/Dashboard.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <title>Bank Dashboard</title>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <nav id="sidebar" class="col-auto">
        <div class="sidebar-logo">
          <span class="material-symbols-outlined fs-2">account_balance</span>
          <div class="logo-text">
            <h5 class="mb-0">KennethBank</h5>
            <small>Management&nbsp;System</small>
          </div>
        </div>
        <hr class="bg-light">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a href="#dashboard" class="nav-link">
              <span class="material-symbols-outlined">dashboard</span>
              <span class="link-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#accounts" class="nav-link">
              <span class="material-symbols-outlined">account_balance_wallet</span>
              <span class="link-text">Accounts</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#transactions" class="nav-link">
              <span class="material-icons">swap_horiz</span>
              <span class="link-text">Transactions</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#savings" class="nav-link">
              <span class="material-symbols-outlined">savings</span>
              <span class="link-text">Savings</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#loan" class="nav-link">
              <span class="material-icons">payments</span>
              <span class="link-text">Loan</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#faq" class="nav-link">
              <span class="material-icons">quiz</span>
              <span class="link-text">FAQs</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" id="adminLogoutLink" class="nav-link">
              <span class="material-symbols-outlined">logout</span>
              <span class="link-text">Logout</span>
            </a>
          </li>
        </ul>
      </nav>

      <main class="col py-4">
        <section id="dashboard">
          <div class="container py-4">
            <div class="mb-4">
              <h1 class="fw-bold">Dashboard</h1>
              <p class="text-muted">
                Welcome back! Here's what's happening with your bank today.
              </p>
            </div>
            <div class="row g-4 mb-4">
              <div class="col-md-6 col-lg-3">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-primary text-white rounded p-2">
                      <i class="bi bi-people fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Total Customers</small>
                  <h3 class="fw-bold"><?php echo $totalCustomers; ?></h3>
                </div>
              </div>

              <div class="col-md-6 col-lg-3">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-primary text-white rounded p-2 bg-purple-custom">
                      <i class="bi-person-lock fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Pending Accounts</small>
                  <h3 class="fw-bold"><?php echo $pendingAcc; ?></h3>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-purple text-white rounded p-2 bg-purple-custom">
                      <i class="bi bi-arrow-left-right fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Transactions Today</small>
                  <h3 class="fw-bold">32</h3>
                </div>
              </div>
              <div class="col-md-6 col-lg-3">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-warning text-white rounded p-2 bg-red-custom">
                      <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Pending Loans</small>
                  <h3 class="fw-bold"><?php echo $pendingLoan; ?></h3>
                </div>
              </div>
            </div>
            <div class="row g-4">
              <div class="col-lg-6">
                <div class="card p-4">
                  <h5 class="fw-semibold mb-3">Account Balances</h5>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Balance</span>
                    <span class="fs-4 fw-bold">$45,230.00</span>
                  </div>
                  <div class="progress mb-4" style="height:8px;">
                    <div class="progress-bar bg-success" style="width:75%"></div>
                  </div>

                  <div class="row border-top pt-3">
                    <div class="col">
                      <small class="text-muted">Savings Accounts</small>
                      <h5 class="fw-semibold">42</h5>
                    </div>
                    <div class="col">
                      <small class="text-muted">Checking Accounts</small>
                      <h5 class="fw-semibold">43</h5>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card p-4">
                  <h5 class="fw-semibold mb-3">Recent Transactions</h5>
                  <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-success bg-opacity-25 rounded p-2">
                        <i class="bi bi-arrow-down-left text-success"></i>
                      </div>
                      <div>
                        <div class="fw-medium">Deposit</div>
                        <small class="text-muted">ACC-10234</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <div class="fw-semibold text-success">+$1,500</div>
                      <small class="text-muted">09/16/2025</small>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-danger bg-opacity-25 rounded p-2">
                        <i class="bi bi-arrow-up-right text-danger"></i>
                      </div>
                      <div>
                        <div class="fw-medium">Withdrawal</div>
                        <small class="text-muted">ACC-55421</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <div class="fw-semibold text-danger">-$500</div>
                      <small class="text-muted">09/15/2025</small>
                    </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center py-2">
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-primary bg-opacity-25 rounded p-2">
                        <i class="bi bi-arrow-left-right text-primary"></i>
                      </div>
                      <div>
                        <div class="fw-medium">Transfer</div>
                        <small class="text-muted">ACC-99887</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <div class="fw-semibold text-primary">+$750</div>
                      <small class="text-muted">09/14/2025</small>
                    </div>
                  </div>

                </div>
              </div>

            </div>

          </div>
        </section>
        <section id="accounts">
          <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h2 class="fw-bold mb-0">Account Management</h2>
                <small class="text-muted">Manage savings and checking accounts</small>
              </div>
            </div>
            <div class="card p-3 mb-4">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control" id="searchInput"
                  placeholder="Search by account number or customer name...">
              </div>
            </div>
            <div class="card p-3">
              <div class="table-responsidve">
                <div id="notFound" class="text-center text-muted mt-3" style="display: none;">
                  No accounts found matching your search.
                </div>
                <table class="table table-hover align-middle" id="searchableTable">
                  <thead class="table-light">
                    <tr>
                      <th>Account Number</th>
                      <th>Customer</th>
                      <th>Email</th>
                      <th>Balance</th>
                      <th>Status</th>
                      <th class="text-center">Actions</th>

                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (mysqli_num_rows($accounts_result) > 0) {
                      while ($row = mysqli_fetch_assoc($accounts_result)) {

                        // Build full name
                        $fullName = $row['FirstName'] . " " . ($row['MiddleName'] ? $row['MiddleName'] . " " : "") . $row['LastName'];

                        $accountNumber = "ACC-" . str_pad($row['ID'], 5, '0', STR_PAD_LEFT);

                        if ($row['Status'] === 'Approved') {
                          $statusClass = 'status-pill status-approved';
                        } elseif ($row['Status'] === 'Pending') {
                          $statusClass = 'status-pill status-pending';
                        } elseif ($row['Status'] === 'Rejected') {
                          $statusClass = 'status-pill status-rejected';
                        }
                        $fullNameEsc = htmlspecialchars($fullName, ENT_QUOTES);
                        $emailEsc = htmlspecialchars($row['Email'], ENT_QUOTES);
                        $phoneEsc = htmlspecialchars($row['Phone'], ENT_QUOTES);
                        $dobEsc = htmlspecialchars($row['Birthdate'], ENT_QUOTES);
                        $addressEsc = htmlspecialchars($row['Address'], ENT_QUOTES);
                        $statusEsc = htmlspecialchars($row['Status'], ENT_QUOTES);
                        $ImgEsc = htmlspecialchars($row['Img'], ENT_QUOTES);
                        if ($row['Status'] === 'Approved') {
                          $balanceDisplay = '₱ ' . number_format($row['Balance'], 2);
                          $balanceClass = 'fw-semibold text-success';
                        } else {
                          $balanceDisplay = '— — — — —';
                          $balanceClass = 'text-muted';
                        }

                        echo "<tr data-account='{$accountNumber}' data-name='{$fullNameEsc}' >
                                  <td>{$accountNumber}</td>
                                  <td>{$fullNameEsc}</td>
                                  <td>{$emailEsc}</td>
                                  <td class='{$balanceClass}'>{$balanceDisplay}</td>
                                  <td><span class='{$statusClass}'>{$statusEsc}</span></td>
                                  <td class='text-center'>
                                      <button type='button' class='btn btn-sm btn-outline-info me-1 view-profile-btn' 
                                              data-bs-toggle='modal' 
                                              data-bs-target='#profileModal'
                                              data-id='{$row['ID']}'
                                              data-name='{$fullNameEsc}'
                                              data-email='{$emailEsc}'
                                              data-phone='{$phoneEsc}'
                                              data-dob='{$dobEsc}'
                                              data-address='{$addressEsc}'
                                              data-status='{$statusEsc}'
                                              data-img='{$ImgEsc}'>
                                              
                                              <i class='bi bi-eye'></i>
                                      </button>
                                       <form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this account? This action cannot be undone.');\" style='display:inline;'>
                                        <input type='hidden' name='delete_account_id' value='{$row['ID']}'>
                                        <button type='submit' class='btn btn-sm btn-outline-danger'>
                                          <i class='bi bi-trash'></i>
                                        </button>
                                      </form>
                                  </td>
                                </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='6'>No Customers Found</td></tr>";
                    }
                    ?>
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </section>
        <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 overflow-hidden shadow-lg">

              <div class="position-relative">
                <div class="bg-primary bg-gradient" style="height: 130px;"></div>

                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                  aria-label="Close"></button>
              </div>

              <div class="modal-body px-4 pb-4">

                <div class="d-flex justify-content-center justify-content-md-start">
                  <div class="position-relative" style="margin-top:-70px;">
                    <img alt="Profile" class="rounded-circle border border-4 border-white shadow modalImg" width="130"
                      height="130">

                    <span
                      class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-2 border-white"
                      style="width:16px;height:16px;"></span>
                  </div>
                </div>
                <div class="mt-3 text-center text-md-start">
                  <h4 id="modalName" class="fw-bold mb-0">John Doe</h4>
                </div>
                <div class="mt-4">

                  <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-envelope text-muted fs-5"></i>
                    <div>
                      <div class="text-uppercase small text-muted fw-semibold">Email Address</div>
                      <div class="fw-medium" id="modalEmail">fsafds</div>
                    </div>
                  </div>

                  <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-telephone text-muted fs-5"></i>
                    <div>
                      <div class="text-uppercase small text-muted fw-semibold">Phone Number</div>
                      <div class="fw-medium" id="modalPhone"></div>
                    </div>
                  </div>

                  <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-calendar-event text-muted fs-5"></i>
                    <div>
                      <div class="text-uppercase small text-muted fw-semibold">Date of Birth</div>
                      <div class="fw-medium" id="modalDOB"></div>
                    </div>
                  </div>

                  <div class="d-flex gap-3 mb-3">
                    <i class="bi bi-geo-alt text-muted fs-5"></i>
                    <div>
                      <div class="text-uppercase small text-muted fw-semibold">Address</div>
                      <div class="fw-medium" id="modalAddress"></div>
                    </div>
                  </div>

                </div>
                <form method="POST">
                  <input type="hidden" name="account_id" id="modalAccountId">

                  <div class="d-flex flex-column flex-md-row gap-3 pt-4 border-top mt-4">
                    <button type="submit" name="update_status" value="Rejected" class="btn btn-outline-danger w-100">
                      <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                    <button type="submit" name="update_status" value="Approved" class="btn btn-success w-100">
                      <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <section id="transactions">
          <div class="container-fluid">

            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h2 class="fw-bold mb-0">Transaction Management</h2>
                <small class="text-muted">View and manage all transactions</small>
              </div>
            </div>
            <div class="card p-3 mb-4">
              <div class="input-group">
                <span class="input-group-text bg-white">
                  <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control" placeholder="Search by account number or type.">
              </div>
            </div>

            <div class="card p-3">
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Date</th>
                      <th>Account</th>
                      <th>Type</th>
                      <th>Amount</th>
                      <th>Recipient</th>
                      <th>Description</th>
                      <th>Reference</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>2025-12-16</td>
                      <td>ACC-10001</td>
                      <td><span class="badge bg-success">Deposit</span></td>
                      <td class="text-success">+$1,500</td>
                      <td>ACC-10006</td>
                      <td>Salary deposit</td>
                      <td>1321639216</td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </section>

        <!--SAVINGS SECTION  -->
        <section id="savings" class="flex-grow-2 p-2">
          <div class="container-fluid">

            <!-- Header -->
            <div class="row mb-4 g-3">
              <div class="col-12 col-md-6 col-lg-8">
                <h3>Savings Management</h3>
                <small class="text-muted">
                  Manage customer savings portfolios and rates.
                </small>
              </div>
              <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#savingsModal">
                  + New Savings Accounts
                </button>
              </div>
            </div> <!-- Header closing -->
            <div class="row g-3">
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-semibold text-secondary">Total Assets Managed</small>
                  </div>
                  <h3 class="fw-bold">₱<?= number_format($totalAssets, 2) ?></h3>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-semibold text-secondary">Active Savings</small>
                  </div>
                  <h3 class="fw-bold"><?= $activeSavings ?></h3>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="fw-semibold text-secondary">Average Interest Rate</small>
                  </div>
                  <h3 class="fw-bold"><?= $avgInterest ?>%</h3>
                </div>
              </div>
            </div>


            <!-- search and filters -->
            <div class="card mb-3 mt-3">
              <div class="card-body">

                <form method="GET">
                  <div class="row g-2 align-items-center">

                    <div class="col-lg-5 col-md-6">
                      <div class="input-group">
                        <span class="input-group-text">
                          <span class="material-symbols-outlined">search</span>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Search by customer name"
                          value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                      </div>
                    </div>
                </form>

                <div class="col-lg-7 col-md-6">
                  <div class="filter-buttons d-flex justify-content-end align-items-center gap-3 flex-wrap">
                    <i class="bi bi-funnel fs-5"></i>
                    <button class="btn btn-dark btn-sm rounded-pill fw-bold">All</button>
                    <button class="btn btn-light btn-sm rounded-pill fw-bold">Active</button>
                    <button class="btn btn-light btn-sm rounded-pill fw-bold">Pending</button>
                    <button class="btn btn-light btn-sm rounded-pill fw-bold">Frozen</button>
                    <button class="btn btn-light btn-sm rounded-pill fw-bold">Closed</button>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="row g-4 mt-2">
            <?php if (!empty($savings_result)): ?>
              <?php foreach ($savings_result as $row): ?>
                <?php
                $status = $row['status'] ?? 'Pending';
                $account_type = $row['savings_type'] ?? 'N/A';
                $customer_id = $row['customer_id'] ?? 0;
                $savings_id = $row['savings_id'] ?? '';
                $interest_rate = floatval($row['interest_rate'] ?? 0);
                $total_balance = floatval($row['balance'] ?? 0);

                $bar_class = $status === 'Active' ? 'bg-success'
                  : ($status === 'Frozen' ? 'bg-danger'
                    : ($status === 'Pending' ? 'bg-warning'
                      : 'bg-secondary'));

                $badge_class = $status === 'Active' ? 'bg-success-subtle text-success px-3'
                  : ($status === 'Frozen' ? 'bg-danger-subtle text-danger px-3'
                    : ($status === 'Pending' ? 'bg-warning-subtle text-warning px-3'
                      : 'bg-secondary-subtle text-secondary px-3'));

                ?>

                <div class="col-12 col-md-6 col-lg-4 savings-tile" data-status="<?= $status ?>">
                  <div class="card h-100 shadow-sm rounded-4 overflow-hidden hover-lift card-hover-effect">

                    <div class="<?= $bar_class ?>" style="height:6px;"></div>

                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-3">
                          <?php
                          $full_name = $row['full_name'] ?? 'Customer #' . $customer_id;
                          $parts = explode(' ', $full_name);
                          $acronym = strtoupper(($parts[0][0] ?? '') . ($parts[1][0] ?? ($parts[0][1] ?? '')));
                          ?>
                          <div
                            class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                            style="width:42px; height:42px;">
                            <?= $acronym ?>
                          </div>
                          <div>
                            <div class="fw-bold">
                              <?= !empty($row['full_name']) ? $row['full_name'] : 'Customer #' . $customer_id ?>
                            </div>
                            <small class="text-muted"><?= $savings_id ?></small>
                          </div>
                        </div>

                        <span class="badge rounded-pill <?= $badge_class ?>">
                          ● <?= $status ?>
                        </span>
                      </div>

                      <div class="mb-3">
                        <small class="text-muted">Total Balance</small>
                        <div class="d-flex align-items-center gap-2">
                          <h3 class="fw-bold mb-0">₱<?= number_format($total_balance, 2) ?></h3>
                          <small class="text-success fw-semibold" style="font-size:0.85rem;">
                            <i class="bi bi-arrow-up-right"></i> <?= number_format($interest_rate * 100, 2) ?>%
                          </small>
                        </div>
                      </div>

                      <hr>

                      <div class="d-flex justify-content-between mt-3">
                        <div>
                          <small class="text-muted">Interest Rate</small><br>
                          <span
                            class="badge bg-primary-subtle text-primary fw-semibold"><?= number_format($interest_rate * 100, 2) ?>%
                            APY</span>
                        </div>
                        <div class="text-end">
                          <small class="text-muted">Savings Type</small><br>
                          <span class="fw-semibold"><?= $account_type ?></span>
                        </div>
                      </div>
                      <!-- DEATAILS BUTTON -->
                      <div class="d-flex align-items-center gap-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary w-100 rounded-3" data-bs-toggle="modal"
                          data-bs-target="#detailsModal" data-savingsid="<?= htmlspecialchars($savings_id) ?>"
                          data-customername="<?= htmlspecialchars($row['full_name'] ?? 'Customer') ?>"
                          data-email="<?= htmlspecialchars($row['Email'] ?? '') ?>"
                          data-phone="<?= htmlspecialchars($row['Phone'] ?? '') ?>"
                          data-address="<?= htmlspecialchars($row['Address'] ?? '') ?>"
                          data-birthdate="<?= htmlspecialchars($row['Birthdate'] ?? '') ?>"
                          data-userstatus="<?= htmlspecialchars($row['UserStatus'] ?? 'Active') ?>"
                          data-profileimg="<?= htmlspecialchars($row['Img'] ?? '') ?>"
                          data-accounttype="<?= htmlspecialchars($account_type ?? '') ?>"
                          data-status="<?= htmlspecialchars($status ?? '') ?>"
                          data-totalbalance="<?= htmlspecialchars($total_balance ?? 0) ?>"
                          data-interestrate="<?= htmlspecialchars($interest_rate ?? 0) ?>">
                          <i class="bi bi-eye me-1"></i> Details
                        </button>



                        <!-- GEAR / SETTINGS BUTTON -->
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#gearModal"
                          data-savingsid="<?= $savings_id ?>"
                          data-savingsname="<?= htmlspecialchars($row['savings_type']) ?>"
                          data-interestrate="<?= $interest_rate ?>">
                          <i class="bi bi-gear"></i>
                        </button>

                        <button type="button" class="btn btn-light status-btn" data-bs-toggle="modal"
                          data-bs-target="#statusModal" data-savingsid="<?= $savings_id ?>"
                          data-currentstatus="<?= $status ?>">
                          <i
                            class="bi <?= $status === 'Active' ? 'bi-unlock text-success' :
                              ($status === 'Frozen' ? 'bi-lock-fill text-danger' :
                                ($status === 'Pending' ? 'bi-hourglass-split text-warning' : 'bi-x-circle text-secondary')) ?>"></i>
                        </button>


                      </div>
                    </div>
                  </div>
                </div>

              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted">No savings accounts found.</p>
            <?php endif; ?>
          </div> <!-- closing for savings box -->

          <!-- DETAILS MODAL -->
          <!-- DETAILS MODAL (keep only this one) -->
          <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
              <div class="modal-content rounded-4 shadow">

                <!-- Header -->
                <div class="modal-header bg-light">
                  <h5 class="modal-title">Savings Account Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                  <!-- PROFILE SECTION -->
                  <div class="d-flex align-items-center mb-4">
                    <img id="profileIMG" src="assets/default-avatar.png" class="rounded-circle me-3" width="80"
                      height="80" alt="Profile Picture">
                    <div>
                      <h5 class="mb-0" id="modalCustomerName"></h5>
                      <small class="text-muted" id="Email"></small><br>
                      <small class="text-muted" id="Phone"></small>
                    </div>
                  </div>

                  <hr>

                  <!-- ACCOUNT INFO -->
                  <h6 class="fw-bold mb-2">Account Information</h6>
                  <div class="row mb-3">
                    <div class="col-md-4"><strong>Address:</strong><br><span id="Address"></span></div>
                    <div class="col-md-4"><strong>Birthdate:</strong><br><span id="modalBirthdate"></span></div>
                    <div class="col-md-4"><strong>Account Status:</strong><br><span id="modalUserStatus"></span></div>
                  </div>


                  <hr>

                  <!-- SAVINGS INFO -->
                  <h6 class="fw-bold mb-2">Savings Information</h6>
                  <div class="row mb-3">
                    <div class="col-md-4"><strong>Savings ID:</strong><br><span id="modalSavingsId"></span></div>
                    <div class="col-md-4"><strong>Account Type:</strong><br><span id="modalAccountType"></span></div>
                    <div class="col-md-4"><strong>Status:</strong><br><span id="modalStatus"></span></div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-4"><strong>Balance:</strong><br>₱<span id="modalTotalBalance"></span></div>
                    <div class="col-md-4"><strong>Interest Rate:</strong><br><span id="modalInterestRate"></span>%</div>
                    <div class="col-md-4"><strong>Total Interest Earned:</strong><br>₱<span
                        id="modalTotalInterest">0.00</span></div>
                  </div>

                  <hr>

                  <!-- TRANSACTIONS -->
                  <h6 class="fw-bold mb-2">Transactions</h6>
                  <table class="table table-sm table-striped table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                      </tr>
                    </thead>
                    <tbody id="modalTransactions">
                      <tr>
                        <td colspan="4" class="text-center">Loading...</td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>
            </div>
          </div>

          <!-- SETTINGS MODAL -->
          <div class="modal fade" id="gearModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Edit Account Settings</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                  <form method="POST" id="gearForm">
                    <input type="hidden" id="gearSavingsId" name="savings_id">

                    <div class="mb-3">
                      <label for="gearSavingsType" class="form-label">Savings Type</label>
                      <select id="gearSavingsType" name="savings_type" class="form-select">
                        <option value="Regular">Regular</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Special">Special</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="gearInterestRate" class="form-label">Interest Rate (%)</label>
                      <input type="number" id="gearInterestRate" class="form-control" readonly name="interest_rate"
                        step="0.01" min="0">
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" name="update_savings" class="btn btn-primary" id="gearSaveBtn">Save
                        Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- STATUS CHANGE MODAL -->
          <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Change Account Status</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                  <p>Current status: <strong id="currentStatusText"></strong></p>
                  <form method="POST" id="statusForm">
                    <input type="hidden" name="savings_id" id="statusSavingsId">

                    <div class="d-flex justify-content-around flex-wrap gap-2">
                      <button type="submit" name="toggle_status" value="Active" class="btn btn-success">
                        <i class="bi bi-unlock"></i> Active
                      </button>

                      <button type="submit" name="toggle_status" value="Pending" class="btn btn-warning">
                        <i class="bi bi-hourglass-split"></i> Pending
                      </button>

                      <button type="submit" name="toggle_status" value=" Frozen" class="btn btn-danger">
                        <i class="bi bi-lock-fill"></i> Frozen
                      </button>

                      <button type="submit" name="toggle_status" value="Closed" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Closed
                      </button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <!-- MODAL FOR NEW SAVINGS APPLICATION -->
          <div class="modal fade" id="savingsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">

                <form method="POST" id="newSavingsForm">

                  <!-- header -->
                  <div class="modal-header">
                    <h5 class="modal-title">New Savings Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>

                  <!-- body -->
                  <div class="modal-body">
                    <input type="hidden" name="savings_id">

                    <div class="mb-3">
                      <label class="form-label">Customer:</label>
                      <select name="customer_id" class="form-select" required>
                        <option value="">--Select Customer--</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?= $user['customer_id'] ?>">
                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Savings Type:</label>
                      <select id="newSavingsType" name="savings_type" class="form-select" required>
                        <option value="Regular">Regular</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Special">Special</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Interest Rate (%):</label>
                      <input type="number" id="newInterestRate" class="form-control" readonly name="interest_rate"
                        step="0.01" min="0">
                    </div>


                    <div class="mb-3">
                      <label class="form-label">Initial Deposit:</label>
                      <input type="number" name="initial_deposit" class="form-control" min="0" step="0.01" required>
                    </div>
                  </div>

                  <!-- footer -->
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_savings" class="btn btn-primary">Create Account</button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </section>

        <!-- LOAN SECTION -->
        <section id="loan" class="flex-grow-2 p-2">
          <div class="container-fluid">

            <!-- Header -->
            <div class="row mb-4 g-3">
              <div class="col-12 col-md-6 col-lg-8">
                <h3>Loan Management</h3>
                <small class="text-muted">
                  Manage loan applications and approvals
                </small>
              </div>
              <div class="col-12 col-md-6 col-lg-4 d-flex align-items-center">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loanModal">
                  + New Loan Application
                </button>
              </div>
            </div>

            <!-- Search -->
            <form method="GET" class="card mb-3">
              <div class="card-body">
                <div class="input-group">
                  <span class="input-group-text">
                    <span class="material-symbols-outlined">
                      search
                    </span>
                  </span>
                  <input type="text" name="search" class="form-control"
                    placeholder="Search by customer name or loan type..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                  <button class="btn btn-primary" type="submit">Search</button>
                </div>
              </div>
            </form>


            <!-- Loans Table -->
            <div class="card">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Customer</th>
                        <th class="d-none d-sm-table-cell">Loan Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="d-none d-lg-table-cell">Application Date</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (mysqli_num_rows($loans_result) > 0) {
                        while ($row = mysqli_fetch_assoc($loans_result)) {

                          $formattedAmount = "₱" . number_format($row['amount'], 2);

                          // for styling the STATUS column
                          $statusStyle = '';
                          if ($row['Status'] === 'Approved') {
                            $statusClass = 'status-pill status-approved';
                          } elseif ($row['Status'] === 'Pending') {
                            $statusClass = 'status-pill status-pending';
                          } elseif ($row['Status'] === 'Rejected') {
                            $statusClass = 'status-pill status-rejected';
                          }

                          echo "<tr> 
                                  <td> {$row['customer_name']} </td>
                                  <td> {$row['loan_type']} </td>
                                  <td> <strong>{$formattedAmount}</strong> </td>
                                  <td><span class='$statusClass'>{$row['Status']}</span></td>
                                  <td> {$row['application_date']} </td>
                                  <td>";

                          if ($row['Status'] === 'Pending') {
                            echo "<form method='Post' style='display:inline;'>
                                    <input type='hidden' name='loan_id' value='{$row['loan_id']}'>
                                    <button 
                                      type='button' 
                                      name='action' 
                                      value='Approved'
                                      class='btn-action btn-accept'
                                      data-bs-toggle='modal'
                                      data-bs-target='#confirmModal'
                                      data-action='Approved'
                                      data-loanid='{$row['loan_id']}'
                                    >Accept
                                    </button>
                                  </form>
                                  <form method='post' style='display:inline;'>
                                    <input type='hidden' name='loan_id' value='{$row['loan_id']}'>
                                    <button 
                                      type='button' 
                                      name='action' 
                                      value='Rejected' 
                                      class='btn-action btn-reject'
                                      data-bs-toggle='modal'
                                      data-bs-target='#confirmModal'
                                      data-action='Rejected'
                                      data-loanid='{$row['loan_id']}'
                                    >Reject
                                    </button>
                                  </form>";
                          } else {
                            echo "-";
                          }

                          echo "</td> </tr>";

                        }
                      } else {
                        echo "<tr><td colspan='4'>No Loans Found</td></tr>";
                      }

                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- closing of loans table -->
            </div>
          </div>
          <!-- modal for reject and accept -->
          <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Confirm Action</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p id="confirmMessage"></p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                  </button>
                  <form method="post">
                    <input type="hidden" name="loan_id" id="confirmLoanId">
                    <input type="hidden" name="action" id="confirmAction">
                    <button type="submit" class="btn btn-primary">
                      Yes, Confirm
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- modal for + new loan application -->
          <div class="modal fade" id="loanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <!-- content -->
                <form method="post">
                  <!-- header -->
                  <div class="modal-header">
                    <h5 class="modal-title">New Loan Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <!-- body -->
                  <div class="modal-body">
                    <input type="hidden" name="loan_id">
                    <div class="mb-3">
                      <label class="form-label">Customer Name: </label>
                      <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Loan Type:</label>
                      <select name="loan_type" class="form-select" required>
                        <option value="">--Select Loan Type--</option>
                        <option value="Personal">Personal</option>
                        <option value="Home">Home</option>
                        <option value="Auto">Auto Loan</option>
                        <option value="Business">Business</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Amount:</label>
                      <input type="number" name="amount" class="form-control" min="1" step="0.01" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      Cancel
                    </button>
                    <button type="submit" name="add_loan" class="btn btn-primary">
                      Submit Application
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- modal for reject and accept -->
          <div class="modal fade" id="confirmModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title">Confirm Action</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                  <p id="confirmMessage"></p>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                  </button>

                  <form method="post">
                    <input type="hidden" name="loan_id" id="confirmLoanId">
                    <input type="hidden" name="action" id="confirmAction">
                    <button type="submit" class="btn btn-primary">
                      Yes, Confirm
                    </button>
                  </form>
                </div>

              </div>
            </div>
          </div>

          <!-- modal for + new loan application -->
          <div class="modal fade" id="loanModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <!-- content -->
                <form method="post">

                  <!-- header -->
                  <div class="modal-header">
                    <h5 class="modal-title">New Loan Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <!-- body -->
                  <div class="modal-body">
                    <input type="hidden" name="loan_id">
                    <div class="mb-3">
                      <label class="form-label">Customer Name: </label>
                      <input type="text" name="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Loan Type:</label>
                      <select name="loan_type" class="form-select" required>
                        <option value="">--Select Loan Type--</option>
                        <option value="Personal">Personal</option>
                        <option value="Home">Home</option>
                        <option value="Auto">Auto Loan</option>
                        <option value="Business">Business</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Amount:</label>
                      <input type="number" name="amount" class="form-control" min="1" step="0.01" required>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                      Cancel
                    </button>
                    <button type="submit" name="add_loan" class="btn btn-primary">
                      Submit Application
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </section>

        <!-- FAQS  -->
        <section id="faq" class="flex-grow-2 p-2">
          <div class="row mb-4 g-3">
            <div class="col-12 col-md-6 col-lg-8">
              <h2>FREQUENTLY ASKED QUESTIONS</h2>
              <h6 class="text-muted">
                Answer your questions with our useless responses
              </h6>
            </div>

            <div class="container py-4 mt-0">
              <?php foreach ($faqs as $catIndex => $category): ?>
                <div class="card shadow-sm mb-4">
                  <div class="card-body">
                    <h4 class="card-title mb-3"><?= htmlspecialchars($category['category']) ?></h4>
                    <div class="accordion" id="accordion-<?= $catIndex ?>">
                      <?php foreach ($category['questions'] as $qIndex => $faq):
                        $collapseId = "collapse-{$catIndex}-{$qIndex}";
                        $headingId = "heading-{$catIndex}-{$qIndex}";
                        ?>
                        <div class="accordion-item">
                          <h2 class="accordion-header" id="<?= $headingId ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                              data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                              <?= htmlspecialchars($faq['q']) ?>
                            </button>
                          </h2>
                          <div id="<?= $collapseId ?>" class="accordion-collapse collapse"
                            aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordion-<?= $catIndex ?>">
                            <div class="accordion-body"><?= htmlspecialchars($faq['a']) ?></div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>

        <!-- LOGOUT -->
        <!-- Logout Modal -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">

              <!-- Header -->
              <div class="modal-header bg-light">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <!-- Body -->
              <div class="modal-body">
                <p class="mb-0">Are you sure you want to log out of your account?</p>
              </div>

              <!-- Footer -->
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <!-- Logout form -->
                <form action="../admin/php/logout.php" method="POST" class="d-inline">
                  <input type="hidden" name="logout" value="true">
                  <button type="submit" class="btn btn-danger">Logout</button>
                </form>

              </div>

            </div>
          </div>
        </div>


      </main>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script/script.js"></script>

</html>
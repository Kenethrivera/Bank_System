<?php require_once 'php/Dashboard_php.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS only -->
  <link rel="stylesheet" href="css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <title>Bank Dashboard</title>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav id="sidebar" class="col-auto">
        <div class="sidebar-logo">
          <span class="material-symbols-outlined fs-2">account_balance</span>
          <div class="logo-text">
            <h5 class="mb-0">KennethBank</h5>
            <small>Management System</small>
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
            <a href="#customers" class="nav-link">
              <span class="material-symbols-outlined">manage_accounts</span>
              <span class="link-text">Customers</span>
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
            <a href="#logout" class="nav-link">
              <span class="material-symbols-outlined">logout</span>
              <span class="link-text">Logout</span>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col py-4">
        <section id="dashboard">
          <div class="container py-4">

            <!-- Page Header -->
            <div class="mb-4">
              <h1 class="fw-bold">Dashboard</h1>
              <p class="text-muted">
                Welcome back! Here's what's happening with your bank today.
              </p>
            </div>

            <!-- STAT CARDS -->
            <div class="row g-4 mb-4">

              <!-- Total Customers -->
              <div class="col-md-6 col-lg-4">
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

              <!-- Active Accounts -->
              <!-- <div class="col-md-6 col-lg-3">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="bg-success text-white rounded p-2">
            <i class="bi bi-wallet2 fs-4"></i>
          </div>
          <span class="text-success small">
            <i class="bi bi-arrow-up"></i> +8%
          </span>
        </div>
        <small class="text-muted">Active Accounts</small>
        <h3 class="fw-bold">85</h3>
      </div>
    </div> -->

              <!-- Transactions Today -->
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-purple text-white rounded p-2" style="background:#6f42c1;">
                      <i class="bi bi-arrow-left-right fs-4"></i>
                    </div>
                    <span class="text-success small">
                      <i class="bi bi-arrow-up"></i> +23%
                    </span>
                  </div>
                  <small class="text-muted">Transactions Today</small>
                  <h3 class="fw-bold">32</h3>
                </div>
              </div>

              <!-- Pending Loans -->
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-warning text-white rounded p-2">
                      <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <span class="text-danger small">
                      <i class="bi bi-arrow-down"></i> -5%
                    </span>
                  </div>
                  <small class="text-muted">Pending Loans</small>
                  <h3 class="fw-bold">6</h3>
                </div>
              </div>

            </div>

            <!-- CONTENT ROW -->
            <div class="row g-4">

              <!-- Account Balances -->
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

              <!-- Recent Transactions -->
              <div class="col-lg-6">
                <div class="card p-4">
                  <h5 class="fw-semibold mb-3">Recent Transactions</h5>

                  <!-- Transaction Item -->
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

                  <!-- Transaction Item -->
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

                  <!-- Transaction Item -->
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

          <!-- <h1 class="mb-4">Dashboard</h1>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card-dashboard Customers">
                                <h4>Customer</h4>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card-dashboard Transactions">
                                <h4>Transactions Today</h4>
                            </div>
                        </div>
                        <div class="col-12 col-xx1-6 col-lg-4">
                            <div class="card-dashboard Loan">
                                <h4>Pending Loan</h4>
                            </div>
                        </div>
                    </div> -->
        </section>
        <section id="customers">
          <h2>Customers</h2>
          <!-- savings content -->
        </section>
        <section id="transactions">
          <h2>Transactions</h2>
          <!-- savings content -->
        </section>
        <section id="savings">
          <h2>Savings</h2>
          <!-- savings content -->
          <!-- Kenneth -->
        </section>
        <section id="loan">
          <h2>Loan</h2>
          <!-- savings content -->
          <!-- Kenneth -->
        </section>
        <section id="faq">
          <h2>FAQS</h2>
          <!-- savings content -->
          <!-- Kenneth -->
        </section>
        <section id="logout">
          <h2>Logout</h2>
          <!-- Kenneth -->
          <!-- savings content -->
        </section>

      </main>
    </div>
  </div>


</body>
<script src="script/script.js"></script>
</body>

</html>
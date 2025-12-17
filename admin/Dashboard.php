<?php require_once 'php/Dashboard_php.php'; ?>
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
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-purple text-white rounded p-2" style="background:#6f42c1;">
                      <i class="bi bi-arrow-left-right fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Transactions Today</small>
                  <h3 class="fw-bold">32</h3>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bg-warning text-white rounded p-2">
                      <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                  </div>
                  <small class="text-muted">Pending Loans</small>
                  <h3 class="fw-bold">6</h3>
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
        <section id="customers">
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
                <input type="text" class="form-control" placeholder="Search by account number or customer name...">
              </div>
            </div>

            <div class="card p-3">
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Account Number</th>
                      <th>Customer</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Balance</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>ACC-10001</td>
                      <td>Juan Dela Cruz</td>
                      <td>JuanDelaCruz@gmail.com</td>
                      <td>09512345678</td>
                      <td class="fw-semibold text-success">$12,500.00</td>

                      <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                          data-bs-target="#accountModal">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>


                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </section>
        <div class="modal fade" id="accountModal" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Create / Edit Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <form class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label">Account Number</label>
                    <input type="text" class="form-control" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" step="0.01" required>
                  </div>


                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" step="0.01" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" step="0.01" required>
                  </div>
                </form>
              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script/script.js"></script>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bank Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    
  </style>
</head>
<body>
  <div class="d-flex min-vh-100 align-items-center justify-content-center p-4">
    <div class="w-100" style="max-width: 400px;">
      <div class="card card-custom p-4 bg-white">
        <div class="text-center mb-4">
          <div class="logo-circle mx-auto">
            <i class="bi bi-building text-white fs-2"></i>
          </div>
          <h1 class="h3 fw-bold text-dark">Maangas na Bank</h1>
          <p class="text-muted mb-0">Sign in to your account</p>
        </div>

        <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

        <form id="loginForm" class="mb-3">
          <div class="mb-3">
            <label for="emailInput" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="emailInput" placeholder="Enter your email" required>
          </div>
          <div class="mb-3">
            <label for="passwordInput" class="form-label">Password</label>
            <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <div class="text-center mb-3">
          <p class="small text-muted mb-0">
            Don't have an account? <a href="register.php" class="text-primary text-decoration-none">Register now</a>
          </p>
        </div>
      </div>
    </div>
  </div>
  <script src="script/login.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

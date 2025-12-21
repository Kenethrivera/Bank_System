<?php require_once 'php/register.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register | SecureBank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/register.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center p-3">
<div class="container">
  <div class="card shadow-lg rounded-4 overflow-hidden">
    <div class="row g-0">
      <div class="col-md-4 bg-primary text-white p-4 d-flex flex-column justify-content-between">
        <div>
          <div class="mb-4 fs-3">
            <i class="bi bi-building"></i>
          </div>
          <h4>Join SecureBank</h4>
          <p class="small text-light">
            Start your journey with the most trusted financial partner.
          </p>
        </div>

        <ul class="small list-unstyled">
          <li>✔ Instant Account Creation</li>
          <li>✔ Secure Online Banking</li>
          <li>✔ 24/7 Customer Support</li>
        </ul>
      </div>
      <div class="col-md-8 p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4>Create Account</h4>
          <a href="login.php" class="text-decoration-none">Sign in instead</a>
        </div>
        <div id="errorMessage" class="alert alert-danger d-none"></div>
        <form id="registerForm" method="POST" enctype="multipart/form-data">
          <div class="text-center mb-4">
            <div id="previewWrapper">
              <div class="upload-box mx-auto" onclick="document.getElementById('profilePicture').click()">
                <i class="bi bi-camera fs-3 text-muted"></i>
              </div>
            </div>

            <input type="file" name="img" id="profilePicture" class="d-none" accept="image/*">
            <p class="small text-muted mt-2">Max 5MB</p>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="firstName" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middleName" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="lastName" class="form-control" required>
            </div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control" required>
            </div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Birthdate</label>
              <input type="date" name="birthdate" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" required>
            </div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirmPassword" class="form-control" required>
            </div>
          </div>
          <button name="submit" class="btn btn-primary w-100 mt-4">
            <i class="bi bi-person-plus me-2"></i>Create Account
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="script/register.js"></script>
</body>
</html>

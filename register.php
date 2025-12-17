<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maangas na Bank Registration</title>
  <link rel="stylesheet" href="css/register.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <div class="d-flex min-vh-100 align-items-center justify-content-center p-4">
    <div class="w-100" style="max-width: 1100px;">
      <div class="card card-custom d-grid" style="grid-template-columns: 1fr 2fr;">
        <div class="left-panel d-flex flex-column justify-content-between">
          <div>
            <div class="logo-square">
              <i class="bi bi-building fs-4"></i>
            </div>
            <h2 class="h5 fw-bold mb-2">Join Maangas na Bank</h2>
            <p class="small text-white-50">Start your journey with the most trusted financial partner.</p>
          </div>
          <div class="mt-4">
            <ul class="list-unstyled small">
              <li><div class="dot"></div> Instant Account Creation</li>
              <li><div class="dot"></div> Secure Online Banking</li>
              <li><div class="dot"></div> 24/7 Customer Support</li>
            </ul>
          </div>
        </div>
        <div class="p-4 bg-white">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 fw-bold text-dark mb-0">Create Account</h1>
            <a href="login.php" class="small text-primary text-decoration-none">Sign In instead</a>
          </div>

          <div id="errorMessage" class="alert alert-danger d-none mb-3"></div>
          <form id="registerForm" class="needs-validation" novalidate>
            <div class="d-flex flex-column align-items-center mb-4">
              <div id="profileWrapper" class="profile-picture-wrapper">
                <div id="uploadPlaceholder" class="upload-placeholder">
                  <i class="bi bi-camera fs-3 text-secondary"></i>
                </div>
              </div>
              <input type="file" accept="image/*" id="profilePicture" class="d-none">
              <label for="profilePicture" id="profileLabel" class="mt-2 text-primary small fw-medium" style="cursor:pointer;">
                Upload Profile Picture
              </label>
              <p class="text-xs text-muted mt-1">Max 5MB</p>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <input type="text" class="form-control" name="firstName" placeholder="First Name" required>
              </div>
              <div class="col-md-4">
                <input type="text" class="form-control" name="middleName" placeholder="Middle Name (Optional)">
              </div>
              <div class="col-md-4">
                <input type="text" class="form-control" name="lastName" placeholder="Last Name" required>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
              </div>
              <div class="col-md-6">
                <input type="tel" class="form-control" name="phone" placeholder="Phone" required>
              </div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <input type="date" class="form-control" name="birthdate" required>
              </div>
              <div class="col-md-6">
                <input type="text" class="form-control" name="address" placeholder="Address" required>
              </div>
            </div>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="col-md-6">
                <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2">
              <i class="bi bi-person-plus"></i> Create Account
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>

  <script src="script/register.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

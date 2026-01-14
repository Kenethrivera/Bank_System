var form = document.getElementById("registerForm");
var fileInput = document.getElementById("profilePicture");
var errorBox = document.getElementById("errorMessage"); // Variable name is errorBox
var preview = document.getElementById("previewWrapper");

var hasImage = false;
var allowedExtensions = ['jpg', 'jpeg', 'png'];

fileInput.onchange = function () {
  var file = fileInput.files[0];
  if (!file) return;
  var fileExt = file.name.split('.').pop().toLowerCase();
  if (!allowedExtensions.includes(fileExt)) {
    showError("Only JPG and PNG files are allowed.");
    resetInput();
    return;
  }
  if (file.size > 5 * 1024 * 1024) {
    showError("Image must be under 5MB.");
    resetInput();
    return;
  }
  var reader = new FileReader();
  reader.onload = function () {
    preview.innerHTML = "<img src='" + reader.result + "' class='profile-preview' style='cursor:pointer' onclick='reselectImage()'>";
    hasImage = true;
    errorBox.classList.add("d-none");
  };
  reader.readAsDataURL(file);
};

function reselectImage() { fileInput.click(); }
function resetInput() { fileInput.value = ""; hasImage = false; resetPlaceholder(); }
function resetPlaceholder() {
  preview.innerHTML = "<div class='upload-box mx-auto' onclick=\"document.getElementById('profilePicture').click()\"><i class='bi bi-camera fs-3 text-muted'></i></div>";
}

form.onsubmit = function(e) {
  // Hide error box at start
  errorBox.classList.add("d-none");

  // Get values safely
  var fName = form.querySelector('[name="firstName"]').value.trim();
  var mName = form.querySelector('[name="middleName"]').value.trim();
  var lName = form.querySelector('[name="lastName"]').value.trim();
  var pNumber = form.querySelector('[name="phone"]').value.trim();

  // 1. Name Validation
  var nameRegex = /^[a-zA-Z\sñÑ]+$/;
  if (!nameRegex.test(fName)) {
    e.preventDefault();
    showError("First Name should only contain letters.");
    return false;
  }
  if (mName !== "" && !nameRegex.test(mName)) {
    e.preventDefault();
    showError("Middle Name should only contain letters.");
    return;
  }
  if (!nameRegex.test(lName)) {
    e.preventDefault();
    showError("Last Name should only contain letters.");
    return false;
  }

  // 2. Phone Validation
  var phoneRegex = /^[0-9]+$/;
  if (!phoneRegex.test(pNumber)) {
    e.preventDefault();
    showError("Phone number should only contain digits.");
    return false;
  }

  // 3. Image Check
  if (!hasImage) {
    e.preventDefault();
    showError("Profile picture is required");
    return false;
  }

  // 4. Password Check
  if (form.password.value !== form.confirmPassword.value) {
    e.preventDefault();
    showError("Passwords do not match");
    return false;
  }

  // 5. Age Logic
  var birthInput = form.birthdate.value;
  var birthDate = new Date(birthInput);
  var today = new Date();
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }

  if (age < 18) {
    e.preventDefault();
    showError("You must be at least 18 years old.");
    return false;
  }
};

// FIXED THIS FUNCTION
function showError(msg) {
    errorBox.innerHTML = msg; // Match the variable name above
    errorBox.classList.remove("d-none");
    window.scrollTo(0, 0);
}
var form = document.getElementById("registerForm");
var fileInput = document.getElementById("profilePicture");
var errorBox = document.getElementById("errorMessage");
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
    preview.innerHTML =
      "<img src='" + reader.result + "' class='profile-preview' style='cursor:pointer' onclick='reselectImage()'>";
    hasImage = true;
    errorBox.classList.add("d-none");
  };
  reader.readAsDataURL(file);
};

function reselectImage() {
  fileInput.click();
}
function resetInput() {
  fileInput.value = "";        
  hasImage = false;
  resetPlaceholder();          
}
function resetPlaceholder() {
  preview.innerHTML =
    "<div class='upload-box mx-auto' onclick=\"document.getElementById('profilePicture').click()\">" +
    "<i class='bi bi-camera fs-3 text-muted'></i>" +
    "</div>";
}

form.onsubmit = function(e) {
  errorBox.classList.add("d-none");

  // 1. Check Image
  if (!hasImage) {
    e.preventDefault();
    showError("Profile picture is required");
    resetPlaceholder();
    return;
  }

  // 2. Check Passwords
  if (form.password.value !== form.confirmPassword.value) {
    e.preventDefault();
    showError("Passwords do not match");
    return;
  }

  // 3. Check Age (Under 18 or Over 110)
  var birthInput = form.birthdate.value;
  if (!birthInput) {
      e.preventDefault();
      showError("Please enter your birthdate");
      return;
  }

  var birthDate = new Date(birthInput);
  var today = new Date();
  
  // Calculate age accurately
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  
  // Adjust if birthday hasn't happened yet this year
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
  }

  if (age < 18) {
      e.preventDefault();
      showError("You must be at least 18 years old to register.");
      return;
  }

  if (age > 110) {
      e.preventDefault();
      showError("Please enter a valid birthdate (Age limit: 110).");
      return;
  }
};

function showError(msg) {
  errorBox.innerHTML = msg;
  errorBox.classList.remove("d-none");
}

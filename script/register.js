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

  if (!hasImage) {
    e.preventDefault();
    showError("Profile picture is required");
    resetPlaceholder();
    return;
  }

  if (form.password.value !== form.confirmPassword.value) {
    e.preventDefault();
    showError("Passwords do not match");
    return;
  }
};

function showError(msg) {
  errorBox.innerHTML = msg;
  errorBox.classList.remove("d-none");
}

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
form.onsubmit = function (e) {
  // Hide error box at start of every attempt
  errorBox.classList.add("d-none");

  // Get values specifically by their name attribute to be safe
  var fName = form.querySelector('[name="firstName"]').value.trim();
  var lName = form.querySelector('[name="lastName"]').value.trim();
  var pNumber = form.querySelector('[name="phone"]').value.trim();

  // 1. Name Validation (Letters only)
  // This regex allows uppercase, lowercase, spaces, and the ñ character
  var nameRegex = /^[a-zA-Z\sñÑ]+$/;

  if (!nameRegex.test(fName)) {
    e.preventDefault(); // Stop form submission
    showError("First Name should only contain letters.");
    window.scrollTo(0, 0); // Scroll to top to see error
    return;
  }

  if (!nameRegex.test(lName)) {
    e.preventDefault();
    showError("Last Name should only contain letters.");
    window.scrollTo(0, 0);
    return;
  }

  // 2. Phone Validation (Numbers only)
  var phoneRegex = /^09[0-9]{9}$/;
  if (!phoneRegex.test(pNumber)) {
    e.preventDefault();
    showError("Invalid Phone Number. Must start with 09 (11 digits).");
    window.scrollTo(0, 0);
    return;
  }

  // 3. Image Check
  if (!hasImage) {
    e.preventDefault();
    showError("Profile picture is required");
    resetPlaceholder();
    return;
  }

  // 4. Password matching check
  if (form.password.value !== form.confirmPassword.value) {
    e.preventDefault();
    showError("Passwords do not match");
    return;
  }

  // 5. Age Logic
  var birthInput = form.birthdate.value;
  if (!birthInput) {
    e.preventDefault();
    showError("Please enter your birthdate");
    return;
  }

  var birthDate = new Date(birthInput);
  var today = new Date();
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
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
  if (emailExists) {
    e.preventDefault();
    showError("Email address already exists.");
    return;
  }
  if (phoneExists) {
    e.preventDefault();
    showError("Phone number already exists.");
    return;
  }


};

function showError(msg) {
  jsErrorBox.innerHTML = msg;
  jsErrorBox.classList.remove("d-none");
  window.scrollTo(0, 0); // Scroll to top so user sees the message
}

var emailInput = form.querySelector('[name="email"]');
var emailExists = false;

emailInput.addEventListener("blur", function () {
  var email = emailInput.value.trim();

  if (!email) return;

  fetch("php/check_email.php?email=" + encodeURIComponent(email))
    .then(response => response.json())
    .then(data => {
      if (data.exists) {
        emailExists = true;
        showError("Email address is already registered.");
      } else {
        emailExists = false;
        errorBox.classList.add("d-none");
      }
    })
    .catch(() => {
      showError("Unable to validate email at the moment.");
    });
});

var phoneInput = form.querySelector('[name="phone"]');
var phoneExists = false;

phoneInput.addEventListener("blur", function () {
  var phone = phoneInput.value.trim();

  if (!phone) return;

  fetch("php/check_phone.php?phone=" + encodeURIComponent(phone))
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        phoneExists = true;
        showError("Phone number is already registered.");
      } else {
        phoneExists = false;
        errorBox.classList.add("d-none");
      }
    })
    .catch(() => {
      showError("Unable to validate phone number.");
    });
});

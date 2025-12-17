const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('errorMessage');

loginForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const email = document.getElementById('emailInput').value;
    const password = document.getElementById('passwordInput').value;

    if ((email === 'admin@bank.com' && password === 'admin123') ||
        (email === 'staff@bank.com' && password === 'staff123')) {
        alert('Login successful!');
        navigation: window.location.href = 'admin/dashboard.php';
    } else {
        errorMessage.textContent = 'Invalid email or password';
        errorMessage.classList.remove('d-none');
    }
});
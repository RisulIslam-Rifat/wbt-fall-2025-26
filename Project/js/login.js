document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Handle Login Submission
    const handleLogin = async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();

        if (!email || !password) {
            alert('Please fill in all fields.');
            return;
        }

        // Show loading state (optional, can be improved)
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerText;
        submitBtn.innerText = 'Logging in...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('../php/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                    // Role is no longer sent, it is determined by the backend
                }),
            });

            const result = await response.json();

            if (result.success) {
                // Store user info
                localStorage.setItem('user', JSON.stringify(result.user));

                // Show success message
                alert('Login successful! Redirecting...');

                // Redirect based on backend response
                window.location.href = result.redirect;
            } else {
                alert(result.message || 'Login failed. Please check your credentials.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        } finally {
            // Reset button state
            submitBtn.innerText = originalBtnText;
            submitBtn.disabled = false;
        }
    };

    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

// Toggle Password Visibility
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {
        input.type = 'text';
        icon.innerText = 'visibility_off';
    } else {
        input.type = 'password';
        icon.innerText = 'visibility';
    }
}

// Forgot Password Handler
function forgotPassword(role) {
    // Role is less relevant now, but keeping the function structure for potentially passing email later
    const email = document.getElementById('email').value;
    alert(`Password reset functionality will be implemented soon.`);
    // You could redirect to a forgot password page here
    // window.location.href = 'forgot_password.html';
}
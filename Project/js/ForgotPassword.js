document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('resetForm');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const emailInput = document.getElementById('email');
    const otpInput = document.getElementById('otp');
    const newPasswordInput = document.getElementById('newPassword');
    const submitBtn = document.getElementById('submitBtn');

    let currentStep = 1;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (currentStep === 1) {
            handleRequestOTP();
        } else {
            handleResetPassword();
        }
    });

    function handleRequestOTP() {
        const email = emailInput.value.trim();
        if (!email) return;

        submitBtn.disabled = true;
        submitBtn.innerText = 'Sending Code...';

        fetch('../php/reset_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'request_otp', email: email })
        })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Send Code';

                if (data.success) {
                    // Alert the OTP for testing purposes
                    if (data.debug_otp) {
                        alert(`[DEMO MODE] Your Verification Code is: ${data.debug_otp}`);
                    }
                    
                    // Switch to Step 2
                    currentStep = 2;
                    step1.classList.add('hidden');
                    step2.classList.remove('hidden');
                    emailInput.disabled = true;
                    submitBtn.innerText = 'Reset Password';
                    otpInput.focus();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Send Code';
                alert('An error occurred. Please try again.');
            });
    }

    function handleResetPassword() {
        const email = emailInput.value.trim();
        const otp = otpInput.value.trim();
        const newPassword = newPasswordInput.value.trim();

        if (!otp || !newPassword) {
            alert('Please fill in all fields.');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerText = 'Updating Password...';

        fetch('../php/reset_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'reset_password', email: email, otp: otp, new_password: newPassword })
        })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Reset Password';

                if (data.success) {
                    alert('Success! Your password has been reset. You can now login.');
                    window.location.href = 'login.html';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Reset Password';
                alert('An error occurred. Please try again.');
            });
    }
});

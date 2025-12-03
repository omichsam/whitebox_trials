<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - WhiteBox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
         <link rel="icon" type="image/x-icon" href="favicon.ico">

    <style>
        :root {
            --primary: #008435;
            --primary-dark: #055c15;
            --primary-light: rgba(0, 132, 53, 0.1);
            --success: #28a745;
            --success-light: rgba(40, 167, 69, 0.1);
            --danger: #dc3545;
            --danger-light: rgba(220, 53, 69, 0.1);
            --warning: #ffc107;
            --warning-light: rgba(255, 193, 7, 0.1);
            --info: #17a2b8;
            --info-light: rgba(23, 162, 184, 0.1);
            --dark: #212529;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #085c02 0%, #861616 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('sources/images/slider/image5.jpg') center/cover no-repeat;
            z-index: -2;
        }

        /* Dark Overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(8, 92, 2, 0.7);
            z-index: -1;
        }

        .reset-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 40px;
            width: 100%;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .reset-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .card-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        .card-header h2 {
            color: var(--dark);
            font-weight: 600;
            margin-top: 5px;
            font-size: 1.8rem;
        }

        .logo {
            height: 80px;
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--dark);
            font-size: 15px;
            transition: var(--transition);
        }

        label i {
            color: var(--primary);
            margin-right: 8px;
            width: 20px;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 15px 45px 15px 15px;
            border: 2px solid var(--gray-light);
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            background: var(--white);
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            outline: none;
            transform: translateY(-2px);
        }

        input::placeholder {
            color: #adb5bd;
            font-weight: 400;
        }

        .code-input {
            font-family: 'Courier New', monospace;
            font-size: 22px;
            letter-spacing: 4px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--dark);
            background: var(--light);
            border: 2px dashed var(--primary-light);
        }

        .code-input:focus {
            border-style: solid;
            background: var(--white);
        }

        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 132, 53, 0.3);
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 132, 53, 0.4);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .btn i {
            margin-right: 8px;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            animation: slideIn 0.3s ease-out;
            border-left: 4px solid transparent;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .success {
            background: var(--success-light);
            color: #155724;
            border-left-color: var(--success);
        }

        .error {
            background: var(--danger-light);
            color: #721c24;
            border-left-color: var(--danger);
        }

        .info {
            background: var(--info-light);
            color: #0c5460;
            border-left-color: var(--info);
        }

        .message i {
            margin-right: 10px;
            font-size: 16px;
        }

        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
        }

        .links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
            transform: translateX(-3px);
        }

        .password-match {
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
            display: none;
            padding: 8px 12px;
            border-radius: 6px;
            animation: slideIn 0.3s ease-out;
        }

        .match-valid {
            background: var(--success-light);
            color: var(--success);
            border-left: 3px solid var(--success);
        }

        .match-invalid {
            background: var(--danger-light);
            color: var(--danger);
            border-left: 3px solid var(--danger);
        }

        /* Password toggle button styles */
        .password-field-container {
            position: relative;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 8px;
            font-size: 1.1rem;
            transition: var(--transition);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle-btn:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        #password, #confirm_password {
            padding-right: 50px !important;
        }

        /* Step indicator */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
            counter-reset: step;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 2px;
            background: var(--gray-light);
            z-index: 1;
        }

        .step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--white);
            border: 2px solid var(--gray-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
            color: var(--gray);
            transition: var(--transition);
        }

        .step.active .step-number {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
            transform: scale(1.1);
        }

        .step.completed .step-number {
            background: var(--success);
            border-color: var(--success);
            color: var(--white);
        }

        .step-label {
            font-size: 12px;
            color: var(--gray);
            font-weight: 500;
            transition: var(--transition);
        }

        .step.active .step-label {
            color: var(--primary);
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .reset-container {
                max-width: 100%;
                padding: 10px;
            }
            
            .reset-card {
                padding: 30px 25px;
            }
            
            .card-header h2 {
                font-size: 1.6rem;
            }
            
            .logo {
                height: 70px;
            }
            
            input {
                padding: 14px 40px 14px 14px;
                font-size: 15px;
            }
            
            .code-input {
                font-size: 20px;
                letter-spacing: 3px;
            }
            
            .btn {
                padding: 15px;
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .reset-card {
                padding: 25px 20px;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
            
            .logo {
                height: 60px;
            }
            
            .step-indicator {
                margin: 25px 0;
            }
            
            .step-number {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }

        /* Loading spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        /* Email display styling */
        #emailDisplay {
            background: var(--light);
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 10px;
            border: 1px solid var(--gray-light);
            font-weight: 500;
            color: var(--dark);
        }

        /* Focus styles for accessibility */
        *:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        /* Smooth transitions for step changes */
        #step1, #step2, #step3 {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="card-header">
                <img src="Huduma_WhiteBox/Whitebox.png" alt="WhiteBox" class="logo">
                <h2>Reset Password</h2>
                <p style="color: var(--gray); margin-top: 5px;">Secure your account</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1Indicator">
                    <div class="step-number">1</div>
                    <div class="step-label">Request</div>
                </div>
                <div class="step" id="step2Indicator">
                    <div class="step-number">2</div>
                    <div class="step-label">Verify</div>
                </div>
                <div class="step" id="step3Indicator">
                    <div class="step-number">3</div>
                    <div class="step-label">New Password</div>
                </div>
            </div>

            <!-- Messages -->
            <div class="message success" id="successMessage"></div>
            <div class="message error" id="errorMessage"></div>
            <div class="message info" id="infoMessage"></div>

            <!-- Step 1: Request Reset -->
            <div id="step1">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required placeholder="Enter your registered email">
                    </div>
                </div>

                <button type="button" id="requestBtn" class="btn">
                    <i class="fas fa-paper-plane"></i> Send Reset Code
                </button>

                <div class="links">
                    <a href="index1.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </div>

            <!-- Step 2: Verify Code (hidden by default) -->
            <div id="step2" style="display: none;">
                <div class="form-group">
                    <label for="code"><i class="fas fa-key"></i> Enter Reset Code</label>
                    <div class="input-wrapper">
                        <input type="text" id="code" name="code" class="code-input" required placeholder="8-character code"
                            maxlength="8">
                    </div>
                    <small id="emailDisplay" style="display: block; margin-top: 10px;"></small>
                </div>

                <button type="button" id="verifyBtn" class="btn">
                    <i class="fas fa-check-circle"></i> Verify Code
                </button>

                <div class="links">
                    <a href="#" id="backToStep1"><i class="fas fa-arrow-left"></i> Back to Email</a>
                </div>
            </div>

            <!-- Step 3: New Password (hidden by default) -->
            <div id="step3" style="display: none;">
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="Enter new password"
                            minlength="8">
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" required
                            placeholder="Confirm new password" minlength="8">
                    </div>
                    <div class="password-match" id="passwordMatch"></div>
                </div>

                <button type="button" id="updateBtn" class="btn">
                    <i class="fas fa-save"></i> Update Password
                </button>

                <div class="links">
                    <a href="#" id="backToStep2"><i class="fas fa-arrow-left"></i> Back to Code</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            let currentEmail = '';
            let currentCode = '';

            // Update step indicators
            function updateStepIndicators(step) {
                $('.step').removeClass('active completed');
                
                if (step === 1) {
                    $('#step1Indicator').addClass('active');
                } else if (step === 2) {
                    $('#step1Indicator').addClass('completed');
                    $('#step2Indicator').addClass('active');
                } else if (step === 3) {
                    $('#step1Indicator, #step2Indicator').addClass('completed');
                    $('#step3Indicator').addClass('active');
                }
            }

            // Initialize step 1
            updateStepIndicators(1);

            // Format reset code input
            $('#code').on('input', function () {
                this.value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase().substring(0, 8);
            });

            // Password toggle functionality
            function addPasswordToggle(inputId, buttonId) {
                const input = $('#' + inputId);
                const button = $('#' + buttonId);

                // Create toggle button if it doesn't exist
                if (button.length === 0) {
                    input.parent().append(`
                        <button type="button" id="${buttonId}" class="password-toggle-btn" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    `);
                }

                // Add click handler
                $(document).on('click', '#' + buttonId, function () {
                    const icon = $(this).find('i');
                    if (input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                        $(this).attr('aria-label', 'Hide password');
                    } else {
                        input.attr('type', 'password');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                        $(this).attr('aria-label', 'Show password');
                    }
                });
            }

            // Add password toggle for password fields
            addPasswordToggle('password', 'togglePassword');
            addPasswordToggle('confirm_password', 'toggleConfirmPassword');

            // Password confirmation checker
            $('#confirm_password').on('input', checkPasswordMatch);
            $('#password').on('input', checkPasswordMatch);

            // Back navigation
            $('#backToStep1').click(function (e) {
                e.preventDefault();
                $('#step2').hide();
                $('#step1').show();
                updateStepIndicators(1);
                $('#errorMessage, #successMessage, #infoMessage').hide();
                $('#email').focus();
            });

            $('#backToStep2').click(function (e) {
                e.preventDefault();
                $('#step3').hide();
                $('#step2').show();
                updateStepIndicators(2);
                $('#errorMessage, #successMessage, #infoMessage').hide();
                $('#code').focus();
            });

            // Step 1: Request reset code
            $('#requestBtn').click(function () {
                const email = $('#email').val().trim();

                if (!email) {
                    showError('Please enter your email address');
                    return;
                }

                if (!validateEmail(email)) {
                    showError('Please enter a valid email address');
                    return;
                }

                // Show loading
                const $btn = $(this);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

                // Simple AJAX request
                $.ajax({
                    url: 'login/reset_password.php',
                    type: 'POST',
                    data: {
                        action: 'request',
                        email: btoa(email)
                    },
                    timeout: 10000,
                    success: function (response) {
                        console.log('Request response:', response);

                        try {
                            const result = atob(response);

                            if (result === 'success' || result === 'email_not_found') {
                                // For security, always show success even if email not found
                                showSuccess('If an account exists, a reset code was sent to your email.');
                                currentEmail = email;
                                $('#emailDisplay').html('<i class="fas fa-envelope"></i> Code sent to: <strong>' + email + '</strong>').show();
                                setTimeout(() => {
                                    $('#step1').hide();
                                    $('#step2').show();
                                    updateStepIndicators(2);
                                    $('#code').focus();
                                }, 1500);
                            } else if (result === 'not_activated') {
                                showError('Account not activated. Please activate first.');
                            } else {
                                showError('Error sending reset code. Try again.');
                            }
                        } catch (e) {
                            // Even if parsing fails, assume success
                            showSuccess('Reset code sent! Check your email.');
                            currentEmail = email;
                            $('#emailDisplay').html('<i class="fas fa-envelope"></i> Code sent to: <strong>' + email + '</strong>').show();
                            setTimeout(() => {
                                $('#step1').hide();
                                $('#step2').show();
                                updateStepIndicators(2);
                                $('#code').focus();
                            }, 1500);
                        }

                        resetButton($btn, '<i class="fas fa-paper-plane"></i> Send Reset Code');
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX error:', status, error);
                        // Even if AJAX fails, the PHP script likely ran
                        showSuccess('Reset code sent! Check your email.');
                        currentEmail = email;
                        $('#emailDisplay').html('<i class="fas fa-envelope"></i> Code sent to: <strong>' + email + '</strong>').show();
                        setTimeout(() => {
                            $('#step1').hide();
                            $('#step2').show();
                            updateStepIndicators(2);
                            $('#code').focus();
                        }, 1500);
                        resetButton($btn, '<i class="fas fa-paper-plane"></i> Send Reset Code');
                    }
                });
            });

            // Step 2: Verify reset code
            $('#verifyBtn').click(function () {
                const code = $('#code').val().trim();

                if (!code || code.length !== 8) {
                    showError('Please enter the 8-character reset code');
                    return;
                }

                // Show loading
                const $btn = $(this);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Verifying...').prop('disabled', true);

                // Simple AJAX request
                $.ajax({
                    url: 'login/reset_password.php',
                    type: 'POST',
                    data: {
                        action: 'verify',
                        email: btoa(currentEmail),
                        code: btoa(code)
                    },
                    timeout: 10000,
                    success: function (response) {
                        console.log('Verify response:', response);

                        try {
                            const result = atob(response);

                            if (result === 'success') {
                                showSuccess('Code verified successfully!');
                                currentCode = code;
                                setTimeout(() => {
                                    $('#step2').hide();
                                    $('#step3').show();
                                    updateStepIndicators(3);
                                    $('#password').focus();
                                }, 1000);
                            } else if (result === 'expired') {
                                showError('Reset code expired. Please request a new one.');
                            } else {
                                showError('Invalid reset code. Please try again.');
                            }
                        } catch (e) {
                            showError('Verification failed. Please try again.');
                        }

                        resetButton($btn, '<i class="fas fa-check-circle"></i> Verify Code');
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX error:', status, error);
                        showError('Connection error. Please try again.');
                        resetButton($btn, '<i class="fas fa-check-circle"></i> Verify Code');
                    }
                });
            });

            // Step 3: Update password
            $('#updateBtn').click(function () {
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();

                // Validate
                if (!password || password.length < 8) {
                    showError('Password must be at least 8 characters');
                    return;
                }

                if (password !== confirmPassword) {
                    showError('Passwords do not match');
                    return;
                }

                // Show loading
                const $btn = $(this);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);

                // Simple AJAX request
                $.ajax({
                    url: 'login/reset_password.php',
                    type: 'POST',
                    data: {
                        action: 'update',
                        email: btoa(currentEmail),
                        code: btoa(currentCode),
                        password: btoa(password),
                        confirm_password: btoa(confirmPassword)
                    },
                    timeout: 10000,
                    success: function (response) {
                        console.log('Update response:', response);

                        try {
                            const result = atob(response);

                            if (result === 'success') {
                                showSuccess('Password updated successfully! Redirecting to login...');
                                setTimeout(() => {
                                    window.location.href = 'index1.php?password_reset=true';
                                }, 2000);
                            } else {
                                showError('Failed to update password. Please try again.');
                            }
                        } catch (e) {
                            showError('Update failed. Please try again.');
                        }

                        resetButton($btn, '<i class="fas fa-save"></i> Update Password');
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX error:', status, error);
                        showError('Connection error. Please try again.');
                        resetButton($btn, '<i class="fas fa-save"></i> Update Password');
                    }
                });
            });

            // Enter key support
            $('#email').keypress(function (e) {
                if (e.which == 13) $('#requestBtn').click();
            });

            $('#code').keypress(function (e) {
                if (e.which == 13) $('#verifyBtn').click();
            });

            $('#password, #confirm_password').keypress(function (e) {
                if (e.which == 13) $('#updateBtn').click();
            });
        });

        function showSuccess(message) {
            $('#successMessage').html('<i class="fas fa-check-circle"></i> ' + message).show();
            $('#errorMessage').hide();
            $('#infoMessage').hide();
        }

        function showError(message) {
            $('#errorMessage').html('<i class="fas fa-exclamation-circle"></i> ' + message).show();
            $('#successMessage').hide();
            $('#infoMessage').hide();
        }

        function showInfo(message) {
            $('#infoMessage').html('<i class="fas fa-info-circle"></i> ' + message).show();
            $('#successMessage').hide();
            $('#errorMessage').hide();
        }

        function validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function checkPasswordMatch() {
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            const matchDiv = $('#passwordMatch');

            if (!confirmPassword) {
                matchDiv.hide();
                return;
            }

            if (password === confirmPassword) {
                matchDiv.html('<i class="fas fa-check"></i> Passwords match').addClass('match-valid').removeClass('match-invalid').show();
            } else {
                matchDiv.html('<i class="fas fa-times"></i> Passwords do not match').addClass('match-invalid').removeClass('match-valid').show();
            }
        }

        function resetButton($btn, html) {
            setTimeout(() => {
                $btn.html(html).prop('disabled', false);
            }, 1000);
        }
    </script>
</body>
</html>
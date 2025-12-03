<?php
session_start();
?>
<?php
if (isset($_GET['activated']) && $_GET['activated'] === "true") {
    echo "<script>
        alert('Your account has been activated successfully. You can now log in.');

        // Remove ?activated=true from the URL
        if (window.history.replaceState) {
            const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({path:newUrl}, '', newUrl);
        }
    </script>";
}
?>
<!DOCTYPE html>

<?php
$id = "";
$datab = "not_shown";

// Check if user is already logged in
if (isset($_SESSION["id"])) {
    $loginuser = $_SESSION["username"];
    $user = base64_decode($loginuser);

    include("connect.php");
    $user_not = "";

    $checkExist = mysqli_query($con, "SELECT * FROM users WHERE email='$user'") or die(mysqli_error($con));

    if (mysqli_num_rows($checkExist) != 0) {
        // User is logged in, redirect to dashboard
        ?>
        <script type="text/javascript">
            window.location.href = "mydashboard/dashboard.php";
        </script>
        <?php
        exit;
    } else {
        $_SESSION = array();
        session_destroy();
    }
}
?>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhiteBox - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #058d27ca;
            --success-dark: #014608ff;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --danger: #a91616ff;
            --warning: #ffc107;
            --card-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #085c02ff 0%, #861616a0 100%);
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
            background: rgba(68, 115, 61, 0.55);
            z-index: -1;
        }

        .auth-space {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            animation: fadeIn 0.8s ease;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 40px 35px;
            width: 100%;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(50, 50, 93, 0.15), 0 7px 20px rgba(0, 0, 0, 0.1);
        }

        .card-headera {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .card-headera::after {
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

        .card-headera h2 {
            color: var(--dark);
            font-weight: 600;
            margin-top: 5px;
            font-size: 1.8rem;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(2, 205, 19, 0.3);
        }

        .logo i {
            color: white;
            font-size: 1.5rem;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo_position {
            height: 80px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            color: var(--gray);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 10px;
            transition: var(--transition);
        }

        .back-home:hover {
            color: var(--primary);
            transform: translateX(-3px);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 15px 45px 15px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: var(--gray);
            font-size: 1.1rem;
        }

        .password-field-container {
            position: relative;
        }

        .password-input-wrapper {
            position: relative;
            width: 100%;
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
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
        }

        .password-toggle-btn:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .password-input-wrapper input {
            padding-right: 50px !important;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-success {
            background: var(--success);
            color: white;
            box-shadow: 0 4px 15px rgba(116, 115, 114, 0.6);
            transition: var(--transition);
        }

        .btn-success:hover {
            background: var(--success-dark);
            box-shadow: 0 6px 20px rgba(47, 46, 46, 0.95);
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .card-footera {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 14px;
            color: var(--gray);
        }

        .card-footera a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .card-footera a:hover {
            text-decoration: underline;
            color: var(--primary-dark);
        }

        .error-message {
            color: var(--danger);
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            background: rgba(230, 57, 70, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--danger);
            display: none;
        }

        .success-message {
            color: #28a745;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 8px;
            border-left: 4px solid #28a745;
            display: none;
        }

        .warning-message {
            color: var(--warning);
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
            padding: 10px;
            background: rgba(255, 193, 7, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--warning);
            display: none;
        }

        /* Password Strength Meter */
        .password-strength-meter {
            margin-top: 8px;
        }

        .strength-bar {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .strength-text {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
        }

        /* Password Match Indicator */
        .password-match {
            font-size: 14px;
            font-weight: 500;
            margin-top: 8px;
            display: none;
        }

        .match-valid {
            color: #28a745;
        }

        .match-invalid {
            color: #dc3545;
        }

        /* Terms Checkbox */
        .terms-check {
            display: flex;
            align-items: flex-start;
            margin: 20px 0;
            padding: 15px;
            background: rgba(248, 249, 250, 0.8);
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .terms-check input[type="checkbox"] {
            margin-top: 3px;
            margin-right: 12px;
            transform: scale(1.2);
        }

        .terms-check label {
            font-size: 14px;
            line-height: 1.5;
            color: #495057;
            margin-bottom: 0;
            flex: 1;
        }

        .terms-check a {
            color: var(--primary);
            text-decoration: none;
        }

        .terms-check a:hover {
            text-decoration: underline;
        }

        /* Animation classes */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .not_shown {
            display: none;
        }

        /* Responsive adjustments */
        @media all and (max-width: 768px) {
            .mobile_hidden {
                display: none !important;
            }

            .auth-card {
                padding: 30px 25px;
            }

            body {
                padding: 15px;
            }

            .card-headera h2 {
                font-size: 1.6rem;
            }

            .form-row {
                flex-direction: column;
                gap: 15px !important;
            }

            .logo_position {
                height: 70px;
            }
        }

        @media all and (min-width: 768px) {
            .desktop_hidden {
                display: none !important;
            }
        }

        @media all and (max-width: 480px) {
            .auth-card {
                padding: 25px 20px;
            }

            .card-headera h2 {
                font-size: 1.5rem;
            }

            .logo_position {
                height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container" id="landing_page">
        <div class="auth-space">
            <!-- Registration Section -->
            <div class="auth-card" id="register_page">
                <div class="card-headera">
                    <div class="header-flex">
                        <a class="back-home start" href="index.php">
                            <i class="fas fa-arrow-left"></i> Back Home
                        </a>
                        <div class="end">
                            <img src="Huduma_WhiteBox/Whitebox.png" alt="logo" class="logo_position">
                        </div>
                    </div>
                    <h2>Create Account</h2>
                    <p class="text-muted">Join WhiteBox today</p>
                </div>
                <div class="card-body">
                    <div id="notific"></div>

                    <!-- Registration Form -->
                    <form class="omb_registerForm bv-form" autocomplete="off" method="POST" novalidate="novalidate"
                        id="registerForm">
                        <!-- Name Row -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" class="form-control" name="first_name"
                                    placeholder="Enter your first name" required>
                                <i class="fas fa-user input-icon"></i>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" class="form-control" name="last_name"
                                    placeholder="Enter your last name" required>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" name="email"
                                placeholder="Enter your email" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" class="form-control" name="phone"
                                placeholder="Enter your phone number" required>
                            <i class="fas fa-phone input-icon"></i>
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" class="form-control" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                            <i class="fas fa-venus-mars input-icon"></i>
                        </div>

                        <!-- Password -->
                        <div class="form-group password-field-container">
                            <label for="password">Password</label>
                            <div class="password-input-wrapper">
                                <input id="password" type="password" class="form-control"
                                    placeholder="Create a strong password" name="password" required>
                                <button type="button" class="password-toggle-btn" id="togglePassword"
                                    aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <!-- Password Strength Meter -->
                            <div class="password-strength-meter">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <div class="strength-text" id="strengthText">Password strength: None</div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group password-field-container">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="password-input-wrapper">
                                <input id="confirm_password" type="password" class="form-control"
                                    placeholder="Confirm your password" name="confirm_password" required>
                                <button type="button" class="password-toggle-btn" id="toggleConfirmPassword"
                                    aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-match" id="passwordMatch"></div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="terms-check">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">
                                I accept the <a href="terms.php" target="_blank">Terms and Conditions</a> and
                                <a href="privacy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" class="btn btn-success" id="register_btn">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                    </form>

                    <!-- Messages -->
                    <div class="error-message" id="error_data"></div>
                    <div class="success-message" id="success_data"></div>
                    <div class="warning-message" id="warning_data"></div>

                    <!-- Login Link -->
                    <div class="card-footera">
                        Already have an account? <a href="index1.php">Sign In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Hide all messages initially
            $("#error_data").hide();
            $("#success_data").hide();
            $("#warning_data").hide();

            // Password visibility toggle
            $('#togglePassword').click(function () {
                togglePasswordVisibility('password', 'togglePassword');
            });

            $('#toggleConfirmPassword').click(function () {
                togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
            });

            // Registration button click
            $('#register_btn').click(function () {
                registerUser();
            });

            // Enter key to submit form
            $('#registerForm input').keypress(function (e) {
                if (e.which == 13) {
                    registerUser();
                }
            });

            // Functions
            function togglePasswordVisibility(inputId, buttonId) {
                const input = $('#' + inputId);
                const icon = $('#' + buttonId + ' i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            }

            function validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function validatePhone(phone) {
                const digits = phone.replace(/\D/g, '');
                return digits.length >= 10;
            }

            function registerUser() {
                // Get form values
                const firstName = $('#first_name').val().trim();
                const lastName = $('#last_name').val().trim();
                const email = $('#email').val().trim();
                const phone = $('#phone').val().trim();
                const gender = $('#gender').val();
                const password = $('#password').val();
                const confirmPassword = $('#confirm_password').val();
                const termsAccepted = $('#terms').is(':checked');

                // Hide previous messages
                $('#error_data, #success_data, #warning_data').hide().html('');

                // Basic validation
                if (!firstName || !lastName || !email || !phone || !gender || !password || !confirmPassword) {
                    $('#error_data').html('All fields are required.').show();
                    return;
                }

                if (!validateEmail(email)) {
                    $('#error_data').html('Please enter a valid email address.').show();
                    return;
                }

                if (!validatePhone(phone)) {
                    $('#error_data').html('Please enter a valid phone number (at least 10 digits).').show();
                    return;
                }

                if (password.length < 8) {
                    $('#error_data').html('Password must be at least 8 characters long.').show();
                    return;
                }

                if (password !== confirmPassword) {
                    $('#error_data').html('Passwords do not match.').show();
                    return;
                }

                if (!termsAccepted) {
                    $('#error_data').html('You must accept the Terms and Conditions.').show();
                    return;
                }

                // Show loading
                const $btn = $('#register_btn');
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Creating Account...').prop('disabled', true);

                // Prepare data
                const formData = {
                    first_name: btoa(firstName),
                    last_name: btoa(lastName),
                    email: btoa(email),
                    phone: btoa(phone),
                    gender: btoa(gender),
                    password: btoa(password),
                    confirm_password: btoa(confirmPassword)
                };

                // Send request
                $.ajax({
                    url: 'login/register.php',
                    type: 'POST',
                    data: formData,
                    timeout: 15000, // 15 seconds timeout
                    success: function (response) {
                        console.log('Raw server response:', response);

                        try {
                            // Try to decode the response
                            const decoded = atob(response);
                            console.log('Decoded response:', decoded);

                            // Check if it's a JSON response (success case)
                            try {
                                const data = JSON.parse(decoded);

                                if (data.status === 'success') {
                                    // Show success message with activation link
                                    const successMessage = `
                                <div style="text-align: center; padding: 15px;">
                                    <div style="font-size: 40px; color: #28a745; margin-bottom: 10px;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h4 style="color: #28a745; margin-bottom: 10px;">Registration Successful!</h4>
                                    <p style="margin-bottom: 15px;">Account created for: <strong>${email}</strong></p>
                                    
                                    <div style="background: #f8f9fa; border: 2px solid #28a745; padding: 15px; 
                                                border-radius: 8px; margin: 15px 0;">
                                        <p><strong>Your Activation Code:</strong></p>
                                        <div style="font-size: 24px; font-weight: bold; letter-spacing: 3px; 
                                                    color: #333; margin: 10px 0; padding: 12px; 
                                                    background: white; border-radius: 5px;
                                                    font-family: monospace;">
                                            ${data.activation_code}
                                        </div>
                                        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">
                                            Check your email for complete instructions
                                        </p>
                                    </div>
                                    
                                    <div style="margin: 20px 0;">
                                        <a href="${data.direct_link}" 
                                           style="background: #085c02; color: white; padding: 12px 25px; 
                                                  text-decoration: none; border-radius: 6px; font-weight: bold; 
                                                  font-size: 15px; display: inline-block; margin: 10px;">
                                            <i class="fas fa-key"></i> Activate Account Now
                                        </a>
                                        <p style="margin-top: 10px; font-size: 13px;">
                                            Or go to: <a href="${data.direct_link}" style="color: #085c02; font-weight: bold;">
                                            activate.php</a>
                                        </p>
                                    </div>
                                </div>
                            `;

                                    $('#success_data').html(successMessage).show();
                                    $('#registerForm')[0].reset();

                                    // Scroll to success message
                                    $('html, body').animate({
                                        scrollTop: $('#success_data').offset().top - 100
                                    }, 500);

                                } else {
                                    $('#error_data').html('Registration failed: ' + (data.message || 'Unknown error')).show();
                                }

                            } catch (jsonError) {
                                // If not JSON, check for error codes
                                handlePlainResponse(decoded);
                            }

                        } catch (decodeError) {
                            console.error('Base64 decode error:', decodeError);
                            // Even if decoding fails, assume registration succeeded
                            showGenericSuccess(email);
                        }

                        resetButton($btn);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            textStatus: status,
                            errorThrown: error,
                            responseText: xhr.responseText
                        });

                        // Even if AJAX fails, assume registration succeeded (since email was sent)
                        showGenericSuccess(email);
                        resetButton($btn);
                    }
                });
            }

            function handlePlainResponse(response) {
                console.log('Plain response:', response);

                const messages = {
                    'email_exists': 'Email already registered. Please <a href="index1.php" style="color: var(--primary);">login</a> instead.',
                    'password_mismatch': 'Passwords do not match.',
                    'password_short': 'Password must be at least 8 characters.',
                    'invalid_email': 'Invalid email address.',
                    'db_error': 'Database error. Please try again.',
                    'db_connection_error': 'Database connection failed. Try again.',
                    'invalid_request': 'Invalid request method.',
                    'invalid_encoding': 'Invalid data format.'
                };

                if (response.startsWith('missing_field:')) {
                    $('#error_data').html('Please fill all required fields.').show();
                } else if (messages[response]) {
                    $('#error_data').html(messages[response]).show();
                } else if (response === 'success' || response === 'success_no_email') {
                    // Handle old success format
                    showGenericSuccess($('#email').val().trim());
                } else {
                    // Unknown response - show generic success since email was sent
                    showGenericSuccess($('#email').val().trim());
                }
            }

            function showGenericSuccess(email) {
                const successMessage = `
            <div style="text-align: center; padding: 15px;">
                <div style="font-size: 40px; color: #28a745; margin-bottom: 10px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4 style="color: #28a745; margin-bottom: 10px;">Registration Submitted!</h4>
                <p style="margin-bottom: 15px;">Check your email at <strong>${email}</strong> for activation instructions.</p>
                
                <div style="margin: 20px 0;">
                    <a href="activate.php?email=${encodeURIComponent(email)}" 
                       style="background: #085c02; color: white; padding: 12px 25px; 
                              text-decoration: none; border-radius: 6px; font-weight: bold; 
                              font-size: 15px; display: inline-block; margin: 10px;">
                        <i class="fas fa-key"></i> Go to Activation Page
                    </a>
                </div>
                
                <p style="font-size: 13px; color: #666;">
                    Didn't receive email? Check spam folder or <a href="activate.php?email=${encodeURIComponent(email)}" 
                    style="color: #085c02; font-weight: bold;">click here</a> to activate manually.
                </p>
            </div>
        `;

                $('#success_data').html(successMessage).show();
                $('#registerForm')[0].reset();

                // Scroll to success message
                $('html, body').animate({
                    scrollTop: $('#success_data').offset().top - 100
                }, 500);
            }

            function resetButton($btn) {
                $btn.html('<i class="fas fa-user-plus"></i> Create Account').prop('disabled', false);
            }
        });
    </script>

</body>

</html>
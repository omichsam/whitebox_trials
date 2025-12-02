<?php
session_start();
?>

<?php
// Handle alerts for activation and password reset
if (isset($_GET['activated']) && $_GET['activated'] === "true") {
    echo "<script>
        alert('Your account has been activated successfully. You can now log in.');

        // Remove query string from URL
        if (window.history.replaceState) {
            const newUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
            window.history.replaceState({path:newUrl}, '', newUrl);
        }
    </script>";
}

if (isset($_GET['password_reset']) && $_GET['password_reset'] === "true") {
    echo "<script>
        alert('Your password has been reset successfully. You can now log in with your new password.');

        // Remove query string from URL
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

if (isset($_SESSION["id"])) {
    $loginuser = $_SESSION["username"];
    $user = base64_decode($loginuser);

    include("connect.php");
    $user_not = "";

    $checkExist = mysqli_query($con, "SELECT * FROM users WHERE email='$user'") or die(mysqli_error($con));

    if (mysqli_num_rows($checkExist) != 0) {
        $user_not = "dashboard";
    } else {
        $_SESSION = array();
        session_destroy();
        ?>
        <script type="text/javascript">
            location.replace("index.php");
        </script>
        <?php
    }
} else {
    $datab = "";
}
?>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhiteBox - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

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
            max-width: 450px;
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
            height: 100px;
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 1.5rem;
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
            box-shadow: 0 0 0 3px rgba(1, 184, 22, 0.6);
            outline: none;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: var(--gray);
            font-size: 1.5rem;
        }

        .password-field-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 42px;
            cursor: pointer;
            color: var(--gray);
            transition: var(--transition);
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 20px;
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

        .links-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            transition: var(--transition);
        }

        .auth-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .activation-message {
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid var(--warning);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            display: none;
        }

        .activation-message i {
            color: var(--warning);
            margin-right: 10px;
            font-size: 18px;
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

            .links-container {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
        }
    </style>
    <?php include('links.php'); ?>
</head>

<body>
    <input type="hidden" class="splashinputs" id="user_email">
    <div class="containerfluid- no_padding not_shown" id="home_pagedata"></div>

    <div class="auth-container" id="landing_page">
        <div class="auth-space">
            <!-- Login Section Only -->
            <div class="auth-card" id="login_page">
                <div class="card-headera">
                    <div class="header-flex">
                        <a class="back-home start" href="index.php">
                            <i class="fas fa-arrow-left"></i> Back Home
                        </a>

                        <div class="end">
                            <img src="Huduma_WhiteBox/Whitebox.png" alt="logo" class="logo_position">
                        </div>
                    </div>

                    <h2>Welcome Back</h2>
                    <p class="text-muted">Sign in to your WhiteBox account</p>
                </div>
                <div class="card-body">
                    <div id="notific"></div>

                    <!-- Activation Message -->
                    <div class="activation-message" id="activationMessage">
                        <i class="fas fa-exclamation-circle"></i>
                        <span id="activationText">Your account needs to be activated. Please check your email for
                            activation instructions.</span>
                    </div>

                    <form class="omb_loginForm bv-form" autocomplete="off" method="POST" novalidate="novalidate">
                        <div class="form-group">
                            <label for="username" class="">Email Address</label>
                            <input type="email" id="username" class="form-control" name="email"
                                placeholder="Enter your email" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        <div class="form-group password-field-container">
                            <label for="password-field">Password</label>
                            <input id="password-field" type="password" class="form-control"
                                placeholder="Enter your password" name="password" required>
                            <span class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>

                        <div class="links-container">
                            <a href="register.php" class="auth-link">
                                <i class="fas fa-user-plus"></i> Create Account
                            </a>
                            <a href="reset_password.php" class="auth-link">
                                <i class="fas fa-key"></i> Forgot Password?
                            </a>
                        </div>

                        <button type="button" class="btn btn-success pt-3" id="login_btn" style="margin-top: 20px;">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </form>

                    <!-- Error Messages -->
                    <div class="error-message" id="error_data"></div>
                    <div class="success-message" id="success_data"></div>
                    <div class="warning-message" id="warning_data"></div>
                </div>
                <div class="card-footera">
                    Need help? <a href="contact.php">Contact Support</a>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" class="splashinputs" id="secret_key">

    <script>
        // Generic function for multiple password fields
        $(document).ready(function () {
            // Initialize all password toggle buttons
            $('.password-toggle-btn').each(function () {
                const toggleBtn = $(this);
                // Find the associated password input (previous sibling input)
                const passwordInput = toggleBtn.parent().find('input[type="password"], input[type="text"]');

                toggleBtn.click(function () {
                    const isPassword = passwordInput.attr('type') === 'password';
                    const icon = $(this).find('i');

                    passwordInput.attr('type', isPassword ? 'text' : 'password');
                    icon.toggleClass('fa-eye fa-eye-slash');
                    $(this).attr('aria-label', isPassword ? 'Hide password' : 'Show password');
                });
            });
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize variables
            let isProcessing = false;

            // Hide all messages initially
            $("#error_data, #success_data, #warning_data").hide();
            $("#activationMessage").hide();

            // ============ HELPER FUNCTIONS ============

            /**
             * Show error message
             * @param {string} message - Error message to display
             */
            function showError(message) {
                $("#error_data").html(message).show();
                $("#success_data, #warning_data").hide();
                // Scroll to error if needed
                $('html, body').animate({
                    scrollTop: $("#error_data").offset().top - 100
                }, 300);
            }

            /**
             * Show success message
             * @param {string} message - Success message to display
             */
            function showSuccess(message) {
                $("#success_data").html(message).show();
                $("#error_data, #warning_data").hide();
            }

            /**
             * Show warning message
             * @param {string} message - Warning message to display
             */
            function showWarning(message) {
                $("#warning_data").html(message).show();
                $("#error_data, #success_data").hide();
            }

            /**
             * Reset login button to original state
             */
            function resetLoginButton() {
                $("#login_btn").html('<i class="fas fa-sign-in-alt"></i> Sign In').prop('disabled', false);
                isProcessing = false;
            }

            /**
             * Set login button to loading state
             */
            function setButtonLoading() {
                $("#login_btn").html('<i class="fas fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
                isProcessing = true;
            }

            /**
             * Validate email format
             * @param {string} email - Email to validate
             * @returns {boolean} - True if valid
             */
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            /**
             * Handle successful login
             * @param {string} username - Base64 encoded username
             * @param {string} dashboardUrl - Dashboard URL
             */
            function handleSuccessfulLogin(username, dashboardUrl) {
                $("#user_email").val(username);

                // Show success message
                showSuccess("Login successful! Redirecting to dashboard...");

                // Load dashboard content
                $.post(dashboardUrl, { model: username }, function (data) {
                    // Hide any modals
                    $(".modal-backdrop").hide();
                    $('body').removeClass("modal-open");

                    // Replace content with dashboard
                    $("#landing_page").html(data);

                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Dashboard load error:", textStatus, errorThrown);
                    showError("Error loading dashboard. Please refresh the page.");
                    resetLoginButton();
                });
            }

            /**
             * Handle activation required - redirect to activation page
             * @param {string} email - User's email address
             */
            function handleActivationRequired(email) {
                // Show brief message before redirect
                showWarning("Account requires activation. Redirecting to activation page...");

                // Store email in session via AJAX if needed
                $.post("store_email_session.php", {
                    email: email
                }, function () {
                    // Redirect after a short delay
                    setTimeout(function () {
                        window.location.href = "activate.php";
                    }, 1500);
                }).fail(function () {
                    // Even if session store fails, still redirect
                    setTimeout(function () {
                        window.location.href = "activate.php";
                    }, 1500);
                });
            }

            /**
             * Process login response
             * @param {string} response - Response from server
             * @param {string} username - Base64 encoded username
             * @param {string} email - User's email
             */
            function processLoginResponse(response, username, email) {
                console.log("Server response:", response);

                // Trim and clean response
                response = response.trim();

                // Check for success responses
                if (response === "portal" || response === "e_learning") {
                    handleSuccessfulLogin(username, "mydashboard/dashboard.php");
                    return true;
                }

                // Check for activation redirect
                if (response === "activation_required" ||
                    response === "redirect_to_activation" ||
                    response === "activate") {
                    handleActivationRequired(email);
                    return true;
                }

                // Check for specific error messages
                switch (response) {
                    case "user_not_found":
                        showError("Account not found. Please check your email or <a href='register.php'>create an account</a>.");
                        break;
                    case "invalid_credentials":
                    case "invalid_password":
                        showError("Invalid password. Please try again.");
                        // Clear password field
                        $("#password-field").val('').focus();
                        break;
                    case "missing_credentials":
                        showError("Email and password are required.");
                        break;
                    case "db_connection_error":
                    case "db_error":
                        showError("Database connection error. Please try again later.");
                        break;
                    case "server_error":
                        showError("Server error. Please try again later.");
                        break;
                    default:
                        // If it's an error message we don't recognize
                        if (response.startsWith("error:")) {
                            showError(response.substring(6));
                        } else {
                            showError("Unexpected response: " + response);
                        }
                }

                return false;
            }

            // ============ EVENT HANDLERS ============

            /**
             * Handle login button click
             */
            $("#login_btn").click(function () {
                // Prevent multiple clicks
                if (isProcessing) return;

                // Get form values
                const email = $("#username").val().trim();
                const password = $("#password-field").val().trim();

                // Hide all messages
                $("#error_data, #success_data, #warning_data").hide();

                // Validate inputs
                if (!email || !password) {
                    showError("Email and Password are required!");
                    return;
                }

                if (!validateEmail(email)) {
                    showError("Please enter a valid email address!");
                    return;
                }

                // Encode credentials for security
                const busername = btoa(email);
                const bpass = btoa(password);

                // Set button to loading state
                setButtonLoading();

                // Send login request
                $.ajax({
                    url: "login/login.php",
                    type: "POST",
                    data: {
                        busername: busername,
                        bpass: bpass
                    },
                    dataType: "text", // Expect text response
                    timeout: 30000, // 30 second timeout

                    // Success handler
                    success: function (response, textStatus, jqXHR) {
                        try {
                            // Try to decode base64 response
                            const decodedResponse = atob(response);
                            processLoginResponse(decodedResponse, busername, email);
                        } catch (decodeError) {
                            // If not base64, process as plain text
                            processLoginResponse(response, busername, email);
                        }
                    },

                    // Error handler
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error:", textStatus, errorThrown);

                        // Check if we got a response (even with error status)
                        if (jqXHR.responseText) {
                            try {
                                const decodedResponse = atob(jqXHR.responseText);
                                if (decodedResponse === "activation_required" ||
                                    decodedResponse === "redirect_to_activation") {
                                    handleActivationRequired(email);
                                    return;
                                }
                            } catch (e) {
                                // Not base64, check plain text
                                if (jqXHR.responseText.includes("activation")) {
                                    handleActivationRequired(email);
                                    return;
                                }
                            }
                        }

                        // Network/connection errors
                        if (textStatus === "timeout") {
                            showError("Request timeout. Please try again.");
                        } else if (textStatus === "error") {
                            if (jqXHR.status === 0) {
                                showError("Network error. Please check your internet connection.");
                            } else if (jqXHR.status === 500) {
                                showError("Server error (500). Please try again later.");
                            } else {
                                showError("Error " + jqXHR.status + ": " + errorThrown);
                            }
                        } else {
                            showError("Connection error: " + textStatus);
                        }

                        resetLoginButton();
                    },

                    // Always execute
                    complete: function () {
                        // Button reset happens in individual handlers
                    }
                });
            });

            // ============ KEYBOARD SUPPORT ============

            /**
             * Handle Enter key in form fields
             */
            $("#username, #password-field").keypress(function (e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    $("#login_btn").click();
                }
            });

            // ============ PASSWORD VISIBILITY TOGGLE ============

            /**
             * Toggle password visibility
             */
            $("#togglePassword").click(function () {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    $(this).attr('title', 'Hide password');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    $(this).attr('title', 'Show password');
                }

                // Keep focus on input
                input.focus();
            });

            // ============ FORM VALIDATION ON BLUR ============

            /**
             * Real-time email validation
             */
            $("#username").on('blur', function () {
                const email = $(this).val().trim();
                if (email && !validateEmail(email)) {
                    $(this).addClass('error-border');
                    // Add error styling
                    $(this).css({
                        'border-color': 'var(--danger)',
                        'box-shadow': '0 0 0 3px rgba(169, 22, 22, 0.1)'
                    });
                } else {
                    $(this).removeClass('error-border');
                    $(this).css({
                        'border-color': '',
                        'box-shadow': ''
                    });
                }
            });

            /**
             * Clear errors when user starts typing
             */
            $("#username, #password-field").on('input', function () {
                if ($(this).hasClass('error-border')) {
                    $(this).removeClass('error-border');
                    $(this).css({
                        'border-color': '',
                        'box-shadow': ''
                    });
                }
                $("#error_data").hide();
            });

            // ============ AUTO-LOGIN IF SESSION EXISTS ============

            <?php if (isset($_SESSION["id"]) && isset($loginuser) && isset($user_not)): ?>
                // Auto-login for returning users with valid session
                $(function () {
                    const loginuser = '<?php echo addslashes($loginuser); ?>';
                    const user_not = '<?php echo addslashes($user_not); ?>';

                    if (loginuser && user_not) {
                        $("#user_email").val(loginuser);

                        // Show loading message
                        $("#login_btn").html('<i class="fas fa-spinner fa-spin"></i> Restoring session...').prop('disabled', true);
                        showSuccess("Restoring your session...");

                        // Load dashboard
                        $.post("mydashboard/" + user_not + ".php", { model: loginuser }, function (data) {
                            $("#landing_page").html(data);
                        }).fail(function () {
                            showError("Session expired. Please login again.");
                            resetLoginButton();
                        });
                    }
                });
            <?php endif; ?>

            // ============ INITIAL FOCUS ============

            // Auto-focus on email field on page load
            $("#username").focus();

            // ============ ADDITIONAL UTILITIES ============

            // Add error border CSS class
            const errorStyle = document.createElement('style');
            errorStyle.textContent = `
            .error-border {
                border-color: var(--danger) !important;
                box-shadow: 0 0 0 3px rgba(169, 22, 22, 0.1) !important;
                animation: shake 0.5s ease-in-out;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            
            #login_btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }
        `;
            document.head.appendChild(errorStyle);

            // ============ CLEANUP ON PAGE UNLOAD ============

            // Prevent form submission on page refresh
            $(window).on('beforeunload', function () {
                if (isProcessing) {
                    return "Login is in progress. Are you sure you want to leave?";
                }
            });

            // Reset processing flag when page is successfully loaded
            $(window).on('load', function () {
                isProcessing = false;
            });

        }); // End of document ready
    </script>


    <!-- #region -->
</body>

</html>
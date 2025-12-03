<?php
session_start();
include("connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enhanced debug logging
function debug_log($message, $data = null)
{
    $log_file = 'activation_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";

    if ($data !== null) {
        if (is_array($data)) {
            // Remove sensitive data from logs
            $sanitized_data = $data;
            if (isset($sanitized_data['email'])) {
                $sanitized_data['email'] = substr($sanitized_data['email'], 0, 3) . '...';
            }
            if (isset($sanitized_data['activation_code'])) {
                $sanitized_data['activation_code'] = '***';
            }
            $log_message .= " | Data: " . json_encode($sanitized_data);
        } else {
            $log_message .= " | Data: " . substr($data, 0, 100);
        }
    }

    $log_message .= PHP_EOL;
    file_put_contents($log_file, $log_message, FILE_APPEND);

    // Also log to PHP error log for immediate visibility
    error_log("ACTIVATION_DEBUG: " . strip_tags($message));
}

// Log script start
debug_log("=== ACTIVATION SCRIPT STARTED ===", [
    'script' => basename(__FILE__),
    'method' => $_SERVER['REQUEST_METHOD'],
    'action' => $_GET['action'] ?? $_POST['action'] ?? 'none',
    'session_id' => session_id(),
    'ip' => $_SERVER['REMOTE_ADDR']
]);

// Check database connection
if (!$con) {
    debug_log("Database connection FAILED");
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
} else {
    debug_log("Database connection SUCCESS");
}

// Generate 8-character code
function generateCode($length = 8)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// Function to test mail system directly
function testMailSystem($email, $subject, $message)
{
    debug_log("Testing mail system directly");

    $mail_file = "Huduma_WhiteBox/mails/general.php";

    if (!file_exists($mail_file)) {
        debug_log("Mail file not found: $mail_file");
        return false;
    }

    debug_log("Mail file found, attempting to include");

    // Clear output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    ob_start();

    // Set required variables
    $mail_subject = $subject;
    $mail_message = $message;
    $mail_to = $email;

    try {
        include($mail_file);
        $output = ob_get_clean();

        debug_log("Mail file included successfully", [
            'output_length' => strlen($output),
            'output_preview' => substr($output, 0, 200)
        ]);

        return [
            'success' => empty($output) || stripos($output, 'success') !== false,
            'output' => $output
        ];

    } catch (Exception $e) {
        $output = ob_get_clean();
        debug_log("Exception in mail system", $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'output' => $output
        ];
    }
}

// Enhanced sendActivationEmail function
function sendActivationEmail($email, $first_name, $last_name, $activation_code)
{
    global $con;

    debug_log("SEND ACTIVATION EMAIL CALLED", [
        'recipient' => $email,
        'name' => "$first_name $last_name",
        'code' => $activation_code
    ]);

    // Sanitize inputs
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        debug_log("Invalid email address: $email");
        return false;
    }

    $activation_link = "http://whitebox.go.ke/activate.php?action=activate&code=" .
        urlencode($activation_code) . "&email=" . urlencode(base64_encode($email));

    $expiry_time = date('l, F j, Y \a\t g:i A', strtotime('+24 hours'));

    $subject = "Activate Your WhiteBox Account";

    // Simple HTML email template
    $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Account Activation</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: #085c02; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='margin: 0;'>WhiteBox Account Activation</h1>
                </div>
                
                <div style='background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 10px 10px;'>
                    <p>Dear <strong>$first_name $last_name</strong>,</p>
                    <p>Welcome to WhiteBox! Please activate your account to get started.</p>
                    
                    <div style='background: #f0f8ff; border: 2px solid #085c02; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                        <h3 style='color: #085c02; margin-top: 0;'>Your Activation Code</h3>
                        <div style='font-family: monospace; font-size: 32px; font-weight: bold; letter-spacing: 3px; color: #085c02; margin: 15px 0; padding: 10px; background: white; border: 1px solid #ccc; border-radius: 5px;'>
                            $activation_code
                        </div>
                        <p>Enter this 8-character code on the activation page</p>
                       
                    </div>
                    
                    <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                        <p><strong>Important:</strong> This code expires on $expiry_time</p>
                    </div>
                    
                    <p>If you didn't create this account, please ignore this email.</p>
                    
                    <p>Best regards,<br><strong>The WhiteBox Team</strong></p>
                </div>
                
                <div style='text-align: center; margin-top: 20px; color: #666; font-size: 12px;'>
                    <p>© " . date('Y') . " WhiteBox. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";

    // Try custom mail system first
    debug_log("Attempting to send via custom mail system");
    $mail_result = testMailSystem($email, $subject, $message);

    if ($mail_result['success']) {
        debug_log("Custom mail system SUCCESS");
        return 'custom_system';
    } else {
        debug_log("Custom mail system FAILED", $mail_result['error'] ?? 'Unknown error');
    }

    // Fallback to PHP mail()
    debug_log("Falling back to PHP mail()");

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: WhiteBox <noreply@whitebox.go.ke>" . "\r\n";
    $headers .= "Reply-To: support@whitebox.go.ke" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (function_exists('mail')) {
        debug_log("Calling PHP mail() function");

        // Suppress warnings to capture error manually
        $old_error_level = error_reporting();
        error_reporting(0);

        $result = mail($email, $subject, $message, $headers);

        error_reporting($old_error_level);

        if ($result) {
            debug_log("PHP mail() returned TRUE");
            return 'php_mail';
        } else {
            debug_log("PHP mail() returned FALSE");

            // Check last error
            $last_error = error_get_last();
            if ($last_error) {
                debug_log("Last PHP error", $last_error);
            }

            return false;
        }
    } else {
        debug_log("PHP mail() function does not exist");
        return false;
    }
}

// Function to handle activation
function handleActivation()
{
    global $con;

    debug_log("HANDLE ACTIVATION called", $_REQUEST);

    // Get activation data from GET or POST
    $code = $_GET['code'] ?? $_POST['code'] ?? '';
    $email_encoded = $_GET['email'] ?? $_POST['email'] ?? '';
    $email_input = $_POST['email_input'] ?? '';

    debug_log("Raw inputs", [
        'code' => $code,
        'email_encoded' => $email_encoded,
        'email_input' => $email_input
    ]);

    // If we have direct email input, encode it
    if (!empty($email_input) && empty($email_encoded)) {
        $email_encoded = base64_encode($email_input);
        debug_log("Encoded email input", $email_encoded);
    }

    // Validate inputs
    if (empty($code) || empty($email_encoded)) {
        $error_msg = "Please provide both email and activation code.";
        debug_log("Missing parameters", ['code' => $code, 'email_encoded' => $email_encoded]);
        $_SESSION['activation_error'] = $error_msg;
        header("Location: activate.php");
        exit();
    }

    // Decode email
    $email = base64_decode($email_encoded);
    if (!$email) {
        debug_log("Failed to decode email", $email_encoded);
        $_SESSION['activation_error'] = "Invalid email format.";
        header("Location: activate.php");
        exit();
    }

    $email = mysqli_real_escape_string($con, trim($email));
    $code = mysqli_real_escape_string($con, trim($code));

    debug_log("Processing activation", [
        'email' => $email,
        'code' => $code
    ]);

    // Check activation
    $result = checkAndProcessActivation($email, $code);

    debug_log("Activation result", $result);

    // Redirect based on result
    if ($result['status'] === 'success') {
        $_SESSION['activation_success'] = $result['message'];
        debug_log("Redirecting to success page");
        header("Location: index1.php?activated=true");
    } else {
        $_SESSION['activation_error'] = $result['message'];
        $_SESSION['activation_data'] = ['email' => $email_encoded, 'code' => $code];
        debug_log("Redirecting back to form with error");
        header("Location: activate.php");
    }
    exit();
}

// Function to check and process activation
function checkAndProcessActivation($email, $code)
{
    global $con;

    debug_log("CHECK AND PROCESS ACTIVATION", ['email' => $email, 'code' => $code]);

    // Check if token is valid and not expired
    $check_query = "SELECT * FROM users WHERE email='$email' 
                    AND token='$code' 
                    AND token_type='activation' 
                    AND token_expires_at > NOW()";

    debug_log("Executing query", $check_query);

    $result = mysqli_query($con, $check_query);

    if (!$result) {
        $error = mysqli_error($con);
        debug_log("Database query error", $error);
        return ['status' => 'error', 'message' => 'Database error. Please try again.'];
    }

    $row_count = mysqli_num_rows($result);
    debug_log("Query returned $row_count rows");

    if ($row_count > 0) {
        $user = mysqli_fetch_assoc($result);
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $user_id = $user['id'];

        debug_log("Valid activation found", [
            'user_id' => $user_id,
            'name' => "$first_name $last_name"
        ]);

        // Activation successful - update user
        $update_query = "UPDATE users SET 
                         token = NULL,
                         token_type = NULL,
                         token_expires_at = NULL,
                         country = 'KE',
                         user_state = 'active',
                         updated_at = NOW()
                         WHERE email='$email' AND id='$user_id'";

        debug_log("Executing update query", $update_query);

        if (mysqli_query($con, $update_query)) {
            debug_log("User activated successfully");

            // Try to send welcome email (but don't fail activation if email fails)
            try {
                sendWelcomeEmail($email, $first_name, $last_name);
            } catch (Exception $e) {
                debug_log("Welcome email failed but activation succeeded", $e->getMessage());
            }

            return ['status' => 'success', 'message' => 'Account activated successfully! You can now login.'];
        } else {
            $error = mysqli_error($con);
            debug_log("Update query failed", $error);
            return ['status' => 'error', 'message' => 'Activation failed. Please try again.'];
        }
    }

    // Check different failure scenarios
    return getActivationFailureReason($email, $code);
}

// Function to determine activation failure reason
function getActivationFailureReason($email, $code)
{
    global $con;

    debug_log("Getting activation failure reason", ['email' => $email, 'code' => $code]);

    // Check if account is already activated
    $check_active = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND country='KE'");

    if (!$check_active) {
        $error = mysqli_error($con);
        debug_log("Check active query failed", $error);
        return ['status' => 'error', 'message' => 'Database error.'];
    }

    if (mysqli_num_rows($check_active) > 0) {
        debug_log("Account already activated");
        return ['status' => 'error', 'message' => 'This account is already activated.'];
    }

    // Check if token exists but expired
    $check_expired = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND token='$code' 
                                        AND token_type='activation' AND token_expires_at <= NOW()");

    if (!$check_expired) {
        $error = mysqli_error($con);
        debug_log("Check expired query failed", $error);
    } else if (mysqli_num_rows($check_expired) > 0) {
        debug_log("Activation code expired");
        return ['status' => 'error', 'message' => 'Activation code has expired.'];
    }

    // Check if email exists but code is wrong
    $check_email = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if (!$check_email) {
        $error = mysqli_error($con);
        debug_log("Check email query failed", $error);
    } else if (mysqli_num_rows($check_email) > 0) {
        debug_log("Invalid activation code");
        return ['status' => 'error', 'message' => 'Invalid activation code.'];
    }

    debug_log("No account found with this email");
    return ['status' => 'error', 'message' => 'No account found with this email.'];
}

// Function to check activation status (AJAX)
function checkActivationStatus()
{
    global $con;

    $email_encoded = $_POST['email'] ?? '';

    debug_log("CHECK ACTIVATION STATUS", ['email_encoded' => $email_encoded]);

    if (empty($email_encoded)) {
        echo json_encode(['status' => 'error', 'message' => 'Email required']);
        exit();
    }

    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));

    debug_log("Checking status for", ['email' => $email]);

    $query = "SELECT id, first_name, country, token, token_expires_at FROM users WHERE email='$email'";
    debug_log("Executing query", $query);

    $result = mysqli_query($con, $query);

    if (!$result) {
        $error = mysqli_error($con);
        debug_log("Query failed", $error);
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        debug_log("User found", [
            'user_id' => $user['id'],
            'country' => $user['country'],
            'has_token' => !empty($user['token'])
        ]);

        if ($user['country'] === 'KE') {
            echo json_encode(['status' => 'activated', 'message' => 'Account is already activated']);
        } elseif (!empty($user['token'])) {
            $expiry = strtotime($user['token_expires_at']);
            $now = time();
            $hours_left = round(($expiry - $now) / 3600, 1);

            debug_log("Token details", [
                'expiry' => $user['token_expires_at'],
                'hours_left' => $hours_left
            ]);

            if ($expiry > $now) {
                echo json_encode([
                    'status' => 'pending',
                    'message' => 'Activation pending',
                    'hours_left' => $hours_left
                ]);
            } else {
                echo json_encode(['status' => 'expired', 'message' => 'Activation code expired']);
            }
        } else {
            echo json_encode(['status' => 'no_token', 'message' => 'No activation code found']);
        }
    } else {
        debug_log("Account not found");
        echo json_encode(['status' => 'not_found', 'message' => 'Account not found']);
    }
    exit();
}

// Function to resend activation code
function resendActivationCode()
{
    global $con;

    $email_encoded = $_POST['email'] ?? '';

    debug_log("RESEND ACTIVATION CODE", ['email_encoded' => $email_encoded]);

    if (empty($email_encoded)) {
        echo json_encode(['status' => 'error', 'message' => 'Email required']);
        exit();
    }

    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));

    debug_log("Processing resend for", ['email' => $email]);

    // Check if user exists
    $checkUser = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if (!$checkUser) {
        $error = mysqli_error($con);
        debug_log("Check user query failed", $error);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $error]);
        exit();
    }

    if (mysqli_num_rows($checkUser) === 0) {
        debug_log("Account not found");
        echo json_encode(['status' => 'error', 'message' => 'Account not found']);
        exit();
    }

    $user = mysqli_fetch_assoc($checkUser);
    $first_name = $user['first_name'];
    $last_name = $user['last_name'];

    debug_log("User found", ['name' => "$first_name $last_name"]);

    // Check if already activated
    if ($user['country'] === 'KE') {
        debug_log("Account already activated");
        echo json_encode(['status' => 'activated', 'message' => 'Account is already activated']);
        exit();
    }

    // Generate new activation code
    $activation_code = generateCode(8);
    $token_type = 'activation';
    $current_time = date('Y-m-d H:i:s');
    $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

    debug_log("Generated new code", [
        'code' => $activation_code,
        'expires' => $token_expires_at
    ]);

    // Update database
    $update = mysqli_query($con, "UPDATE users SET 
                 token = '$activation_code',
                 token_type = '$token_type',
                 token_expires_at = '$token_expires_at',
                 updated_at = '$current_time'
                 WHERE email = '$email'");

    if ($update) {
        debug_log("Database updated successfully");

        // Send activation email
        debug_log("Calling sendActivationEmail function");
        $email_result = sendActivationEmail($email, $first_name, $last_name, $activation_code);

        if ($email_result) {
            debug_log("Activation email sent successfully", ['method' => $email_result]);
            echo json_encode([
                'status' => 'success',
                'message' => 'New activation code sent! Please check your email.',
                'code' => $activation_code,
                'method' => $email_result
            ]);
        } else {
            debug_log("FAILED to send activation email");
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again later or contact support.',
                'debug' => 'Check activation_debug.log for details'
            ]);
        }
    } else {
        $error = mysqli_error($con);
        debug_log("Update token failed", $error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to generate new code: ' . $error]);
    }
    exit();
}

// Function to verify activation code (AJAX)
function verifyActivationCode()
{
    global $con;

    $code = $_POST['code'] ?? '';
    $email_encoded = $_POST['email'] ?? '';

    debug_log("VERIFY ACTIVATION CODE", ['code' => $code, 'email_encoded' => $email_encoded]);

    if (empty($code) || empty($email_encoded)) {
        echo json_encode(['status' => 'error', 'message' => 'Code and email required']);
        exit();
    }

    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));
    $code = mysqli_real_escape_string($con, trim($code));

    $result = checkAndProcessActivation($email, $code);
    debug_log("Verification result", $result);
    echo json_encode($result);
    exit();
}

// Function to send welcome email
function sendWelcomeEmail($email, $first_name, $last_name)
{
    debug_log("SEND WELCOME EMAIL", ['email' => $email, 'name' => "$first_name $last_name"]);

    $subject = "Welcome to WhiteBox - Account Activated";

    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #085c02; color: white; padding: 20px; text-align: center;'>
                <h1 style='margin: 0;'>Welcome to WhiteBox!</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>Your account is now active</p>
            </div>
            <div style='padding: 30px; background: #f9f9f9;'>
                <p>Dear <strong>$first_name $last_name</strong>,</p>
                <p>Your WhiteBox account has been successfully activated!</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://whitebox.go.ke/index1.php' 
                       style='background: #085c02; color: white; padding: 15px 30px; 
                              text-decoration: none; border-radius: 6px; font-weight: bold;
                              font-size: 16px; display: inline-block;'>
                        Go to Login
                    </a>
                </div>
                
                <p>Best regards,<br><strong>The WhiteBox Team</strong></p>
            </div>
        </div>
    ";

    // Use the same test function
    $mail_result = testMailSystem($email, $subject, $message);

    if ($mail_result['success']) {
        debug_log("Welcome email sent via custom system");
        return 'custom_system';
    }

    // Fallback to PHP mail()
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: WhiteBox <noreply@whitebox.go.ke>" . "\r\n";

    if (function_exists('mail') && mail($email, $subject, $message, $headers)) {
        debug_log("Welcome email sent via PHP mail()");
        return 'php_mail';
    }

    debug_log("Failed to send welcome email");
    return false;
}

// Function to show activation form (default view)
function showActivationForm()
{
    debug_log("SHOWING ACTIVATION FORM");

    // Test mail system button
    $show_test_button = isset($_SESSION['is_admin']) || true; // Adjust based on your admin check

    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Activate Account - WhiteBox</title>
         <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">

        <style>
            :root {
                --primary: #085c02;
                --primary-light: #0c7c03;
                --primary-dark: #054001;
                --success: #28a745;
                --success-light: #34ce57;
                --danger: #dc3545;
                --danger-light: #e4606d;
                --warning: #ffc107;
                --warning-light: #ffce3a;
                --dark: #212529;
                --dark-light: #343a40;
                --gray: #6c757d;
                --gray-light: #adb5bd;
                --light: #f8f9fa;
                --white: #ffffff;
                --shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
                --shadow-light: 0 4px 15px rgba(0, 0, 0, 0.1);
                --border-radius: 12px;
                --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            html {
                height: 100%;
            }

            body {
                font-family: 'Poppins', sans-serif;
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
                background: url('sources/images/slider/image5.jpg') center/cover no-repeat fixed;
                z-index: -2;
                animation: zoomEffect 20s infinite alternate ease-in-out;
            }

            @keyframes zoomEffect {
                0% {
                    transform: scale(1);
                }

                100% {
                    transform: scale(1.05);
                }
            }

            /* Dark Overlay with Gradient */
            body::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg,
                        rgba(71, 192, 62, 0.85) 0%,
                        rgba(52, 91, 50, 0.75) 25%,
                        rgba(146, 237, 135, 0.48) 50%,
                        rgba(103, 144, 100, 0.46) 75%,
                        rgba(58, 140, 52, 0.9) 100%);
                z-index: -1;
                backdrop-filter: blur(2px);
            }

            /* Animated Background Elements */
            .bg-elements {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: -1;
                overflow: hidden;
            }

            .bg-circle {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.05);
                animation: float 15s infinite linear;
            }

            .bg-circle:nth-child(1) {
                width: 300px;
                height: 300px;
                top: -150px;
                right: -150px;
                animation-delay: 0s;
            }

            .bg-circle:nth-child(2) {
                width: 200px;
                height: 200px;
                bottom: -100px;
                left: -100px;
                animation-delay: -5s;
            }

            .bg-circle:nth-child(3) {
                width: 150px;
                height: 150px;
                top: 50%;
                right: 20%;
                animation-delay: -10s;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0) rotate(0deg);
                }

                33% {
                    transform: translateY(-20px) rotate(120deg);
                }

                66% {
                    transform: translateY(20px) rotate(240deg);
                }
            }

            .activation-container {
                width: 100%;
                max-width: 480px;
                margin: 0 auto;
                animation: slideUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .activation-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: var(--border-radius);
                box-shadow: var(--shadow);
                padding: 40px;
                width: 100%;
                border: 1px solid rgba(255, 255, 255, 0.2);
                position: relative;
                overflow: hidden;
            }

            .activation-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 5px;
                background: linear-gradient(90deg, var(--primary), var(--success-light));
            }

            .card-header {
                text-align: center;
                margin-bottom: 35px;
                position: relative;
            }

            .card-header::after {
                content: '';
                position: absolute;
                bottom: -15px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, var(--primary), transparent);
                border-radius: 3px;
            }

            .logo-container {
                margin-bottom: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 80px;
            }

            .logo {
                height: 70px;
                max-width: 100%;
                object-fit: contain;
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
                transition: var(--transition);
            }

            .logo:hover {
                transform: scale(1.05);
            }

            .card-header h2 {
                color: var(--dark);
                margin-bottom: 8px;
                font-size: 28px;
                font-weight: 700;
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .card-header p {
                color: var(--gray);
                font-size: 15px;
                line-height: 1.5;
            }

            .form-group {
                margin-bottom: 25px;
                position: relative;
            }

            label {
                display: block;
                margin-bottom: 10px;
                font-weight: 500;
                color: var(--dark-light);
                font-size: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            label i {
                color: var(--primary);
                width: 20px;
                text-align: center;
            }

            .input-container {
                position: relative;
            }

            input {
                width: 100%;
                padding: 15px 45px 15px 15px;
                border: 2px solid #e9ecef;
                border-radius: 8px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                transition: var(--transition);
                background: var(--white);
                color: var(--dark);
            }

            input:focus {
                border-color: var(--primary);
                outline: none;
                box-shadow: 0 0 0 3px rgba(8, 92, 2, 0.1);
                transform: translateY(-2px);
            }

            input:hover:not(:focus) {
                border-color: var(--gray-light);
            }

            .input-container::after {
                content: '';
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--gray-light);
                font-size: 14px;
            }

            .code-input {
                font-family: 'Courier New', monospace;
                font-size: 18px;
                font-weight: 600;
                letter-spacing: 8px;
                text-align: center;
                text-transform: uppercase;
                padding-right: 15px;
            }

            .input-hint {
                color: var(--gray);
                font-size: 13px;
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 5px;
                opacity: 0.8;
            }

            .input-hint i {
                color: var(--primary);
            }

            .btn {
                width: 100%;
                padding: 16px 24px;
                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                color: var(--white);
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 10px;
                font-family: 'Poppins', sans-serif;
                transition: var(--transition);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
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
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: 0.5s;
            }

            .btn:hover:not(:disabled) {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(8, 92, 2, 0.2);
            }

            .btn:hover:not(:disabled)::before {
                left: 100%;
            }

            .btn:active:not(:disabled) {
                transform: translateY(-1px);
            }

            .btn:disabled {
                background: var(--gray-light);
                cursor: not-allowed;
                opacity: 0.7;
            }

            .btn-secondary {
                background: linear-gradient(135deg, var(--gray), var(--dark-light));
            }

            .btn-warning {
                background: linear-gradient(135deg, var(--warning), var(--warning-light));
                color: var(--dark);
            }

            .btn-small {
                padding: 10px 20px;
                font-size: 14px;
                width: auto;
                margin: 0;
            }

            .message {
                padding: 16px 20px;
                border-radius: 8px;
                margin-bottom: 25px;
                font-size: 15px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                animation: fadeIn 0.4s ease-out;
                border-left: 4px solid transparent;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-15px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .message i {
                font-size: 18px;
                margin-top: 2px;
                flex-shrink: 0;
            }

            .success {
                background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
                color: #155724;
                border-left-color: var(--success);
            }

            .error {
                background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
                color: #721c24;
                border-left-color: var(--danger);
            }

            .info {
                background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
                color: #0c5460;
                border-left-color: var(--primary);
            }

            .status-info {
                background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
                padding: 16px 20px;
                border-radius: 8px;
                margin-bottom: 25px;
                font-size: 14px;
                display: none;
                animation: fadeIn 0.3s ease-in;
                border-left: 4px solid var(--primary);
            }

            .links {
                text-align: center;
                margin-top: 35px;
                padding-top: 25px;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 20px;
            }

            .links a {
                color: var(--primary);
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                transition: var(--transition);
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 6px;
                background: rgba(8, 92, 2, 0.05);
            }

            .links a:hover {
                color: var(--primary-dark);
                background: rgba(8, 92, 2, 0.1);
                transform: translateY(-2px);
                text-decoration: none;
            }

            .links a i {
                font-size: 14px;
            }

            .debug-section {
                margin-top: 30px;
                padding-top: 25px;
                border-top: 1px dashed rgba(0, 0, 0, 0.2);
            }

            .debug-section h4 {
                color: var(--dark);
                margin-bottom: 15px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 1px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .debug-actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .test-result {
                margin-top: 20px;
                padding: 20px;
                background: rgba(248, 249, 250, 0.8);
                border-radius: 8px;
                font-size: 13px;
                display: none;
                max-height: 250px;
                overflow-y: auto;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .activation-card {
                    padding: 30px 25px;
                }

                .card-header h2 {
                    font-size: 24px;
                }

                .logo-container {
                    height: 70px;
                }

                .logo {
                    height: 60px;
                }

                .links {
                    flex-direction: column;
                    gap: 12px;
                }

                .debug-actions {
                    flex-direction: column;
                }

                .btn-small {
                    width: 100%;
                }

                .code-input {
                    letter-spacing: 6px;
                    font-size: 16px;
                }
            }

            @media (max-width: 480px) {
                body {
                    padding: 15px;
                }

                .activation-card {
                    padding: 25px 20px;
                }

                .card-header h2 {
                    font-size: 22px;
                }

                .logo {
                    height: 50px;
                }

                input {
                    padding: 14px 40px 14px 14px;
                }

                .code-input {
                    letter-spacing: 4px;
                }
            }

            /* Loading Animation */
            .loader {
                display: inline-block;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: var(--white);
                animation: spin 1s ease-in-out infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Pulse animation for important elements */
            .pulse {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% {
                    box-shadow: 0 0 0 0 rgba(8, 92, 2, 0.4);
                }

                70% {
                    box-shadow: 0 0 0 10px rgba(8, 92, 2, 0);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(8, 92, 2, 0);
                }
            }
        </style>
    </head>

    <body>
        <!-- Background Elements -->
        <div class="bg-elements">
            <div class="bg-circle"></div>
            <div class="bg-circle"></div>
            <div class="bg-circle"></div>
        </div>

        <div class="activation-container">
            <div class="activation-card">
                <div class="card-header">
                    <div class="logo-container">
                        <img src="Huduma_WhiteBox/Whitebox.png" alt="WhiteBox" class="logo"
                            onerror="this.style.display='none'">
                    </div>
                    <h2>Activate Your Account</h2>
                    <p>Enter the activation code sent to your email</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['activation_success'])): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <?php echo $_SESSION['activation_success']; ?>
                            <?php unset($_SESSION['activation_success']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['activation_error'])): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <?php echo $_SESSION['activation_error']; ?>
                            <?php unset($_SESSION['activation_error']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status Info (for AJAX) -->
                <div class="status-info" id="statusInfo"></div>

                <!-- Activation Form -->
                <form method="POST" action="activate.php?action=activate" id="activationForm">
                    <input type="hidden" name="action" value="activate">

                    <div class="form-group">
                        <label for="email_input">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-container">
                            <input type="email" id="email_input" name="email_input" required
                                placeholder="your.email@example.com" value="<?php
                                if (isset($_SESSION['activation_data']['email'])) {
                                    echo htmlspecialchars(base64_decode($_SESSION['activation_data']['email']));
                                    unset($_SESSION['activation_data']);
                                }
                                ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="code">
                            <i class="fas fa-key"></i> Activation Code
                        </label>
                        <div class="input-container">
                            <input type="text" id="code" name="code" class="code-input" required maxlength="8"
                                pattern="[A-Z0-9]{8}" placeholder="A1B2C3D4" value="<?php
                                if (isset($_SESSION['activation_data']['code'])) {
                                    echo htmlspecialchars($_SESSION['activation_data']['code']);
                                }
                                ?>">
                        </div>
                        <div class="input-hint">
                            <i class="fas fa-info-circle"></i> Enter the 8-character code from your email
                        </div>
                    </div>

                    <button type="submit" class="btn pulse" id="activateBtn">
                        <i class="fas fa-check-circle"></i> <span>Activate Account</span>
                    </button>

                    <!-- <?php if ($show_test_button): ?>
                        <div class="debug-section">
                            <h4><i class="fas fa-tools"></i> Diagnostics</h4>
                            <div class="debug-actions">
                                <button type="button" class="btn btn-warning btn-small" onclick="testMailSystem()">
                                    <i class="fas fa-paper-plane"></i> Test Mail System
                                </button>
                                <button type="button" class="btn btn-secondary btn-small" onclick="checkLogs()">
                                    <i class="fas fa-file-alt"></i> Check Logs
                                </button>
                                <button type="button" class="btn btn-secondary btn-small" onclick="clearForm()">
                                    <i class="fas fa-broom"></i> Clear Form
                                </button>
                            </div>
                            <div class="test-result" id="testResult"></div>
                        </div>
                    <?php endif; ?> -->
                </form>

                <div class="links">
                    <a href="javascript:void(0)" onclick="checkStatus()" id="checkStatusLink">
                        <i class="fas fa-question-circle"></i> Check Status
                    </a>
                    <a href="javascript:void(0)" onclick="resendCode()" id="resendLink">
                        <i class="fas fa-redo"></i> Resend Code
                    </a>
                    <a href="index1.php">
                        <i class="fas fa-sign-in-alt"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                console.log('Activation form loaded');

                // Format activation code input
                $('#code').on('input', function () {
                    let value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase();
                    this.value = value.substring(0, 8);

                    // Add spaces for readability (optional)
                    // if (value.length > 4) {
                    //     this.value = value.substring(0, 4) + ' ' + value.substring(4, 8);
                    // }
                });

                // Auto-uppercase on blur
                $('#code').on('blur', function () {
                    this.value = this.value.toUpperCase();
                });

                // Check if URL has activation parameters
                const urlParams = new URLSearchParams(window.location.search);
                const urlCode = urlParams.get('code');
                const urlEmail = urlParams.get('email');

                if (urlCode && urlEmail) {
                    console.log('URL parameters detected:', { code: urlCode, email: urlEmail });
                    $('#code').val(urlCode);
                    $('#email_input').val(atob(urlEmail));
                    $('#activateBtn').click();
                }

                // Form submission
                $('#activationForm').submit(function (e) {
                    const code = $('#code').val().replace(/\s/g, '');
                    const email = $('#email_input').val().trim();

                    if (code.length !== 8) {
                        showMessage('Please enter a valid 8-character code', 'error');
                        e.preventDefault();
                        return false;
                    }

                    if (!isValidEmail(email)) {
                        showMessage('Please enter a valid email address', 'error');
                        e.preventDefault();
                        return false;
                    }

                    // Update form values
                    $('#code').val(code);

                    console.log('Submitting form:', { code: code, email: email });

                    $('#activateBtn').html('<div class="loader"></div> <span>Activating...</span>').prop('disabled', true);
                    $('#activateBtn').removeClass('pulse');
                    $('#checkStatusLink, #resendLink').css('opacity', '0.5').css('pointer-events', 'none');

                    return true;
                });
            });

            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function showMessage(message, type = 'info') {
                const statusDiv = $('#statusInfo');
                const icon = type === 'error' ? 'fas fa-exclamation-circle' :
                    type === 'success' ? 'fas fa-check-circle' : 'fas fa-info-circle';

                statusDiv.html(`<i class="${icon}"></i> ${message}`)
                    .removeClass('success error info')
                    .addClass(type)
                    .show();

                // Auto-hide info messages after 5 seconds
                if (type === 'info') {
                    setTimeout(() => {
                        statusDiv.fadeOut(500);
                    }, 5000);
                }

                // Scroll to message
                statusDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            function checkStatus() {
                const email = $('#email_input').val().trim();

                if (!email) {
                    showMessage('Please enter your email first', 'error');
                    $('#email_input').focus();
                    return;
                }

                if (!isValidEmail(email)) {
                    showMessage('Please enter a valid email address', 'error');
                    $('#email_input').focus();
                    return;
                }

                showMessage('Checking activation status...', 'info');

                $.post('activate.php?action=check', { email: btoa(email) })
                    .done(function (response) {
                        console.log('Status check response:', response);
                        try {
                            const data = JSON.parse(response);
                            let message = '';

                            switch (data.status) {
                                case 'activated':
                                    message = '✅ Account is already activated';
                                    break;
                                case 'pending':
                                    message = `⏳ Activation pending (${data.hours_left} hours left)`;
                                    break;
                                case 'expired':
                                    message = '⌛ Activation code expired';
                                    break;
                                case 'no_token':
                                    message = '🔑 No activation code found';
                                    break;
                                case 'not_found':
                                    message = '🔍 Account not found';
                                    break;
                                default:
                                    message = data.message || 'Unknown status';
                            }

                            showMessage(message,
                                data.status === 'activated' ? 'success' :
                                    data.status === 'pending' ? 'info' : 'error');
                        } catch (e) {
                            console.error('Parse error:', e);
                            showMessage('Invalid response from server', 'error');
                        }
                    })
                    .fail(function (xhr, status, error) {
                        console.error('Status check failed:', error);
                        showMessage('Error checking status. Please try again.', 'error');
                    });
            }

            function resendCode() {
                const email = $('#email_input').val().trim();

                if (!email) {
                    showMessage('Please enter your email first', 'error');
                    $('#email_input').focus();
                    return;
                }

                if (!isValidEmail(email)) {
                    showMessage('Please enter a valid email address', 'error');
                    $('#email_input').focus();
                    return;
                }

                if (!confirm(`Send new activation code to ${email}?`)) {
                    return;
                }

                showMessage('Sending new activation code...', 'info');
                const resendBtn = $('#resendLink');
                const originalHtml = resendBtn.html();
                resendBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');

                $.post('activate.php?action=resend', { email: btoa(email) })
                    .done(function (response) {
                        console.log('Resend response:', response);
                        try {
                            const data = JSON.parse(response);

                            if (data.status === 'success') {
                                showMessage(`✅ New code sent! Please check your email.`, 'success');
                                if (data.code) {
                                    $('#code').val(data.code).focus().addClass('pulse');
                                    setTimeout(() => $('#code').removeClass('pulse'), 2000);
                                }
                            } else {
                                showMessage(data.message || 'Failed to send code', 'error');
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                            showMessage('Invalid response from server', 'error');
                        }
                    })
                    .fail(function (xhr, status, error) {
                        console.error('Resend failed:', error);
                        showMessage('Error sending code. Please try again.', 'error');
                    })
                    .always(function () {
                        resendBtn.html(originalHtml);
                    });
            }

            function testMailSystem() {
                const email = $('#email_input').val().trim() || 'test@example.com';

                if (email && !isValidEmail(email)) {
                    showMessage('Please enter a valid email for testing', 'error');
                    return;
                }

                showMessage('Testing mail system...', 'info');
                const resultDiv = $('#testResult');
                resultDiv.html('<p><i class="fas fa-spinner fa-spin"></i> Testing mail system...</p>').show();

                $.post('test_mail.php', {
                    test_email: email,
                    test_type: 'activation'
                })
                    .done(function (response) {
                        resultDiv.html(response);
                        console.log('Mail test result:', response);
                    })
                    .fail(function (xhr, status, error) {
                        resultDiv.html(`<div class="error"><i class="fas fa-exclamation-circle"></i> Test failed: ${error}</div>`);
                        console.error('Mail test failed:', error);
                    });
            }

            function checkLogs() {
                const resultDiv = $('#testResult');
                resultDiv.html('<p><i class="fas fa-spinner fa-spin"></i> Loading logs...</p>').show();

                $.get('view_logs.php')
                    .done(function (response) {
                        resultDiv.html(`<pre style="font-size: 11px; line-height: 1.3;">${response}</pre>`);
                    })
                    .fail(function () {
                        resultDiv.html('<div class="error"><i class="fas fa-exclamation-circle"></i> Failed to load logs</div>');
                    });
            }

            function clearForm() {
                if (confirm('Clear all form fields?')) {
                    $('#email_input').val('');
                    $('#code').val('');
                    $('#email_input').focus();
                    showMessage('Form cleared', 'info');
                }
            }
        </script>
    </body>

    </html>
    <?php
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? '';

debug_log("Processing action: $action");

switch ($action) {
    case 'activate':
        handleActivation();
        break;
    case 'check':
        checkActivationStatus();
        break;
    case 'resend':
        resendActivationCode();
        break;
    case 'verify':
        verifyActivationCode();
        break;
    default:
        showActivationForm();
        break;
}

// Close database connection
if (isset($con) && $con) {
    mysqli_close($con);
    debug_log("Database connection closed");
}

debug_log("=== ACTIVATION SCRIPT ENDED ===");
?>
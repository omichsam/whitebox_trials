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
                        <a href='$activation_link' style='display: inline-block; background: #085c02; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 0;'>
                            Activate Account Now
                        </a>
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">

        <style>
            :root {
                --primary: #4361ee;
                --success: #058d27ca;
                --danger: #a91616ff;
                --dark: #212529;
                --gray: #6c757d;
                --warning: #ffc107;
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
            }

            .activation-container {
                width: 100%;
                max-width: 500px;
                margin: 0 auto;
            }

            .activation-card {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                padding: 30px;
                width: 100%;
            }

            .card-header {
                text-align: center;
                margin-bottom: 25px;
            }

            .card-header h2 {
                color: var(--dark);
                margin-bottom: 10px;
                font-size: 24px;
            }

            .logo {
                height: 60px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: var(--dark);
                font-size: 14px;
            }

            input {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
                transition: border-color 0.3s;
            }

            input:focus {
                border-color: var(--primary);
                outline: none;
                box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            }

            .code-input {
                font-family: 'Courier New', monospace;
                font-size: 16px;
                letter-spacing: 3px;
                text-align: ccenter;
                text-transform: uuppercase;
            }

            .btn {
                width: 100%;
                padding: 14px;
                background: var(--success);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 10px;
                font-family: 'Poppins', sans-serif;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .btn:hover:not(:disabled) {
                opacity: 0.9;
                transform: translateY(-2px);
            }

            .btn:disabled {
                background: var(--gray);
                cursor: not-allowed;
                opacity: 0.6;
            }

            .btn-secondary {
                background: var(--gray);
                margin-top: 5px;
            }

            .btn-warning {
                background: var(--warning);
                color: var(--dark);
                margin-top: 5px;
            }

            .btn-small {
                padding: 8px 15px;
                font-size: 14px;
                width: auto;
                margin: 0;
            }

            .message {
                padding: 12px 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                display: flex;
                align-items: center;
                animation: fadeIn 0.3s ease-in;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .message i {
                margin-right: 10px;
                font-size: 16px;
            }

            .success {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            .info {
                background: #e7f3ff;
                color: #0c5460;
                border: 1px solid #b8daff;
            }

            .links {
                text-align: center;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 15px;
            }

            .links a {
                color: var(--primary);
                text-decoration: none;
                font-size: 14px;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .links a:hover {
                color: var(--dark);
                text-decoration: underline;
            }

            .status-info {
                background: #e7f3ff;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                display: none;
                animation: fadeIn 0.3s ease-in;
            }

            .debug-section {
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px dashed #ddd;
            }

            .debug-section h4 {
                color: var(--dark);
                margin-bottom: 10px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .debug-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .test-result {
                margin-top: 15px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                font-size: 12px;
                display: none;
                max-height: 200px;
                overflow-y: auto;
            }

            @media (max-width: 480px) {
                .activation-card {
                    padding: 20px;
                }

                .card-header h2 {
                    font-size: 20px;
                }

                .logo {
                    height: 50px;
                }

                .links {
                    flex-direction: column;
                    gap: 10px;
                }

                .debug-actions {
                    flex-direction: column;
                }

                .btn-small {
                    width: 100%;
                }
            }
        </style>
    </head>

    <body>
        <div class="activation-container">
            <div class="activation-card">
                <div class="card-header">
                    <img src="Huduma_WhiteBox/Whitebox.png" alt="WhiteBox" class="logo" onerror="this.style.display='none'">
                    <h2>Activate Your Account</h2>
                    <p style="color: var(--gray); font-size: 14px;">Enter your activation code</p>
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
                        <input type="email" id="email_input" name="email_input" required
                            placeholder="your.email@example.com" value="<?php
                            if (isset($_SESSION['activation_data']['email'])) {
                                echo htmlspecialchars(base64_decode($_SESSION['activation_data']['email']));
                                unset($_SESSION['activation_data']);
                            }
                            ?>">
                    </div>

                    <div class="form-group">
                        <label for="code">
                            <i class="fas fa-key"></i> Activation Code
                        </label>
                        <input type="text" id="code" name="code" class="code-input" required maxlength="8"
                            pattern="[A-Z0-9]{8}" placeholder="Enter 8-character code" value="<?php
                            if (isset($_SESSION['activation_data']['code'])) {
                                echo htmlspecialchars($_SESSION['activation_data']['code']);
                            }
                            ?>">
                        <small style="color: var(--gray); font-size: 12px; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Enter the 8-character code from your email
                        </small>
                    </div>

                    <button type="submit" class="btn" id="activateBtn">
                        <i class="fas fa-check-circle"></i> <span>Activate Account</span>
                    </button>

                    <!-- < ?php if ($show_test_button): ?>
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
                    < ?php endif; ?> -->
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

                    $('#activateBtn').html('<i class="fas fa-spinner fa-spin"></i> <span>Activating...</span>').prop('disabled', true);
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
                        statusDiv.fadeOut();
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
                                    message = 'Account is already activated';
                                    break;
                                case 'pending':
                                    message = `Activation pending (${data.hours_left} hours left)`;
                                    break;
                                case 'expired':
                                    message = 'Activation code expired';
                                    break;
                                case 'no_token':
                                    message = 'No activation code found';
                                    break;
                                case 'not_found':
                                    message = 'Account not found';
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

                $.post('activate.php?action=resend', { email: btoa(email) })
                    .done(function (response) {
                        console.log('Resend response:', response);
                        try {
                            const data = JSON.parse(response);

                            if (data.status === 'success') {
                                showMessage(`New code sent! Please check your email.`, 'success');
                                if (data.code) {
                                    $('#code').val(data.code).focus();
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
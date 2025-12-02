<?php
session_start();
include("connect.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$con) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Generate 8-character code
function generateCode($length = 8)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// DEBUG: Log function for tracing
function debug_log($message, $data = null)
{
    $log_file = 'activation_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message";

    if ($data !== null) {
        $log_message .= " | Data: " . (is_array($data) ? json_encode($data) : $data);
    }

    $log_message .= PHP_EOL;
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Enhanced sendActivationEmail function
function sendActivationEmail($email, $first_name, $last_name, $activation_code)
{
    global $con;

    debug_log("Attempting to send activation email to: $email", [
        'first_name' => $first_name,
        'activation_code' => $activation_code
    ]);

    $activation_link = "http://whitebox.go.ke/activate.php?action=activate&code=" .
        urlencode($activation_code) . "&email=" . urlencode(base64_encode($email));

    $expiry_time = date('l, F j, Y \a\t g:i A', strtotime('+24 hours'));

    $subject = "Activate Your WhiteBox Account";

    $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Account Activation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #085c02ff 0%, #861616a0 100%); color: white; padding: 30px; text-align: center; }
                .content { background: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .activation-box { background: #f8f9fa; border: 2px solid #085c02; padding: 25px; margin: 25px 0; text-align: center; border-radius: 10px; }
                .activation-code { font-family: 'Courier New', monospace; font-size: 32px; font-weight: bold; letter-spacing: 3px; color: #085c02; margin: 15px 0; }
                .btn { display: inline-block; background: #085c02; color: white; padding: 14px 30px; text-decoration: none; 
                       border-radius: 6px; font-weight: bold; font-size: 16px; margin: 15px 0; }
                .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>WhiteBox Account Activation</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>$first_name $last_name</strong>,</p>
                <p>Welcome to WhiteBox! Please activate your account to get started.</p>
                
                <div class='activation-box'>
                    <h3>Your Activation Code</h3>
                    <div class='activation-code'>$activation_code</div>
                    <p>Enter this 8-character code on the activation page</p>
                    <a href='$activation_link' class='btn'>Activate Account Now</a>
                </div>
                
                <div class='warning'>
                    <p><strong>Important:</strong> This code expires on $expiry_time</p>
                </div>
                
                <p>If you didn't create this account, please ignore this email.</p>
                
                <p>Best regards,<br><strong>The WhiteBox Team</strong></p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " WhiteBox. All rights reserved.</p>
            </div>
        </body>
        </html>
    ";

    // Try custom mail system first
    $mail_sent = false;
    $mail_method = 'unknown';
    $mail_file = "Huduma_WhiteBox/mails/general.php";

    if (file_exists($mail_file)) {
        debug_log("Custom mail file found: $mail_file");

        // Clear any existing output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Start output buffering
        ob_start();

        // Create variables expected by your mail system
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $email;

        // Make database connection available globally
        $GLOBALS['con'] = $GLOBALS['con'] ?? null;

        // Include the mail file
        try {
            include($mail_file);
            $mail_output = ob_get_clean();

            debug_log("Mail file included successfully. Output length: " . strlen($mail_output));

            if (strlen($mail_output) > 0) {
                debug_log("Mail system output (first 500 chars): " . substr($mail_output, 0, 500));
            }

            // Check for success indicators
            $success_indicators = [
                'Email sent successfully',
                'success',
                'Message has been sent',
                'mail sent',
                'sent successfully',
                'successfully sent'
            ];

            $error_indicators = [
                'Error',
                'Failed',
                'not sent',
                'could not',
                'unable to',
                'failed to send',
                'sending failed'
            ];

            $mail_sent = false;
            $found_success = false;
            $found_error = false;

            // Check for success indicators
            foreach ($success_indicators as $indicator) {
                if (stripos($mail_output, $indicator) !== false) {
                    debug_log("Success indicator found: '$indicator'");
                    $found_success = true;
                    break;
                }
            }

            // Check for error indicators
            foreach ($error_indicators as $indicator) {
                if (stripos($mail_output, $indicator) !== false) {
                    debug_log("Error indicator found: '$indicator'");
                    $found_error = true;
                    break;
                }
            }

            // Determine result
            if ($found_success && !$found_error) {
                $mail_sent = true;
                $mail_method = 'custom_system';
                debug_log("Custom mail system SUCCESS");
            } elseif ($found_error) {
                $mail_sent = false;
                debug_log("Custom mail system FAILED (error indicator found)");
            } elseif (empty($mail_output)) {
                $mail_sent = true;
                $mail_method = 'custom_system';
                debug_log("Custom mail system SUCCESS (no output)");
            } else {
                // If we have output but no clear indicators, assume success
                $mail_sent = true;
                $mail_method = 'custom_system';
                debug_log("Custom mail system assumed SUCCESS (unclear output)");
            }

        } catch (Exception $e) {
            $mail_output = ob_get_clean();
            debug_log("Exception in mail file: " . $e->getMessage());
            debug_log("Output during exception: " . $mail_output);
            $mail_sent = false;
        }
    } else {
        debug_log("Custom mail file NOT found: $mail_file");
    }

    // If custom mail system failed or doesn't exist, fall back to PHP mail()
    if (!$mail_sent) {
        debug_log("Attempting PHP mail() fallback for: $email");

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: WhiteBox <noreply@whitebox.go.ke>" . "\r\n";
        $headers .= "Reply-To: support@whitebox.go.ke" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Test if mail() function works
        if (function_exists('mail')) {
            $result = mail($email, $subject, $message, $headers);

            if ($result) {
                debug_log("PHP mail() succeeded for: $email");
                $mail_sent = true;
                $mail_method = 'php_mail';
            } else {
                debug_log("PHP mail() FAILED for: $email");
                $mail_sent = false;

                // Get last error
                $error = error_get_last();
                if ($error) {
                    debug_log("Last error", $error);
                }
            }
        } else {
            debug_log("PHP mail() function does not exist!");
            $mail_sent = false;
        }
    }

    debug_log("Final email sending result for $email: " .
        ($mail_sent ? 'SUCCESS' : 'FAILED') .
        " (Method: " . ($mail_method ?: 'none') . ")");

    return $mail_sent ? $mail_method : false;
}

// Function to handle activation
function handleActivation()
{
    global $con;

    debug_log("Handle activation called", $_REQUEST);

    // Get activation data from GET or POST
    $code = $_GET['code'] ?? $_POST['code'] ?? '';
    $email_encoded = $_GET['email'] ?? $_POST['email'] ?? '';
    $email_input = $_POST['email_input'] ?? '';

    // If we have direct email input, encode it
    if (!empty($email_input) && empty($email_encoded)) {
        $email_encoded = base64_encode($email_input);
    }

    // Validate inputs
    if (empty($code) || empty($email_encoded)) {
        $_SESSION['activation_error'] = "Please provide both email and activation code.";
        debug_log("Missing parameters", ['code' => $code, 'email_encoded' => $email_encoded]);
        header("Location: activate.php");
        exit();
    }

    // Decode email
    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));
    $code = mysqli_real_escape_string($con, trim($code));

    debug_log("Processing activation", ['email' => $email, 'code' => $code]);

    // Check activation
    $result = checkAndProcessActivation($email, $code);

    debug_log("Activation result", $result);

    // Redirect based on result
    if ($result['status'] === 'success') {
        $_SESSION['activation_success'] = $result['message'];
        header("Location: index1.php?activated=true");
    } else {
        $_SESSION['activation_error'] = $result['message'];
        $_SESSION['activation_data'] = ['email' => $email_encoded, 'code' => $code];
        header("Location: activate.php");
    }
    exit();
}

// Function to check and process activation
function checkAndProcessActivation($email, $code)
{
    global $con;

    debug_log("Checking activation for email: $email, code: $code");

    // Check if token is valid and not expired
    $check_query = "SELECT * FROM users WHERE email='$email' 
                    AND token='$code' 
                    AND token_type='activation' 
                    AND token_expires_at > NOW()";

    $result = mysqli_query($con, $check_query);

    if (!$result) {
        $error = mysqli_error($con);
        debug_log("Database query error", $error);
        return ['status' => 'error', 'message' => 'Database error. Please try again.'];
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $user_id = $user['id'];

        debug_log("Valid activation found for user", ['user_id' => $user_id, 'name' => "$first_name $last_name"]);

        // Activation successful - update user
        $update_query = "UPDATE users SET 
                         token = NULL,
                         token_type = NULL,
                         token_expires_at = NULL,
                         country = 'KE',
                         user_state = 'active',
                         updated_at = NOW()
                         WHERE email='$email' AND id='$user_id'";

        if (mysqli_query($con, $update_query)) {
            debug_log("User activated successfully", ['user_id' => $user_id]);

            // Send welcome email
            $welcome_sent = sendWelcomeEmail($email, $first_name, $last_name);

            if ($welcome_sent) {
                debug_log("Welcome email sent successfully");
            } else {
                debug_log("Failed to send welcome email");
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

    debug_log("Getting activation failure reason for email: $email, code: $code");

    // Check if account is already activated
    $check_active = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND country='KE'");

    if (mysqli_num_rows($check_active) > 0) {
        debug_log("Account already activated: $email");
        return ['status' => 'error', 'message' => 'This account is already activated.'];
    }

    // Check if token exists but expired
    $check_expired = mysqli_query($con, "SELECT * FROM users WHERE email='$email' AND token='$code' 
                                        AND token_type='activation' AND token_expires_at <= NOW()");

    if (mysqli_num_rows($check_expired) > 0) {
        debug_log("Activation code expired: $email");
        return ['status' => 'error', 'message' => 'Activation code has expired.'];
    }

    // Check if email exists but code is wrong
    $check_email = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check_email) > 0) {
        debug_log("Invalid activation code for email: $email");
        return ['status' => 'error', 'message' => 'Invalid activation code.'];
    }

    debug_log("No account found with email: $email");
    return ['status' => 'error', 'message' => 'No account found with this email.'];
}

// Function to check activation status (AJAX)
function checkActivationStatus()
{
    global $con;

    $email_encoded = $_POST['email'] ?? '';

    debug_log("Check activation status", ['email_encoded' => $email_encoded]);

    if (empty($email_encoded)) {
        echo json_encode(['status' => 'error', 'message' => 'Email required']);
        exit();
    }

    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));

    debug_log("Checking status for email: $email");

    $query = "SELECT id, first_name, country, token, token_expires_at FROM users WHERE email='$email'";
    $result = mysqli_query($con, $query);

    if (!$result) {
        $error = mysqli_error($con);
        debug_log("Check status query failed", $error);
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
        exit();
    }

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        debug_log("User found", ['user_id' => $user['id'], 'country' => $user['country']]);

        if ($user['country'] === 'KE') {
            echo json_encode(['status' => 'activated', 'message' => 'Account is already activated']);
        } elseif (!empty($user['token'])) {
            $expiry = strtotime($user['token_expires_at']);
            $now = time();
            $hours_left = round(($expiry - $now) / 3600, 1);

            debug_log("Token found", [
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
        debug_log("Account not found: $email");
        echo json_encode(['status' => 'not_found', 'message' => 'Account not found']);
    }
    exit();
}

// Function to resend activation code
function resendActivationCode()
{
    global $con;

    $email_encoded = $_POST['email'] ?? '';

    debug_log("Resend activation code requested", ['email_encoded' => $email_encoded]);

    if (empty($email_encoded)) {
        echo json_encode(['status' => 'error', 'message' => 'Email required']);
        exit();
    }

    $email = base64_decode($email_encoded);
    $email = mysqli_real_escape_string($con, trim($email));

    debug_log("Resend for email: $email");

    // Check if user exists
    $checkUser = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if (!$checkUser) {
        $error = mysqli_error($con);
        debug_log("Check user query failed", $error);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $error]);
        exit();
    }

    if (mysqli_num_rows($checkUser) === 0) {
        debug_log("Account not found for resend: $email");
        echo json_encode(['status' => 'error', 'message' => 'Account not found']);
        exit();
    }

    $user = mysqli_fetch_assoc($checkUser);
    $first_name = $user['first_name'];
    $last_name = $user['last_name'];

    debug_log("User found for resend", ['name' => "$first_name $last_name"]);

    // Check if already activated
    if ($user['country'] === 'KE') {
        debug_log("Account already activated: $email");
        echo json_encode(['status' => 'activated', 'message' => 'Account is already activated']);
        exit();
    }

    // Generate new activation code
    $activation_code = generateCode(8);
    $token_type = 'activation';
    $current_time = date('Y-m-d H:i:s');
    $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

    debug_log("Generated new code", [
        'email' => $email,
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
        debug_log("Database updated with new token");

        // Send activation email using the updated function
        $email_result = sendActivationEmail($email, $first_name, $last_name, $activation_code);

        if ($email_result) {
            debug_log("Activation email sent successfully via: $email_result");
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

    debug_log("Verify activation code", ['code' => $code, 'email_encoded' => $email_encoded]);

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
    global $con;

    debug_log("Sending welcome email to: $email");

    $subject = "Welcome to WhiteBox - Account Activated";

    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #085c02; color: white; padding: 25px; text-align: center;'>
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

    // Try using your custom mail system first
    $mail_sent = false;
    $mail_method = 'unknown';
    $mail_file = "Huduma_WhiteBox/mails/general.php";

    if (file_exists($mail_file)) {
        debug_log("Using custom mail system for welcome email");

        // Clear output buffers
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_start();

        // Create variables that your mail system expects
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $email;

        // Make database connection available globally
        $GLOBALS['con'] = $GLOBALS['con'] ?? null;

        try {
            include($mail_file);
            $mail_output = ob_get_clean();

            debug_log("Welcome mail output length: " . strlen($mail_output));

            // Simple check for success
            if (empty($mail_output) || stripos($mail_output, 'success') !== false) {
                $mail_sent = true;
                $mail_method = 'custom_system';
                debug_log("Welcome email sent via custom system");
            } else {
                debug_log("Custom system may have failed for welcome email");
            }
        } catch (Exception $e) {
            $mail_output = ob_get_clean();
            debug_log("Exception in welcome mail: " . $e->getMessage());
        }
    }

    // Fallback to PHP mail()
    if (!$mail_sent) {
        debug_log("Falling back to PHP mail() for welcome email");

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: WhiteBox <noreply@whitebox.go.ke>" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (function_exists('mail')) {
            if (mail($email, $subject, $message, $headers)) {
                $mail_sent = true;
                $mail_method = 'php_mail';
                debug_log("Welcome email sent via PHP mail()");
            } else {
                debug_log("PHP mail() failed for welcome email");
            }
        }
    }

    debug_log("Welcome email result: " . ($mail_sent ? "SUCCESS via $mail_method" : "FAILED"));
    return $mail_sent ? $mail_method : false;
}

// Function to show activation form (default view)
function showActivationForm()
{
    global $con;

    debug_log("Showing activation form");

    // Create diagnostic button for admins
    $is_admin = false; // Add your admin check logic here

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
                padding: 40px;
                width: 100%;
            }

            .card-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .card-header h2 {
                color: var(--dark);
                margin-bottom: 10px;
            }

            .logo {
                height: 80px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
                color: var(--dark);
            }

            input {
                width: 100%;
                padding: 12px 15px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                font-family: 'Poppins', sans-serif;
            }

            input:focus {
                border-color: var(--primary);
                outline: none;
            }

            .code-input {
                font-family: 'Courier New', monospace;
                font-size: 20px;
                letter-spacing: 3px;
                text-align: center;
                text-transform: uppercase;
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
                transition: opacity 0.3s;
            }

            .btn:hover {
                opacity: 0.9;
            }

            .btn:disabled {
                background: var(--gray);
                cursor: not-allowed;
                opacity: 0.6;
            }

            .btn-warning {
                background: var(--warning);
                color: var(--dark);
            }

            .message {
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                display: flex;
                align-items: center;
            }

            .message i {
                margin-right: 10px;
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
            }

            .links a {
                color: var(--primary);
                text-decoration: none;
                margin: 0 10px;
                font-size: 14px;
                transition: color 0.3s;
            }

            .links a:hover {
                text-decoration: underline;
                color: var(--dark);
            }

            .status-info {
                background: #e7f3ff;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                display: none;
            }

            .debug-info {
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                font-size: 12px;
                color: var(--gray);
                border: 1px dashed #ddd;
            }

            .debug-info h4 {
                margin-bottom: 10px;
                color: var(--dark);
            }

            @media (max-width: 480px) {
                .activation-card {
                    padding: 20px;
                }

                .links a {
                    display: block;
                    margin: 5px 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="activation-container">
            <div class="activation-card">
                <div class="card-header">
                    <img src="Huduma_WhiteBox/Whitebox.png" alt="WhiteBox" class="logo">
                    <h2>Activate Your Account</h2>
                    <p style="color: var(--gray);">Enter your activation code</p>
                </div>

                <!-- Messages -->
                <?php if (isset($_SESSION['activation_success'])): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['activation_success']; ?>
                        <?php unset($_SESSION['activation_success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['activation_error'])): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $_SESSION['activation_error']; ?>
                        <?php unset($_SESSION['activation_error']); ?>
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
                            pattern="[A-Z0-9]{8}" placeholder="ENTER 8-CHAR CODE" value="<?php
                            if (isset($_SESSION['activation_data']['code'])) {
                                echo htmlspecialchars($_SESSION['activation_data']['code']);
                            }
                            ?>">
                        <small style="color: var(--gray); font-size: 12px; display: block; margin-top: 5px;">
                            <i class="fas fa-info-circle"></i> Enter the 8-character code from your email
                        </small>
                    </div>

                    <button type="submit" class="btn" id="activateBtn">
                        <i class="fas fa-check-circle"></i> Activate Account
                    </button>

                    <?php if ($is_admin): ?>
                        <button type="button" class="btn btn-warning" onclick="runDiagnostics()" style="margin-top: 10px;">
                            <i class="fas fa-stethoscope"></i> Run Diagnostics
                        </button>
                    <?php endif; ?>
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

                <?php if ($is_admin): ?>
                    <div class="debug-info" id="debugInfo" style="display: none;">
                        <h4><i class="fas fa-bug"></i> Debug Information</h4>
                        <div id="debugContent"></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                // Format activation code input
                $('#code').on('input', function () {
                    this.value = this.value.replace(/[^A-Z0-9]/gi, '').toUpperCase().substring(0, 8);
                });

                // Check if URL has activation parameters
                const urlParams = new URLSearchParams(window.location.search);
                const urlCode = urlParams.get('code');
                const urlEmail = urlParams.get('email');

                if (urlCode && urlEmail) {
                    $('#code').val(urlCode);
                    $('#email_input').val(atob(urlEmail));
                    $('#activateBtn').click();
                }

                // Form submission
                $('#activationForm').submit(function (e) {
                    const code = $('#code').val();
                    const email = $('#email_input').val();

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

                    $('#activateBtn').html('<i class="fas fa-spinner fa-spin"></i> Activating...').prop('disabled', true);
                    $('#checkStatusLink, #resendLink').css('opacity', '0.5').css('pointer-events', 'none');
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
            }

            function checkStatus() {
                const email = $('#email_input').val();

                if (!email) {
                    showMessage('Please enter your email first', 'error');
                    return;
                }

                if (!isValidEmail(email)) {
                    showMessage('Please enter a valid email address', 'error');
                    return;
                }

                showMessage('Checking status...', 'info');

                $.post('activate.php?action=check', { email: btoa(email) })
                    .done(function (response) {
                        try {
                            const data = JSON.parse(response);
                            let message = '';

                            switch (data.status) {
                                case 'activated':
                                    message = '<i class="fas fa-check-circle"></i> Account is already activated';
                                    break;
                                case 'pending':
                                    message = `<i class="fas fa-clock"></i> Activation pending (${data.hours_left} hours left)`;
                                    break;
                                case 'expired':
                                    message = '<i class="fas fa-exclamation-triangle"></i> Activation code expired';
                                    break;
                                case 'no_token':
                                    message = '<i class="fas fa-times-circle"></i> No activation code found';
                                    break;
                                case 'not_found':
                                    message = '<i class="fas fa-user-times"></i> Account not found';
                                    break;
                                default:
                                    message = `<i class="fas fa-exclamation-circle"></i> ${data.message}`;
                            }

                            showMessage(message,
                                data.status === 'activated' ? 'success' :
                                    data.status === 'pending' ? 'info' : 'error');
                        } catch (e) {
                            showMessage('Invalid response from server', 'error');
                        }
                    })
                    .fail(function () {
                        showMessage('Error checking status. Please try again.', 'error');
                    });
            }

            function resendCode() {
                const email = $('#email_input').val();

                if (!email) {
                    showMessage('Please enter your email first', 'error');
                    return;
                }

                if (!isValidEmail(email)) {
                    showMessage('Please enter a valid email address', 'error');
                    return;
                }

                if (!confirm(`Send new activation code to ${email}?`)) {
                    return;
                }

                showMessage('Sending new activation code...', 'info');

                $.post('activate.php?action=resend', { email: btoa(email) })
                    .done(function (response) {
                        try {
                            const data = JSON.parse(response);

                            if (data.status === 'success') {
                                showMessage(`New code sent! Please check your email. (Method: ${data.method || 'unknown'})`, 'success');
                            } else {
                                showMessage(data.message, 'error');
                            }
                        } catch (e) {
                            showMessage('Invalid response from server', 'error');
                        }
                    })
                    .fail(function () {
                        showMessage('Error sending code. Please try again.', 'error');
                    });
            }

            function runDiagnostics() {
                const email = $('#email_input').val() || 'test@example.com';

                showMessage('Running diagnostics...', 'info');
                $('#debugInfo').show();
                $('#debugContent').html('<i class="fas fa-spinner fa-spin"></i> Running tests...');

                $.post('diagnose_mail.php', { test_email: email, run_diagnostics: true })
                    .done(function (response) {
                        $('#debugContent').html(response);
                    })
                    .fail(function () {
                        $('#debugContent').html('<div class="error">Failed to run diagnostics</div>');
                    });
            }
        </script>
    </body>

    </html>
    <?php
}

// Handle different actions
$action = $_GET['action'] ?? $_POST['action'] ?? '';

debug_log("Activation script called", [
    'action' => $action,
    'method' => $_SERVER['REQUEST_METHOD'],
    'ip' => $_SERVER['REMOTE_ADDR'],
    'session_id' => session_id()
]);

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
?>
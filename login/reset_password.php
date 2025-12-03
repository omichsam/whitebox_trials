<?php
// login/reset_password.php - Simple reset password processor
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database connection
$connect_path = dirname(dirname(__FILE__)) . '/connect.php';
if (file_exists($connect_path)) {
    include($connect_path);
} else {
    die(base64_encode('db_error'));
}

if (!isset($con) || !$con) {
    die(base64_encode('db_error'));
}

// Set headers for clean response
header('Content-Type: text/plain');

// Function to generate reset code
function generateCode($length = 8) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// Function to hash password
function hashword($string, $salt) {
    return crypt($string, '$1$' . $salt . '$');
}

$SALT = "A073955@am";

// Get action
$action = $_POST['action'] ?? '';

if ($action === 'request') {
    // Step 1: Request reset code
    $email = base64_decode($_POST['email'] ?? '');
    
    if (empty($email)) {
        die(base64_encode('invalid_email'));
    }
    
    $email = strtolower(mysqli_real_escape_string($con, trim($email)));
    
    // Check if user exists
    $checkUser = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($checkUser) === 0) {
        // For security, don't reveal if email exists
        die(base64_encode('email_not_found'));
    }
    
    $user = mysqli_fetch_assoc($checkUser);
    
    // Check if account is activated (country = "KE")
    if ($user['country'] !== "KE") {
        die(base64_encode('not_activated'));
    }
    
    // Generate reset code
    $reset_code = generateCode(8);
    $current_time = date('Y-m-d H:i:s');
    $expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Update database
    mysqli_query($con, "UPDATE users SET 
        token = '$reset_code',
        token_type = 'reset',
        token_expires_at = '$expiry_time',
        updated_at = '$current_time'
        WHERE email = '$email'");
    
    // Prepare email
    $subject = "Password Reset Code - WhiteBox";
    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #085c02; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>Password Reset</h2>
            </div>
            <div style='padding: 25px; background: #f9f9f9;'>
                <p>Dear <strong>{$user['first_name']} {$user['last_name']}</strong>,</p>
                <p>Use this code to reset your password:</p>
                
                <div style='background: white; border: 2px solid #085c02; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                    <div style='font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #333; 
                             margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 6px;
                             font-family: monospace;'>
                        $reset_code
                    </div>
                    <p style='font-size: 14px; color: #666;'>8-character reset code</p>
                </div>
                
                <p>Enter this code on the reset page. Code expires in 1 hour.</p>
                
                <p style='margin-bottom: 0;'>
                    Best regards,<br>
                    <strong>The WhiteBox Team</strong>
                </p>
            </div>
        </div>
    ";
    
    // Send email silently
    $mail_subject = $subject;
    $mail_message = $message;
    $mail_to = $email;
    
    // Start output buffering
    ob_start();
    
    // Include mailer file - use absolute path
    $mailer_path = dirname(dirname(dirname(__FILE__))) . '/Huduma_WhiteBox/mails/general.php';
    if (file_exists($mailer_path)) {
        include($mailer_path);
    } else {
        // Try relative path
        $mailer_path = '../Huduma_WhiteBox/mails/general.php';
        if (file_exists($mailer_path)) {
            include($mailer_path);
        }
    }
    
    // Discard any output from mailer
    ob_end_clean();
    
    // Set session for next steps
    $_SESSION['reset_email'] = $email;
    
    echo base64_encode('success');
    
} elseif ($action === 'verify') {
    // Step 2: Verify reset code
    $email = base64_decode($_POST['email'] ?? '');
    $code = base64_decode($_POST['code'] ?? '');
    
    if (empty($email) || empty($code)) {
        die(base64_encode('invalid_data'));
    }
    
    $email = mysqli_real_escape_string($con, trim($email));
    $code = mysqli_real_escape_string($con, trim($code));
    
    // Check if code is valid and not expired
    $result = mysqli_query($con, "SELECT * FROM users WHERE email='$email' 
                AND token='$code' AND token_type='reset' AND token_expires_at > NOW()");
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['reset_verified'] = true;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;
        echo base64_encode('success');
    } else {
        // Check if expired
        $expired = mysqli_query($con, "SELECT * FROM users WHERE email='$email' 
                    AND token='$code' AND token_type='reset' AND token_expires_at <= NOW()");
        
        if (mysqli_num_rows($expired) > 0) {
            echo base64_encode('expired');
        } else {
            echo base64_encode('invalid_code');
        }
    }
    
} elseif ($action === 'update') {
    // Step 3: Update password
    $email = base64_decode($_POST['email'] ?? '');
    $code = base64_decode($_POST['code'] ?? '');
    $password = base64_decode($_POST['password'] ?? '');
    $confirm_password = base64_decode($_POST['confirm_password'] ?? '');
    
    // Validate
    if (empty($password) || strlen($password) < 8) {
        die(base64_encode('invalid_password'));
    }
    
    if ($password !== $confirm_password) {
        die(base64_encode('password_mismatch'));
    }
    
    $email = mysqli_real_escape_string($con, trim($email));
    $code = mysqli_real_escape_string($con, trim($code));
    
    // Verify code again
    $result = mysqli_query($con, "SELECT * FROM users WHERE email='$email' 
                AND token='$code' AND token_type='reset' AND token_expires_at > NOW()");
    
    if (mysqli_num_rows($result) === 0) {
        die(base64_encode('invalid_session'));
    }
    
    // Hash new password
    $hashed_password = hashword(base64_encode($password), $SALT);
    
    // Update password and clear reset token
    mysqli_query($con, "UPDATE users SET 
        password = '$hashed_password',
        token = NULL,
        token_type = NULL,
        token_expires_at = NULL,
        updated_at = NOW()
        WHERE email = '$email'");
    
    // Clear session
    unset($_SESSION['reset_verified']);
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_code']);
    
    echo base64_encode('success');
    
} else {
    echo base64_encode('invalid_action');
}

mysqli_close($con);
?>
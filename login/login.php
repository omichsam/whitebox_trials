<?php
// login/login.php - Improved Login Processor
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Configuration
$SALT = "A073955@am";

// Function to generate activation code
function generateCode($length = 8) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// Function to hash password
function hashword($string, $salt) {
    return crypt($string, '$1$' . $salt . '$');
}

// Function to send activation email
function sendActivationEmail($email, $first_name, $last_name, $activation_code) {
    $codeb = base64_encode($activation_code);
    $keyb = base64_encode($email);
    $activation_link = "http://whitebox.go.ke/activate.php?code=$codeb&key=$keyb";
    
    $subject = "Account Activation Required - WhiteBox";
    
    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #085c02; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>Account Activation Required</h2>
            </div>
            <div style='padding: 25px; background: #f9f9f9;'>
                <p>Dear <strong>$first_name $last_name</strong>,</p>
                <p>Your account requires activation. Please use the code below:</p>
                
                <div style='background: white; border: 2px solid #085c02; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                    <div style='font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #333; 
                             margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 6px;
                             font-family: monospace;'>
                        $activation_code
                    </div>
                    <p style='font-size: 14px; color: #666;'>
                        8-digit activation code
                    </p>
                </div>
                
                <div style='text-align: center; margin: 20px 0;'>
                    <p>Or click the link below:</p>
                    <a href='$activation_link' 
                       style='background: #085c02; color: white; padding: 12px 24px; text-decoration: none; 
                              border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;'>
                        Activate My Account
                    </a>
                </div>
                
                <p>This code will expire in 24 hours.</p>
                
                <p style='margin-bottom: 0;'>
                    Best regards,<br>
                    <strong>The WhiteBox Team</strong>
                </p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                <p style='margin: 0;'>© " . date('Y') . " Huduma WhiteBox Platform. All rights reserved.</p>
            </div>
        </div>
    ";
    
    // Set variables for mailer
    $GLOBALS['mail_subject'] = $subject;
    $GLOBALS['mail_message'] = $message;
    $GLOBALS['mail_to'] = $email;
    
    // Include the mailer file
    include(dirname(dirname(__FILE__)) . '/Huduma_WhiteBox/mails/general.php');
    
    return true;
}

// Set headers for AJAX response
header('Content-Type: text/plain');
header('Cache-Control: no-cache, must-revalidate');

// Log errors to a file
$log_file = dirname(__FILE__) . '/login_errors.log';

try {
    // Include database connection
    $connect_path = dirname(dirname(__FILE__)) . '/connect.php';
    
    if (!file_exists($connect_path)) {
        throw new Exception("Database connection file not found");
    }
    
    include($connect_path);
    
    if (!isset($con) || !$con) {
        throw new Exception("Database connection failed");
    }
    
    // Check if POST data exists
    if (!isset($_POST['busername']) || !isset($_POST['bpass'])) {
        echo base64_encode("All fields required!");
        exit();
    }
    
    $old_user = trim($_POST['busername']);
    $oldpass = trim($_POST['bpass']);
    
    // Check if credentials are provided
    if (empty($old_user) || empty($oldpass)) {
        echo base64_encode("missing_credentials");
        exit();
    }
    
    // Decode inputs
    $decoded_user = base64_decode($old_user);
    $decoded_pass = base64_decode($oldpass);
    
    if ($decoded_user === false || $decoded_pass === false) {
        echo base64_encode("invalid_encoding");
        exit();
    }
    
    // Sanitize inputs
    $my_user = strtolower(mysqli_real_escape_string($con, $decoded_user));
    $my_pass = mysqli_real_escape_string($con, $decoded_pass);
    
    // Hash the password
    $hashed_password = hashword(base64_encode($my_pass), $SALT);
    
    // Check if user exists
    $checkExist = mysqli_query($con, "SELECT * FROM users WHERE email='$my_user'");
    
    if (!$checkExist) {
        echo base64_encode("db_error");
        exit();
    }
    
    if (mysqli_num_rows($checkExist) == 0) {
        echo base64_encode("user_not_found");
        exit();
    }
    
    $user = mysqli_fetch_assoc($checkExist);
    $stored_password = $user['password'];
    $first_name = $user['first_name'] ?? '';
    $last_name = $user['last_name'] ?? '';
    $country = $user['country'] ?? '';
    $email = $user['email'];
    $account_type = $user['account_type'] ?? 'regular';
    
    // Verify password
    if ($hashed_password != $stored_password) {
        echo base64_encode("invalid_credentials");
        exit();
    }
    
    // Check if account is activated
    if ($country == "KE") {
        // Account is activated - login successful
        
        // Set session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $email;
        $_SESSION["email"] = $email;
        $_SESSION["first_name"] = $first_name;
        $_SESSION["last_name"] = $last_name;
        $_SESSION["user_id"] = $user['id'] ?? '';
        
        // Update last login
        $current_time = date('Y-m-d H:i:s');
        mysqli_query($con, "UPDATE users SET last_login='$current_time' WHERE email='$my_user'");
        
        // Determine redirect based on account type
        $response = ($account_type == 'e_learning') ? "e_learning" : "portal";
        echo base64_encode($response);
        
    } else {
        // Account not activated - handle activation
        
        $token = $user['token'] ?? '';
        $token_type = $user['token_type'] ?? '';
        $token_expires_at = $user['token_expires_at'] ?? '';
        $current_time = date('Y-m-d H:i:s');
        
        // Check if we need new activation code
        if (empty($token) || $token_type != 'activation' || $token_expires_at < $current_time) {
            // Generate new activation code
            $activation_code = generateCode(8);
            $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Update database with new code
            $update = mysqli_query($con, "UPDATE users SET 
                token = '$activation_code',
                token_type = 'activation',
                token_expires_at = '$token_expires_at',
                updated_at = '$current_time'
                WHERE email = '$my_user'");
            
            if (!$update) {
                echo base64_encode("activation_update_failed");
                exit();
            }
        } else {
            $activation_code = $token;
        }
        
        // Send activation email
        sendActivationEmail($email, $first_name, $last_name, $activation_code);
        
        echo base64_encode("activation_required");
    }
    
    // Close connection
    mysqli_close($con);
    
} catch (Exception $e) {
    // Log error
    error_log(date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", 3, $log_file);
    echo base64_encode("server_error");
}

exit();
?>
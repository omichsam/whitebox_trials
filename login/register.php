<?php
// login/register.php - Registration with activation link
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(base64_encode('invalid_request'));
}

// Include database connection
include(dirname(dirname(__FILE__)) . '/connect.php');

if (!isset($con) || !$con) {
    die(base64_encode('db_connection_error'));
}

// Configuration
$SALT = "A073955@am";

// Function to hash password
function hashword($string, $salt) {
    return crypt($string, '$1$' . $salt . '$');
}

// Function to generate activation code
function generateCode($length = 8) {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// Decode and validate input
$inputs = [];
$fields = ['first_name', 'last_name', 'email', 'phone', 'gender', 'password', 'confirm_password'];

foreach ($fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        die(base64_encode('missing_field:' . $field));
    }
    
    $decoded = base64_decode($_POST[$field]);
    if ($decoded === false) {
        die(base64_encode('invalid_encoding'));
    }
    
    $inputs[$field] = trim($decoded);
}

// Validate email
if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
    die(base64_encode('invalid_email'));
}

// Check password length
if (strlen($inputs['password']) < 8) {
    die(base64_encode('password_short'));
}

// Check passwords match
if ($inputs['password'] !== $inputs['confirm_password']) {
    die(base64_encode('password_mismatch'));
}

// Sanitize inputs
$first_name = mysqli_real_escape_string($con, $inputs['first_name']);
$last_name = mysqli_real_escape_string($con, $inputs['last_name']);
$email = mysqli_real_escape_string($con, strtolower($inputs['email']));
$phone = mysqli_real_escape_string($con, $inputs['phone']);
$gender = mysqli_real_escape_string($con, $inputs['gender']);

// Check if email exists
$check_email = mysqli_query($con, "SELECT id FROM users WHERE email='$email'");
if (mysqli_num_rows($check_email) > 0) {
    die(base64_encode('email_exists'));
}

// Hash password
$hashed_password = hashword(base64_encode($inputs['password']), $SALT);

// Generate activation code
$activation_code = generateCode(8);
$current_time = date('Y-m-d H:i:s');
$expiry_time = date('Y-m-d H:i:s', strtotime('+24 hours'));

// Insert user
$sql = "INSERT INTO users (first_name, last_name, email, phone, gender, password, 
        token, token_type, token_expires_at, country, created_at, updated_at)
        VALUES ('$first_name', '$last_name', '$email', '$phone', '$gender', 
                '$hashed_password', '$activation_code', 'activation', 
                '$expiry_time', 'not_activated', '$current_time', '$current_time')";

if (mysqli_query($con, $sql)) {
    // Prepare activation data for response
    $codeb = base64_encode($activation_code);
    $keyb = base64_encode($email);
    $user_id = mysqli_insert_id($con);
    
    // Prepare email content
    $subject = "Account Activation Required - WhiteBox";
    $activation_link = "http://whitebox.go.ke/activate.php?code=$codeb&key=$keyb";
    
    $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #085c02; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>Welcome to WhiteBox!</h2>
            </div>
            <div style='padding: 25px; background: #f9f9f9;'>
                <p>Dear <strong>$first_name $last_name</strong>,</p>
                <p>Thank you for registering with WhiteBox. Please activate your account using the code below:</p>
                
                <div style='background: white; border: 2px solid #085c02; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                    <div style='font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #333; 
                             margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 6px;
                             font-family: monospace;'>
                        $activation_code
                    </div>
                    <p style='font-size: 14px; color: #666;'>8-digit activation code</p>
                </div>
                
               
                
                <p>This activation code will expire in 24 hours.</p>
                
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
    
    // Send email using mailer (silently)
    $mail_subject = $subject;
    $mail_message = $message;
    $mail_to = $email;
    
    // Start output buffering to capture mailer output
    ob_start();
    
    // Include mailer file
    $mailer_path = dirname(dirname(dirname(__FILE__))) . '/Huduma_WhiteBox/mails/general.php';
    if (file_exists($mailer_path)) {
        include($mailer_path);
    } else {
        // Try alternative path
        include('../Huduma_WhiteBox/mails/general.php');
    }
    
    // Discard any output from mailer
    ob_end_clean();
    
    // Return success with activation link data
    $response_data = [
        'status' => 'success',
        'message' => 'Account created successfully!',
        'activation_code' => $activation_code,
        'email' => $email,
        'activation_link' => $activation_link,
        'direct_link' => "activate.php?code=$codeb&key=$keyb"
    ];
    
    echo base64_encode(json_encode($response_data));
    
} else {
    error_log("Registration failed: " . mysqli_error($con));
    echo base64_encode('db_error');
}

mysqli_close($con);
exit();
?>
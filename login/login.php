<?php
session_start();
include("../connect.php");

// Include the mailer file
include("../Huduma_WhiteBox/mails/general.php");

// Configuration
$SALT = "A073955@am";

// Functions
function generateCode($length = 8)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

function generateSessionCode($length = 15)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz@!#$%&";
    return base64_encode(substr(str_shuffle($chars), 0, $length));
}

function hashword($string, $salt)
{
    return crypt($string, '$1$' . $salt . '$');
}

function setupUserSession($user_data)
{
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = generateSessionCode();
    $_SESSION["username"] = $user_data['email'];
    $_SESSION["email"] = $user_data['email'];
    $_SESSION["first_name"] = $user_data['first_name'];
    $_SESSION["last_name"] = $user_data['last_name'];
    $_SESSION["user_id"] = $user_data['id'];
}

function sendActivationEmail($email, $first_name, $last_name, $activation_code)
{
    global $con; // Use the existing database connection

    // Get mailer settings from database
    $informations = mysqli_query($con, "SELECT * FROM settings_mailer WHERE status='active' LIMIT 1");
    if (!$informations) {
        error_log("Failed to fetch mailer settings: " . mysqli_error($con));
        return false;
    }

    $get = mysqli_fetch_assoc($informations);

    if ($get) {
        $email_sender = $get['email_sender'] ?? "noreply@whitebox.go.ke";
        $reply_to_email = $get['reply_to_email'] ?? "support@whitebox.go.ke";
    } else {
        // Default values
        $email_sender = "noreply@whitebox.go.ke";
        $reply_to_email = "support@whitebox.go.ke";
    }

    $codeb = base64_encode($activation_code);
    $keyb = base64_encode($email);
    $subject = "Account Verification - WhiteBox";

    $activation_link = "http://whitebox.go.ke/activate.php?code=$codeb&key=$keyb";

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
                
                <hr style='border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;'>
                
                <div style='background: #fff8e1; border: 1px solid #ffd54f; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p style='margin: 0; color: #5d4037; font-size: 14px;'>
                        <strong>Note:</strong> If the button above doesn't work, copy and paste this link in your browser:<br>
                        <span style='word-break: break-all; color: #1976d2;'>$activation_link</span>
                    </p>
                </div>
                
                <p style='margin-bottom: 0;'>
                    Best regards,<br>
                    <strong>The WhiteBox Team</strong>
                </p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;'>
                <p style='margin: 0;'>© " . date('Y') . " Huduma WhiteBox Platform. All rights reserved.</p>
                <p style='margin: 5px 0 0 0; font-size: 11px; color: #bbb;'>
                    This is an automated message, please do not reply directly to this email.
                </p>
            </div>
        </div>
    ";

    // Set global variables for the mailer
    global $mail_subject, $mail_message, $mail_to;

    $mail_subject = $subject;
    $mail_message = $message;
    $mail_to = $email;

    try {
        // Include the mailer file which will send the email
        include("../Huduma_WhiteBox/mails/general.php");
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

// Main execution starts here

// Check if connection exists
if (!isset($con) || !$con) {
    error_log("Database connection failed in login.php");
    echo base64_encode("db_connection_error");
    exit();
}

// Validate input
if (!isset($_POST['busername']) || !isset($_POST['bpass'])) {
    echo base64_encode("All fields required!");
    exit();
}

$old_user = $_POST['busername'] ?? '';
$oldpass = $_POST['bpass'] ?? '';

// Check if credentials are provided
if (empty($old_user) || empty($oldpass)) {
    echo base64_encode("missing_credentials");
    exit();
}

// Decode and sanitize inputs
$decoded_user = base64_decode($old_user);
$decoded_pass = base64_decode($oldpass);

if ($decoded_user === false || $decoded_pass === false) {
    echo base64_encode("invalid_encoding");
    exit();
}

$my_user = strtolower(mysqli_real_escape_string($con, $decoded_user));
$my_pass = mysqli_real_escape_string($con, $decoded_pass);

// Hash the password
$hashed_password = hashword(base64_encode($my_pass), $SALT);

// Check if user exists in database
$checkExist = mysqli_query($con, "SELECT * FROM users WHERE email='$my_user'");

if (!$checkExist) {
    error_log("Database query error: " . mysqli_error($con));
    echo base64_encode("db_error");
    exit();
}

if (mysqli_num_rows($checkExist) == 0) {
    echo base64_encode("user_not_found");
    exit();
}

$user = mysqli_fetch_assoc($checkExist);
$stored_password = $user['password'];
$first_name = $user['first_name'];
$last_name = $user['last_name'];
$country = $user['country'];
$email = $user['email'];
$account_type = $user['account_type'] ?? 'regular';

// Verify password
if ($hashed_password != $stored_password) {
    echo base64_encode("invalid_credentials");
    exit();
}

// Check if account is activated (country = "KE")
if ($country == "KE") {
    // Account is activated - proceed with login

    // Generate session
    setupUserSession($user);

    // Update last login time
    $current_time = date('Y-m-d H:i:s');
    $update_query = mysqli_query($con, "UPDATE users SET last_login='$current_time' WHERE email='$my_user'");

    if (!$update_query) {
        error_log("Failed to update last login: " . mysqli_error($con));
    }

    // Determine redirect based on account type
    if ($account_type == 'e_learning') {
        echo base64_encode("e_learning");
    } else {
        echo base64_encode("portal");
    }

} else {
    // Account not activated
    $token = $user['token'] ?? '';
    $token_type = $user['token_type'] ?? '';
    $token_expires_at = $user['token_expires_at'] ?? '';

    $current_time = date('Y-m-d H:i:s');
    $needs_new_token = false;

    // Check if token is valid
    if (empty($token) || $token_type != 'activation' || $token_expires_at < $current_time) {
        // Generate new activation code
        $activation_code = generateCode(8);
        $token_type = 'activation';
        $token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Update database with new activation token
        $update_token = mysqli_query($con, "UPDATE users SET 
            token = '$activation_code',
            token_type = '$token_type',
            token_expires_at = '$token_expires_at',
            updated_at = '$current_time'
            WHERE email = '$my_user'");

        if ($update_token) {
            // Send activation email using the included mailer
            $email_sent = sendActivationEmail($email, $first_name, $last_name, $activation_code);

            if (!$email_sent) {
                error_log("Failed to send activation email to: $email");
                // Still return activation_required, but log the error
            }

            echo base64_encode("activation_required");
        } else {
            error_log("Failed to update token: " . mysqli_error($con));
            echo base64_encode("token_update_failed");
        }
    } else {
        // Token still valid - resend the existing activation code
        $email_sent = sendActivationEmail($email, $first_name, $last_name, $token);

        if (!$email_sent) {
            error_log("Failed to resend activation email to: $email");
        }

        echo base64_encode("activation_required");
    }
}

// Close database connection
mysqli_close($con);
exit();
?>
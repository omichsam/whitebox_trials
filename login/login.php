<?php
// login/login.php - Silent mailer version with redirect
session_start();

// Configuration
$SALT = "A073955@am";

// Function to generate activation code
function generateCode($length = 8)
{
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    return substr(str_shuffle($chars), 0, $length);
}

// Function to hash password
function hashword($string, $salt)
{
    return crypt($string, '$1$' . $salt . '$');
}

// Set headers for AJAX response
header('Content-Type: text/plain');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Include database connection
    include(dirname(dirname(__FILE__)) . '/connect.php');

    if (!isset($con) || !$con) {
        echo base64_encode("db_connection_error");
        exit();
    }

    // Get POST data
    $old_user = $_POST['busername'] ?? '';
    $oldpass = $_POST['bpass'] ?? '';

    // Validate inputs
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

    // Verify password
    if ($hashed_password != $user['password']) {
        echo base64_encode("invalid_credentials");
        exit();
    }

    // Check account activation
    if ($user['country'] == "KE") {
        // Account activated - login successful

        // Set session
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $user['email'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["first_name"] = $user['first_name'] ?? '';
        $_SESSION["last_name"] = $user['last_name'] ?? '';
        $_SESSION["user_id"] = $user['id'] ?? '';

        // Update last login
        mysqli_query($con, "UPDATE users SET last_login=NOW() WHERE email='$my_user'");

        // Return success
        $response = ($user['account_type'] ?? 'regular') == 'e_learning' ? "e_learning" : "portal";
        echo base64_encode($response);

    } else {
        // Account not activated

        $current_time = date('Y-m-d H:i:s');
        $token = $user['token'] ?? '';
        $token_expires_at = $user['token_expires_at'] ?? '';

        // Generate new activation code if needed
        if (empty($token) || $token_expires_at < $current_time) {
            $activation_code = generateCode(8);
            $expiry_time = date('Y-m-d H:i:s', strtotime('+24 hours'));

            mysqli_query($con, "UPDATE users SET 
                token = '$activation_code',
                token_type = 'activation',
                token_expires_at = '$expiry_time',
                updated_at = '$current_time'
                WHERE email = '$my_user'");
        } else {
            $activation_code = $token;
        }

        // Prepare activation email
        $codeb = base64_encode($activation_code);
        $keyb = base64_encode($user['email']);
        $activation_link = "http://whitebox.go.ke/activate.php?code=$codeb&key=$keyb";

        $subject = "Account Activation Required - WhiteBox";
        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #085c02; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Account Activation Required</h2>
                </div>
                <div style='padding: 25px; background: #f9f9f9;'>
                    <p>Dear <strong>{$user['first_name']} {$user['last_name']}</strong>,</p>
                    <p>Your account requires activation. Please use this code:</p>
                    
                    <div style='background: white; border: 2px solid #085c02; padding: 20px; margin: 20px 0; text-align: center; border-radius: 8px;'>
                        <div style='font-size: 32px; font-weight: bold; letter-spacing: 4px; color: #333; 
                                 margin: 15px 0; padding: 15px; background: #f8f9fa; border-radius: 6px;
                                 font-family: monospace;'>
                            $activation_code
                        </div>
                        <p style='font-size: 14px; color: #666;'>8-digit activation code</p>
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
            </div>
        ";

        // Set variables for mailer
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $user['email'];

        // Start output buffering
        ob_start();

        // Include mailer file - capture ALL output
        include(dirname(dirname(__FILE__)) . '/Huduma_WhiteBox/mails/general.php');

        // Discard all output from mailer
        ob_end_clean();

        // Instead of returning "activation_required", return a redirect URL
        // This tells frontend to redirect to activation page
        echo base64_encode("redirect:" . $codeb . ":" . $keyb);
    }

    mysqli_close($con);
    exit();

} catch (Exception $e) {
    echo base64_encode("server_error");
    exit();
}
?>
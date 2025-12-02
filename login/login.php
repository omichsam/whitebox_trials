<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple test response
if (isset($_POST['test'])) {
    echo base64_encode("test_success");
    exit();
}

// Configuration
$SALT = "A073955@am";

// Function to hash password
function hashword($string, $salt)
{
    return crypt($string, '$1$' . $salt . '$');
}

// First, test if we can connect to database
try {
    include("../connect.php");
    
    if (!isset($con) || !$con) {
        throw new Exception("Database connection failed");
    }
    
    // Test database connection
    if (!mysqli_ping($con)) {
        throw new Exception("Database connection lost");
    }
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
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
    error_log("Query error: " . mysqli_error($con));
    echo base64_encode("db_error");
    exit();
}

if (mysqli_num_rows($checkExist) == 0) {
    echo base64_encode("user_not_found");
    exit();
}

$user = mysqli_fetch_assoc($checkExist);
$stored_password = $user['password'];

// Verify password
if ($hashed_password != $stored_password) {
    echo base64_encode("invalid_credentials");
    exit();
}

// Check account activation status
$country = $user['country'] ?? '';

if ($country == "KE") {
    // Account is activated
    
    // Start session
    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $user['email'];
    $_SESSION["email"] = $user['email'];
    $_SESSION["first_name"] = $user['first_name'] ?? '';
    $_SESSION["last_name"] = $user['last_name'] ?? '';
    $_SESSION["user_id"] = $user['id'] ?? '';
    
    // Update last login
    $current_time = date('Y-m-d H:i:s');
    mysqli_query($con, "UPDATE users SET last_login='$current_time' WHERE email='$my_user'");
    
    echo base64_encode("portal");
    
} else {
    // Account not activated
    echo base64_encode("activation_required");
}

// Close connection
mysqli_close($con);
exit();
?>
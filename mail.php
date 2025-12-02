<?php
// mail_test_simple.php - Quick Email Testing Script
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Email Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #085c02; border-bottom: 2px solid #085c02; padding-bottom: 10px; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        textarea { height: 100px; font-family: monospace; }
        .btn { background: #085c02; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { opacity: 0.9; }
        .result { margin-top: 20px; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Quick Email Test</h2>
        
        <form method='post'>
            <div class='form-group'>
                <label>Test Email:</label>
                <input type='email' name='test_email' required placeholder='test@example.com' value='" . ($_POST['test_email'] ?? '') . "'>
            </div>
            
            <div class='form-group'>
                <button type='submit' name='test_php' class='btn'>Test PHP mail()</button>
                <button type='submit' name='test_custom' class='btn'>Test Custom Mail</button>
                <button type='submit' name='check_system' class='btn'>Check System</button>
            </div>
        </form>
        
        <div class='result'>";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_email = $_POST['test_email'] ?? 'test@example.com';

    if (isset($_POST['test_php'])) {
        testPHPMail($test_email);
    } elseif (isset($_POST['test_custom'])) {
        testCustomMail($test_email);
    } elseif (isset($_POST['check_system'])) {
        checkSystem();
    }
}

echo "</div></div></body></html>";

// Function to test PHP mail()
function testPHPMail($email)
{
    $subject = "PHP mail() Test - " . date('Y-m-d H:i:s');
    $message = "<h3>Test Email</h3><p>This tests if PHP mail() works.</p>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: test@whitebox.go.ke\r\n";

    echo "<h3>Testing PHP mail()</h3>";
    echo "<p><strong>To:</strong> $email</p>";
    echo "<p><strong>Subject:</strong> $subject</p>";

    if (mail($email, $subject, $message, $headers)) {
        echo "<div class='success'>✓ PHP mail() returned TRUE</div>";
        echo "<p>Check inbox/spam folder for email.</p>";
    } else {
        echo "<div class='error'>✗ PHP mail() returned FALSE</div>";
        echo "<p>Check PHP mail configuration.</p>";
    }
}

// Function to test custom mail system
function testCustomMail($email)
{
    $mail_file = 'Huduma_WhiteBox/mails/general.php';

    echo "<h3>Testing Custom Mail System</h3>";
    echo "<p><strong>File:</strong> $mail_file</p>";

    if (!file_exists($mail_file)) {
        echo "<div class='error'>✗ File not found!</div>";
        return;
    }

    // Set variables for mail system
    $mail_subject = "Custom Mail Test - " . date('Y-m-d H:i:s');
    $mail_message = "<h3>Custom Mail Test</h3><p>Testing your mail system.</p>";
    $mail_to = $email;

    // Capture output
    ob_start();
    include($mail_file);
    $output = ob_get_clean();

    if (empty($output)) {
        echo "<div class='success'>✓ Mail system executed (no output)</div>";
    } else {
        echo "<div class='warning'>Mail system output:</div>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }
}

// Function to check system
function checkSystem()
{
    echo "<h3>System Check</h3>";

    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

    echo "<p><strong>mail() function:</strong> ";
    echo function_exists('mail') ? "✓ Available" : "✗ Not Available";
    echo "</p>";

    echo "<p><strong>SMTP Server:</strong> " . (ini_get('SMTP') ?: 'Not set') . "</p>";

    echo "<p><strong>Sendmail Path:</strong> " . (ini_get('sendmail_path') ?: 'Not set') . "</p>";

    $mail_file = 'Huduma_WhiteBox/mails/general.php';
    echo "<p><strong>Mail System File:</strong> ";
    if (file_exists($mail_file)) {
        echo "✓ Found (" . filesize($mail_file) . " bytes)";
    } else {
        echo "✗ Not found";
    }
    echo "</p>";
}

?>
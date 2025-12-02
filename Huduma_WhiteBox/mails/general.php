<?php
// Huduma_WhiteBox/mails/general.php - FIXED VERSION

// Get the root directory path
$root_dir = dirname(dirname(dirname(__FILE__)));

// Include database connection using absolute path
include $root_dir . '/connect.php';

// Check if connection exists
if (!isset($con) || !$con) {
   die("Database connection failed");
}

// Get mailer settings from database
$informations = mysqli_query($con, "SELECT * FROM settings_mailer WHERE status='active' LIMIT 1") or die(mysqli_error($con));
$get = mysqli_fetch_assoc($informations);

if ($get) {
   $usernemail = $get['email'];
   $email_sender = $get['email_sender'];
   $reply_to_email = $get['reply_to_email'];
   $bcc_email = $get['bcc_email'];
   $Password = base64_decode($get['Password']);
   $hostb = $get['host_mail'];
   $html_enabled = $get['html_enabled'];
   $Portm = $get['Port_mail'];
   $SMTPSecure = $get['SMTPSecure'];
   $mail_engine = $get['mail_engine'];
} else {
   // Use default values if no settings found
   $usernemail = "noreply@whitebox.go.ke";
   $email_sender = "noreply@whitebox.go.ke";
   $reply_to_email = "support@whitebox.go.ke";
   $bcc_email = "";
   $Password = "";
   $hostb = "localhost";
   $html_enabled = "1";
   $Portm = "587";
   $SMTPSecure = "tls";
   $mail_engine = "phpmailer";
}

// Include required PHPMailer files
$phpmailer_path = $root_dir . '/PHPMailer/';
if (file_exists($phpmailer_path . 'PHPMailer.php')) {
   require $phpmailer_path . 'PHPMailer.php';
   require $phpmailer_path . 'SMTP.php';
} else {
   // Try alternative path
   require 'PHPMailer/PHPMailer.php';
   require 'PHPMailer/SMTP.php';
}

// Check if variables are set by calling script
if (!isset($mail_subject) || !isset($mail_message) || !isset($mail_to)) {
   die("Error: Required email variables not set. Need: \$mail_subject, \$mail_message, \$mail_to");
}

try {
   $mail = new PHPMailer\PHPMailer\PHPMailer(true);

   // Server settings
   $mail->isSMTP();
   $mail->SMTPDebug = 0; // 0 = off, 1 = client messages, 2 = client and server messages
   $mail->SMTPAuth = true;
   $mail->Host = $hostb;
   $mail->Port = $Portm;
   $mail->SMTPSecure = $SMTPSecure;
   $mail->Username = $usernemail;
   $mail->Password = $Password;

   // Sender and recipient
   $mail->setFrom($email_sender, 'WhiteBox');
   $mail->addAddress($mail_to);

   if (!empty($reply_to_email)) {
      $mail->addReplyTo($reply_to_email, 'WhiteBox Support');
   }

   if (!empty($bcc_email)) {
      $mail->addBCC($bcc_email);
   }

   // Content
   $mail->isHTML((bool) $html_enabled);
   $mail->Subject = $mail_subject;
   $mail->Body = $mail_message;

   // Optional plain text version
   $mail->AltBody = strip_tags($mail_message);

   // Send email
   if ($mail->send()) {
      echo "Email sent successfully to " . htmlspecialchars($mail_to);
   } else {
      echo "Error sending email: " . $mail->ErrorInfo;
   }

} catch (Exception $e) {
   echo "Mailer Error: " . $e->getMessage();
}

// Close database connection
mysqli_close($con);
?>
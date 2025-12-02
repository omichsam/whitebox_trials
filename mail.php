<?php
// mail_test.php - Comprehensive Email Testing Script
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>WhiteBox Mail System Test</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; line-height: 1.6; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #085c02ff 0%, #861616a0 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; margin-bottom: 20px; }
        .header h1 { margin-bottom: 10px; }
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-section { margin-bottom: 30px; border: 1px solid #ddd; border-radius: 8px; padding: 20px; }
        .test-section h3 { color: #085c02; margin-bottom: 15px; border-bottom: 2px solid #085c02; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type='text'], input[type='email'], textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        textarea { height: 150px; font-family: monospace; }
        .btn { background: #085c02; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 10px; }
        .btn:hover { opacity: 0.9; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .btn-warning { background: #ffc107; color: #333; }
        .result { margin-top: 15px; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #e7f3ff; color: #004085; border: 1px solid #b8daff; }
        .log { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; white-space: pre-wrap; margin-top: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #f8f9fa; }
        .status { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
        .hidden { display: none; }
        .tabs { display: flex; border-bottom: 2px solid #085c02; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; background: #f8f9fa; border: 1px solid #ddd; border-bottom: none; margin-right: 5px; border-radius: 5px 5px 0 0; }
        .tab.active { background: #085c02; color: white; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>WhiteBox Mail System Diagnostic Tool</h1>
            <p>Test and diagnose email functionality for your WhiteBox system</p>
        </div>

        <div class='tabs'>
            <div class='tab active' onclick='showTab(1)'>System Check</div>
            <div class='tab' onclick='showTab(2)'>Mail Function Test</div>
            <div class='tab' onclick='showTab(3)'>Custom Mail System Test</div>
            <div class='tab' onclick='showTab(4)'>Activation Email Test</div>
        </div>

        <!-- Tab 1: System Check -->
        <div id='tab1' class='test-section'>
            <h3>System Environment Check</h3>
            
            <div class='form-group'>
                <button class='btn btn-success' onclick='runSystemCheck()'>Run System Check</button>
            </div>
            
            <div id='systemCheckResult' class='result hidden'></div>
        </div>

        <!-- Tab 2: Mail Function Test -->
        <div id='tab2' class='test-section hidden'>
            <h3>Test PHP mail() Function</h3>
            
            <form id='mailTestForm'>
                <div class='form-group'>
                    <label for='test_email'>Recipient Email Address:</label>
                    <input type='email' id='test_email' name='test_email' required 
                           value='<?php echo isset($_SESSION['test_email']) ? htmlspecialchars($_SESSION['test_email']) : ''; ?>'
                           placeholder='test@example.com'>
                </div>
                
                <div class='form-group'>
                    <label for='test_subject'>Email Subject:</label>
                    <input type='text' id='test_subject' name='test_subject' required 
                           value='Test Email from WhiteBox System'>
                </div>
                
                <div class='form-group'>
                    <label for='test_message'>Email Message (HTML):</label>
                    <textarea id='test_message' name='test_message' required>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
        .header { background: #085c02; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { background: #333; color: white; padding: 10px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class='header'>
        <h2>Test Email from WhiteBox</h2>
    </div>
    <div class='content'>
        <p>This is a test email sent from the WhiteBox mail system diagnostic tool.</p>
        <p>If you received this email, it means the PHP mail() function is working correctly.</p>
        <p>Timestamp: <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    <div class='footer'>
        <p>© <?php echo date('Y'); ?> WhiteBox System Test</p>
    </div>
</body>
</html></textarea>
                </div>
                
                <div class='form-group'>
                    <button type='button' class='btn btn-success' onclick='testPHPMail()'>Send Test Email via PHP mail()</button>
                    <button type='button' class='btn btn-warning' onclick='testMailHeaders()'>Test Mail Headers</button>
                </div>
            </form>
            
            <div id='mailTestResult' class='result hidden'></div>
        </div>

        <!-- Tab 3: Custom Mail System Test -->
        <div id='tab3' class='test-section hidden'>
            <h3>Test Custom Mail System (general.php)</h3>
            
            <form id='customMailTestForm'>
                <div class='form-group'>
                    <label for='custom_test_email'>Recipient Email Address:</label>
                    <input type='email' id='custom_test_email' name='custom_test_email' required 
                           value='<?php echo isset($_SESSION['test_email']) ? htmlspecialchars($_SESSION['test_email']) : ''; ?>'
                           placeholder='test@example.com'>
                </div>
                
                <div class='form-group'>
                    <label for='custom_test_subject'>Email Subject:</label>
                    <input type='text' id='custom_test_subject' name='custom_test_subject' required 
                           value='Test Email from WhiteBox Custom Mail System'>
                </div>
                
                <div class='form-group'>
                    <label>Mail System Path:</label>
                    <input type='text' value='../Huduma_WhiteBox/mails/general.php' readonly style='background: #f0f0f0;'>
                    <small>Current working directory: <?php echo getcwd(); ?></small>
                </div>
                
                <div class='form-group'>
                    <label>File Status:</label>
                    <div id='fileStatus'></div>
                </div>
                
                <div class='form-group'>
                    <button type='button' class='btn btn-success' onclick='testCustomMailSystem()'>Test Custom Mail System</button>
                    <button type='button' class='btn btn-warning' onclick='inspectMailSystem()'>Inspect Mail System</button>
                </div>
            </form>
            
            <div id='customMailTestResult' class='result hidden'></div>
        </div>

        <!-- Tab 4: Activation Email Test -->
        <div id='tab4' class='test-section hidden'>
            <h3>Test Activation Email (Full System)</h3>
            
            <form id='activationTestForm'>
                <div class='form-group'>
                    <label for='activation_email'>Test User Email:</label>
                    <input type='email' id='activation_email' name='activation_email' required 
                           placeholder='user@example.com'>
                </div>
                
                <div class='form-group'>
                    <label for='activation_first_name'>First Name:</label>
                    <input type='text' id='activation_first_name' name='activation_first_name' required 
                           value='Test'>
                </div>
                
                <div class='form-group'>
                    <label for='activation_last_name'>Last Name:</label>
                    <input type='text' id='activation_last_name' name='activation_last_name' required 
                           value='User'>
                </div>
                
                <div class='form-group'>
                    <label for='activation_code'>Activation Code:</label>
                    <input type='text' id='activation_code' name='activation_code' required 
                           value='ABCD1234' maxlength='8' style='font-family: monospace; letter-spacing: 2px;'>
                </div>
                
                <div class='form-group'>
                    <button type='button' class='btn btn-success' onclick='testActivationEmail()'>Test Activation Email</button>
                    <button type='button' class='btn btn-warning' onclick='testWelcomeEmail()'>Test Welcome Email</button>
                </div>
            </form>
            
            <div id='activationTestResult' class='result hidden'></div>
        </div>

        <!-- Log Output -->
        <div class='card'>
            <h3>Diagnostic Log</h3>
            <div id='logOutput' class='log'>Ready to run tests...</div>
        </div>
    </div>

    <script>
        // Tab switching
        function showTab(tabNumber) {
            // Hide all tabs
            document.querySelectorAll('.test-section').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab and mark as active
            document.getElementById('tab' + tabNumber).classList.remove('hidden');
            document.querySelectorAll('.tab')[tabNumber - 1].classList.add('active');
            
            // If showing custom mail tab, check file status
            if (tabNumber === 3) {
                checkFileStatus();
            }
        }

        // Check custom mail system file status
        function checkFileStatus() {
            fetch('mail_test_ajax.php?action=check_file')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('fileStatus').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('fileStatus').innerHTML = '<span class="status status-error">Error checking file: ' + error + '</span>';
                });
        }

        // Run system check
        function runSystemCheck() {
            const resultDiv = document.getElementById('systemCheckResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Running system checks...</div>';
            
            fetch('mail_test_ajax.php?action=system_check')
                .then(response => response.text())
                .then(data => {
                    resultDiv.innerHTML = data;
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="error">Error running system check: ' + error + '</div>';
                });
        }

        // Test PHP mail() function
        function testPHPMail() {
            const form = document.getElementById('mailTestForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('mailTestResult');
            
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Sending test email via PHP mail()...</div>';
            
            // Save email for next time
            sessionStorage.setItem('test_email', document.getElementById('test_email').value);
            
            fetch('mail_test_ajax.php?action=test_php_mail', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Error sending email: ' + error + '</div>';
            });
        }

        // Test mail headers
        function testMailHeaders() {
            const resultDiv = document.getElementById('mailTestResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Testing mail headers...</div>';
            
            fetch('mail_test_ajax.php?action=test_mail_headers')
                .then(response => response.text())
                .then(data => {
                    resultDiv.innerHTML = data;
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="error">Error testing headers: ' + error + '</div>';
                });
        }

        // Test custom mail system
        function testCustomMailSystem() {
            const form = document.getElementById('customMailTestForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('customMailTestResult');
            
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Testing custom mail system...</div>';
            
            fetch('mail_test_ajax.php?action=test_custom_mail', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Error testing custom mail system: ' + error + '</div>';
            });
        }

        // Inspect mail system
        function inspectMailSystem() {
            const resultDiv = document.getElementById('customMailTestResult');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Inspecting mail system...</div>';
            
            fetch('mail_test_ajax.php?action=inspect_mail_system')
                .then(response => response.text())
                .then(data => {
                    resultDiv.innerHTML = data;
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="error">Error inspecting mail system: ' + error + '</div>';
                });
        }

        // Test activation email
        function testActivationEmail() {
            const form = document.getElementById('activationTestForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('activationTestResult');
            
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Testing activation email...</div>';
            
            fetch('mail_test_ajax.php?action=test_activation_email', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Error testing activation email: ' + error + '</div>';
            });
        }

        // Test welcome email
        function testWelcomeEmail() {
            const form = document.getElementById('activationTestForm');
            const formData = new FormData(form);
            const resultDiv = document.getElementById('activationTestResult');
            
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<div class="info">Testing welcome email...</div>';
            
            fetch('mail_test_ajax.php?action=test_welcome_email', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = '<div class="error">Error testing welcome email: ' + error + '</div>';
            });
        }

        // Log function
        function log(message, type = 'info') {
            const logDiv = document.getElementById('logOutput');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = `[${timestamp}] ${message}\n`;
            
            if (type === 'error') {
                logDiv.innerHTML = '<span style="color: #dc3545;">' + logEntry + '</span>' + logDiv.innerHTML;
            } else if (type === 'success') {
                logDiv.innerHTML = '<span style="color: #28a745;">' + logEntry + '</span>' + logDiv.innerHTML;
            } else if (type === 'warning') {
                logDiv.innerHTML = '<span style="color: #ffc107;">' + logEntry + '</span>' + logDiv.innerHTML;
            } else {
                logDiv.innerHTML = logEntry + logDiv.innerHTML;
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Check for saved email
            const savedEmail = sessionStorage.getItem('test_email');
            if (savedEmail) {
                document.getElementById('test_email').value = savedEmail;
                document.getElementById('custom_test_email').value = savedEmail;
            }
            
            // Log initial info
            log('Mail testing system initialized');
            log('PHP Version: <?php echo phpversion(); ?>');
            log('Server: <?php echo $_SERVER['SERVER_SOFTWARE']; ?>');
        });
    </script>
</body>
</html>";
?>

<?php
// AJAX handler - save this as mail_test_ajax.php in the same directory
if (isset($_GET['action'])) {
    // Start output buffering to capture any output
    ob_start();
    
    switch ($_GET['action']) {
        case 'check_file':
            checkFileStatus();
            break;
        case 'system_check':
            runSystemCheck();
            break;
        case 'test_php_mail':
            testPHPMail();
            break;
        case 'test_mail_headers':
            testMailHeaders();
            break;
        case 'test_custom_mail':
            testCustomMailSystem();
            break;
        case 'inspect_mail_system':
            inspectMailSystem();
            break;
        case 'test_activation_email':
            testActivationEmail();
            break;
        case 'test_welcome_email':
            testWelcomeEmail();
            break;
    }
    
    $output = ob_get_clean();
    echo $output;
    exit;
}

// Function to check file status
function checkFileStatus() {
    $mail_file = '../Huduma_WhiteBox/mails/general.php';
    
    if (file_exists($mail_file)) {
        $file_size = filesize($mail_file);
        $file_perms = substr(sprintf('%o', fileperms($mail_file)), -4);
        $file_mtime = date('Y-m-d H:i:s', filemtime($mail_file));
        
        echo "<div class='success'>";
        echo "<p><strong>✓ File Found:</strong> $mail_file</p>";
        echo "<p><strong>Size:</strong> " . number_format($file_size) . " bytes</p>";
        echo "<p><strong>Permissions:</strong> $file_perms</p>";
        echo "<p><strong>Last Modified:</strong> $file_mtime</p>";
        
        // Check if file is readable
        if (is_readable($mail_file)) {
            echo "<p><strong>Readable:</strong> <span class='status status-success'>Yes</span></p>";
        } else {
            echo "<p><strong>Readable:</strong> <span class='status status-error'>No</span></p>";
        }
        
        // Check first few lines
        $content = file_get_contents($mail_file);
        $first_lines = implode("\n", array_slice(explode("\n", $content), 0, 10));
        echo "<p><strong>Preview (first 10 lines):</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($first_lines) . "</pre>";
        
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<p><strong>✗ File Not Found:</strong> $mail_file</p>";
        echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
        
        // Try to find the file
        echo "<p><strong>Searching for mail files...</strong></p>";
        
        $possible_paths = [
            'Huduma_WhiteBox/mails/general.php',
            '../mails/general.php',
            '../../mails/general.php',
            'mails/general.php'
        ];
        
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                echo "<p>✓ Found alternative path: $path</p>";
            }
        }
        
        echo "</div>";
    }
}

// Function to run system check
function runSystemCheck() {
    echo "<div class='result'>";
    echo "<h4>System Environment Check</h4>";
    
    echo "<table class='table'>";
    echo "<tr><th>Check</th><th>Status</th><th>Details</th></tr>";
    
    // PHP Version
    $php_version = phpversion();
    $php_ok = version_compare($php_version, '7.0', '>=');
    echo "<tr>";
    echo "<td>PHP Version</td>";
    echo "<td><span class='status " . ($php_ok ? "status-success" : "status-warning") . "'>$php_version</span></td>";
    echo "<td>" . ($php_ok ? "OK (≥ 7.0)" : "Consider upgrading to PHP 7.0 or higher") . "</td>";
    echo "</tr>";
    
    // mail() function
    $mail_func = function_exists('mail');
    echo "<tr>";
    echo "<td>mail() Function</td>";
    echo "<td><span class='status " . ($mail_func ? "status-success" : "status-error") . "'>" . ($mail_func ? "Available" : "Not Available") . "</span></td>";
    echo "<td>" . ($mail_func ? "PHP mail() function is available" : "PHP mail() function is disabled") . "</td>";
    echo "</tr>";
    
    // SMTP settings
    $smtp_host = ini_get('SMTP');
    $smtp_port = ini_get('smtp_port');
    echo "<tr>";
    echo "<td>SMTP Server</td>";
    echo "<td><span class='status " . ($smtp_host ? "status-success" : "status-warning") . "'>" . ($smtp_host ? $smtp_host : "Not Set") . "</span></td>";
    echo "<td>Port: " . ($smtp_port ? $smtp_port : '25') . "</td>";
    echo "</tr>";
    
    // sendmail_path
    $sendmail_path = ini_get('sendmail_path');
    echo "<tr>";
    echo "<td>Sendmail Path</td>";
    echo "<td><span class='status " . ($sendmail_path ? "status-success" : "status-warning") . "'>" . ($sendmail_path ? htmlspecialchars($sendmail_path) : "Not Set") . "</span></td>";
    echo "<td>" . ($sendmail_path ? "Sendmail configuration found" : "Sendmail path not configured") . "</td>";
    echo "</tr>";
    
    // allow_url_fopen
    $allow_url_fopen = ini_get('allow_url_fopen');
    echo "<tr>";
    echo "<td>allow_url_fopen</td>";
    echo "<td><span class='status " . ($allow_url_fopen ? "status-success" : "status-warning") . "'>" . ($allow_url_fopen ? "On" : "Off") . "</span></td>";
    echo "<td>" . ($allow_url_fopen ? "Allows URL file access" : "May affect some mail libraries") . "</td>";
    echo "</tr>";
    
    // max_execution_time
    $max_exec_time = ini_get('max_execution_time');
    echo "<tr>";
    echo "<td>max_execution_time</td>";
    echo "<td><span class='status status-success'>$max_exec_time seconds</span></td>";
    echo "<td>Time limit for script execution</td>";
    echo "</tr>";
    
    // memory_limit
    $memory_limit = ini_get('memory_limit');
    echo "<tr>";
    echo "<td>memory_limit</td>";
    echo "<td><span class='status status-success'>$memory_limit</span></td>";
    echo "<td>Memory allocation limit</td>";
    echo "</tr>";
    
    // Server software
    $server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    echo "<tr>";
    echo "<td>Server Software</td>";
    echo "<td><span class='status status-success'>" . htmlspecialchars($server_software) . "</span></td>";
    echo "<td>Web server information</td>";
    echo "</tr>";
    
    // Operating System
    $os = PHP_OS;
    echo "<tr>";
    echo "<td>Operating System</td>";
    echo "<td><span class='status status-success'>$os</span></td>";
    echo "<td>Server operating system</td>";
    echo "</tr>";
    
    echo "</table>";
    
    // Additional checks
    echo "<h4>Additional Checks</h4>";
    
    // Check if session is working
    echo "<p><strong>Session Status:</strong> " . session_status() . " (0=DISABLED, 1=NONE, 2=ACTIVE)</p>";
    
    // Check disk space
    $disk_free = disk_free_space(__DIR__);
    $disk_total = disk_total_space(__DIR__);
    $disk_percent = round(($disk_free / $disk_total) * 100, 2);
    echo "<p><strong>Disk Space:</strong> " . formatBytes($disk_free) . " free of " . formatBytes($disk_total) . " ($disk_percent% free)</p>";
    
    echo "</div>";
}

// Function to test PHP mail()
function testPHPMail() {
    $to = $_POST['test_email'] ?? 'test@example.com';
    $subject = $_POST['test_subject'] ?? 'Test Email';
    $message = $_POST['test_message'] ?? 'Test message';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: WhiteBox Test <test@whitebox.go.ke>\r\n";
    $headers .= "Reply-To: support@whitebox.go.ke\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    echo "<div class='result'>";
    echo "<h4>PHP mail() Function Test</h4>";
    
    echo "<p><strong>To:</strong> " . htmlspecialchars($to) . "</p>";
    echo "<p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>";
    echo "<p><strong>Headers:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($headers) . "</pre>";
    
    // Try to send the email
    try {
        $result = mail($to, $subject, $message, $headers);
        
        if ($result) {
            echo "<div class='success'>";
            echo "<p><strong>✓ Email sent successfully!</strong></p>";
            echo "<p>The PHP mail() function appears to be working. Check the recipient's inbox (and spam folder).</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<p><strong>✗ Email sending failed!</strong></p>";
            echo "<p>The PHP mail() function returned false. This could mean:</p>";
            echo "<ul>";
            echo "<li>Mail server is not configured</li>";
            echo "<li>SMTP settings are incorrect</li>";
            echo "<li>PHP mail() function is disabled</li>";
            echo "<li>Sendmail is not installed or configured</li>";
            echo "</ul>";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<p><strong>✗ Exception occurred:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    echo "</div>";
}

// Function to test mail headers
function testMailHeaders() {
    echo "<div class='result'>";
    echo "<h4>Mail Headers Test</h4>";
    
    // Test different header formats
    $test_cases = [
        'Basic HTML' => [
            'headers' => "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: test@example.com",
            'description' => 'Basic HTML email headers'
        ],
        'With Reply-To' => [
            'headers' => "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: test@example.com\r\nReply-To: support@example.com",
            'description' => 'Headers with Reply-To address'
        ],
        'Full Headers' => [
            'headers' => "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: Test User <test@example.com>\r\nReply-To: Support <support@example.com>\r\nX-Mailer: PHP/" . phpversion(),
            'description' => 'Full set of headers with X-Mailer'
        ],
        'Plain Text' => [
            'headers' => "MIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8\r\nFrom: test@example.com",
            'description' => 'Plain text email headers'
        ]
    ];
    
    foreach ($test_cases as $name => $test) {
        echo "<h5>$name</h5>";
        echo "<p><strong>Description:</strong> {$test['description']}</p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($test['headers']) . "</pre>";
        echo "<hr>";
    }
    
    echo "</div>";
}

// Function to test custom mail system
function testCustomMailSystem() {
    $to = $_POST['custom_test_email'] ?? 'test@example.com';
    $subject = $_POST['custom_test_subject'] ?? 'Test Custom Mail';
    
    $message = "<html><body>";
    $message .= "<h2>Test Email from Custom Mail System</h2>";
    $message .= "<p>This is a test email sent via your custom mail system (general.php).</p>";
    $message .= "<p>Timestamp: " . date('Y-m-d H:i:s') . "</p>";
    $message .= "<p>If you receive this, your custom mail system is working!</p>";
    $message .= "</body></html>";
    
    $mail_file = '../Huduma_WhiteBox/mails/general.php';
    
    echo "<div class='result'>";
    echo "<h4>Custom Mail System Test</h4>";
    
    if (!file_exists($mail_file)) {
        echo "<div class='error'>";
        echo "<p><strong>✗ Custom mail system file not found:</strong> $mail_file</p>";
        echo "</div>";
        echo "</div>";
        return;
    }
    
    echo "<p><strong>File:</strong> $mail_file</p>";
    echo "<p><strong>To:</strong> " . htmlspecialchars($to) . "</p>";
    echo "<p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>";
    
    // Try to include the mail system
    try {
        // Set up variables that the mail system expects
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $to;
        
        // Start output buffering
        ob_start();
        
        // Include the mail system
        include($mail_file);
        
        // Get any output
        $output = ob_get_clean();
        
        echo "<p><strong>Mail System Output:</strong></p>";
        
        if (empty($output)) {
            echo "<div class='success'>";
            echo "<p><strong>✓ Mail system executed without output</strong></p>";
            echo "<p>The custom mail system was included and executed. Check if the email was received.</p>";
            echo "</div>";
        } else {
            echo "<div class='info'>";
            echo "<p><strong>Mail system produced output:</strong></p>";
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($output) . "</pre>";
            echo "</div>";
            
            // Check if output indicates success
            if (stripos($output, 'success') !== false || stripos($output, 'sent') !== false) {
                echo "<div class='success'>";
                echo "<p><strong>✓ Output suggests email was sent successfully</strong></p>";
                echo "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<p><strong>✗ Exception occurred:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</div>";
    }
    
    echo "</div>";
}

// Function to inspect mail system
function inspectMailSystem() {
    $mail_file = '../Huduma_WhiteBox/mails/general.php';
    
    echo "<div class='result'>";
    echo "<h4>Mail System Inspection</h4>";
    
    if (!file_exists($mail_file)) {
        echo "<div class='error'>";
        echo "<p><strong>✗ File not found:</strong> $mail_file</p>";
        echo "</div>";
        echo "</div>";
        return;
    }
    
    $content = file_get_contents($mail_file);
    
    // Analyze the file
    echo "<p><strong>File Size:</strong> " . strlen($content) . " bytes</p>";
    echo "<p><strong>Lines of Code:</strong> " . count(explode("\n", $content)) . "</p>";
    
    // Check for common patterns
    $patterns = [
        'mail(' => 'PHP mail() function calls',
        'PHPMailer' => 'PHPMailer usage',
        'SMTP' => 'SMTP configuration',
        'sendmail' => 'Sendmail usage',
        'smtp_host' => 'SMTP host configuration',
        'smtp_port' => 'SMTP port configuration',
        'smtp_user' => 'SMTP username',
        'smtp_pass' => 'SMTP password',
        'Content-type:' => 'Email content type headers',
        'MIME-Version:' => 'MIME version headers',
        'From:' => 'From headers',
        'Reply-To:' => 'Reply-To headers',
        'require' => 'Require statements',
        'include' => 'Include statements',
        'function' => 'Function definitions',
        'class' => 'Class definitions'
    ];
    
    echo "<h5>Code Analysis</h5>";
    echo "<table class='table'>";
    echo "<tr><th>Pattern</th><th>Found</th><th>Description</th></tr>";
    
    foreach ($patterns as $pattern => $description) {
        $found = (stripos($content, $pattern) !== false);
        echo "<tr>";
        echo "<td><code>$pattern</code></td>";
        echo "<td><span class='status " . ($found ? "status-success" : "status-warning") . "'>" . ($found ? "Yes" : "No") . "</span></td>";
        echo "<td>$description</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show file content (first 500 characters)
    echo "<h5>File Preview (first 500 characters):</h5>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";
    
    // Check for required variables
    echo "<h5>Required Variables Check:</h5>";
    
    $required_vars = ['mail_subject', 'mail_message', 'mail_to'];
    $missing_vars = [];
    
    foreach ($required_vars as $var) {
        if (strpos($content, '$' . $var) !== false) {
            echo "<p><span class='status status-success'>✓ Variable found: \$$var</span></p>";
        } else {
            echo "<p><span class='status status-warning'>✗ Variable not found: \$$var</span></p>";
            $missing_vars[] = $var;
        }
    }
    
    if (!empty($missing_vars)) {
        echo "<div class='warning'>";
        echo "<p><strong>Warning:</strong> Your activation script expects these variables but they weren't found in the mail system:</p>";
        echo "<ul>";
        foreach ($missing_vars as $var) {
            echo "<li>\$$var</li>";
        }
        echo "</ul>";
        echo "<p>You may need to update either the mail system or your activation script to use matching variable names.</p>";
        echo "</div>";
    }
    
    echo "</div>";
}

// Function to test activation email
function testActivationEmail() {
    $email = $_POST['activation_email'] ?? 'test@example.com';
    $first_name = $_POST['activation_first_name'] ?? 'Test';
    $last_name = $_POST['activation_last_name'] ?? 'User';
    $activation_code = $_POST['activation_code'] ?? 'ABCD1234';
    
    echo "<div class='result'>";
    echo "<h4>Activation Email Test</h4>";
    
    // Simulate the activation email function
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
    
    echo "<p><strong>Test Parameters:</strong></p>";
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    echo "<p>Name: " . htmlspecialchars($first_name . ' ' . $last_name) . "</p>";
    echo "<p>Activation Code: " . htmlspecialchars($activation_code) . "</p>";
    echo "<p>Activation Link: " . htmlspecialchars($activation_link) . "</p>";
    echo "<p>Expiry: " . htmlspecialchars($expiry_time) . "</p>";
    
    // Try to send using custom mail system
    $mail_file = '../Huduma_WhiteBox/mails/general.php';
    
    if (file_exists($mail_file)) {
        echo "<h5>Sending via Custom Mail System:</h5>";
        
        // Set up variables
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $email;
        
        // Start output buffering
        ob_start();
        
        try {
            include($mail_file);
            $output = ob_get_clean();
            
            if (empty($output)) {
                echo "<div class='success'>";
                echo "<p><strong>✓ Custom mail system executed without errors</strong></p>";
                echo "</div>";
            } else {
                echo "<div class='info'>";
                echo "<p><strong>Custom mail system output:</strong></p>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($output) . "</pre>";
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<p><strong>✗ Custom mail system error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='warning'>";
        echo "<p><strong>Custom mail system not found, testing with PHP mail() instead...</strong></p>";
        echo "</div>";
        
        // Test with PHP mail()
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: WhiteBox <noreply@whitebox.go.ke>\r\n";
        $headers .= "Reply-To: support@whitebox.go.ke\r\n";
        
        if (mail($email, $subject, $message, $headers)) {
            echo "<div class='success'>";
            echo "<p><strong>✓ PHP mail() sent test activation email</strong></p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<p><strong>✗ PHP mail() failed to send</strong></p>";
            echo "</div>";
        }
    }
    
    // Show email preview
    echo "<h5>Email Preview:</h5>";
    echo "<div style='border: 1px solid #ddd; padding: 10px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
    echo $message;
    echo "</div>";
    
    echo "</div>";
}

// Function to test welcome email
function testWelcomeEmail() {
    $email = $_POST['activation_email'] ?? 'test@example.com';
    $first_name = $_POST['activation_first_name'] ?? 'Test';
    $last_name = $_POST['activation_last_name'] ?? 'User';
    
    echo "<div class='result'>";
    echo "<h4>Welcome Email Test</h4>";
    
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
                    <a href='http://whitebox.go.ke/login.php' 
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
    
    echo "<p><strong>Test Parameters:</strong></p>";
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    echo "<p>Name: " . htmlspecialchars($first_name . ' ' . $last_name) . "</p>";
    
    // Try to send using custom mail system
    $mail_file = '../Huduma_WhiteBox/mails/general.php';
    
    if (file_exists($mail_file)) {
        echo "<h5>Sending via Custom Mail System:</h5>";
        
        // Set up variables
        $mail_subject = $subject;
        $mail_message = $message;
        $mail_to = $email;
        
        // Start output buffering
        ob_start();
        
        try {
            include($mail_file);
            $output = ob_get_clean();
            
            if (empty($output)) {
                echo "<div class='success'>";
                echo "<p><strong>✓ Custom mail system executed without errors</strong></p>";
                echo "</div>";
            } else {
                echo "<div class='info'>";
                echo "<p><strong>Custom mail system output:</strong></p>";
                echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($output) . "</pre>";
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<p><strong>✗ Custom mail system error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='warning'>";
        echo "<p><strong>Custom mail system not found, testing with PHP mail() instead...</strong></p>";
        echo "</div>";
        
        // Test with PHP mail()
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: WhiteBox <noreply@whitebox.go.ke>\r\n";
        
        if (mail($email, $subject, $message, $headers)) {
            echo "<div class='success'>";
            echo "<p><strong>✓ PHP mail() sent test welcome email</strong></p>";
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<p><strong>✗ PHP mail() failed to send</strong></p>";
            echo "</div>";
        }
    }
    
    // Show email preview
    echo "<h5>Email Preview:</h5>";
    echo "<div style='border: 1px solid #ddd; padding: 10px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
    echo $message;
    echo "</div>";
    
    echo "</div>";
}

// Helper function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!-- **How to use this testing script:**

1. **Save the entire code** as `mail_test.php` in your WhiteBox directory (same location as your activate.php file)

2. **Access it through your browser**: `http://yourdomain.com/whitebox/mail_test.php`

3. **Four testing tabs available**:
   - **System Check**: Tests PHP configuration, mail settings, and server environment
   - **Mail Function Test**: Tests PHP's built-in `mail()` function directly
   - **Custom Mail System Test**: Tests your `../Huduma_WhiteBox/mails/general.php` file
   - **Activation Email Test**: Tests the full activation email system

4. **Key features**:
   - Real-time diagnostics
   - Email previews
   - File inspection
   - Error logging
   - Session preservation for test emails

5. **What to look for**:
   - Green checkmarks indicate success
   - Red X marks indicate failures
   - Yellow warnings indicate potential issues
   - Detailed logs show exactly what's happening

This comprehensive testing tool will help you identify exactly where your mail system is failing and provide actionable information to fix it. -->
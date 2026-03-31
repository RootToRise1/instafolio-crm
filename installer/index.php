<?php
// Check if app is already installed (unless force_install is set)
$force_install = isset($_GET['force_install']);
$app_config_path = dirname(__DIR__) . '/application/config/app-config.php';

if (file_exists($app_config_path) && !$force_install) {
    // App is installed - show reinstall option
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = dirname($_SERVER['SCRIPT_NAME']);
    $path = str_replace('/installer', '', $path);
    $base_url = rtrim($protocol . '://' . $host . $path, '/');
    
    // Check if it's a POST request to delete config
    if (isset($_POST['reinstall'])) {
        unlink($app_config_path);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Instafolio CRM - Already Installed</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                font-family: 'Inter', sans-serif;
            }
            .installer-container {
                background: white;
                border-radius: 20px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                padding: 40px;
                max-width: 500px;
                text-align: center;
            }
            .status-icon {
                font-size: 4rem;
                color: #10b981;
                margin-bottom: 20px;
            }
            .btn {
                padding: 14px 32px;
                font-weight: 600;
                border-radius: 10px;
                margin: 10px;
            }
            .btn-primary {
                background: #4f46e5;
                border: none;
            }
            .btn-danger {
                background: #ef4444;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class="installer-container">
            <div class="status-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2>Instafolio CRM is Already Installed</h2>
            <p class="text-muted mb-4">The application has been previously installed on this server.</p>
            
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?php echo $base_url; ?>/index.php/admin" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Go to Admin Panel
                </a>
                <a href="<?php echo $base_url; ?>/index.php/login" class="btn btn-outline-primary">
                    <i class="bi bi-person me-2"></i> Go to Login
                </a>
            </div>
            
            <form method="post" style="margin-top: 25px;">
                <button type="submit" name="reinstall" value="1" class="btn btn-danger" onclick="return confirm('Are you sure? This will delete your current configuration and database settings!');">
                    <i class="bi bi-arrow-repeat me-2"></i> Reinstall (Reset Config)
                </button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instafolio CRM - Installation Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --light: #f8fafc;
            --dark: #1e293b;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .installer-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        
        .installer-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        
        .installer-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .installer-header p {
            opacity: 0.9;
            font-size: 1rem;
        }
        
        .installer-body {
            padding: 40px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        
        .step {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .step.active .step-number {
            background: var(--primary);
            color: white;
        }
        
        .step.completed .step-number {
            background: var(--success);
            color: white;
        }
        
        .step-label {
            font-weight: 500;
            color: var(--secondary);
        }
        
        .step.active .step-label {
            color: var(--dark);
        }
        
        .step-line {
            width: 60px;
            height: 2px;
            background: #e2e8f0;
            margin: 0 15px;
        }
        
        .step-line.completed {
            background: var(--success);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-control.is-invalid {
            border-color: var(--danger);
        }
        
        .input-group-text {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 10px;
            padding: 14px 32px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .btn-outline-secondary {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 32px;
            font-weight: 600;
        }
        
        .btn-outline-secondary:hover {
            background: #f8fafc;
            border-color: var(--secondary);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        
        .progress {
            height: 6px;
            border-radius: 10px;
            background: #e2e8f0;
            overflow: hidden;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, var(--primary) 0%, var(--info) 100%);
            transition: width 0.5s ease;
        }
        
        .check-icon {
            color: var(--success);
            font-size: 1.5rem;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 2px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
        }
        
        .feature-item i {
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .installation-status {
            text-align: center;
            padding: 40px;
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        @media (max-width: 576px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .step-label {
                display: none;
            }
            
            .step-line {
                width: 30px;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-header">
            <i class="bi bi-grid-3x3-gap-fill" style="font-size: 3rem; margin-bottom: 15px;"></i>
            <h1>Instafolio CRM</h1>
            <p>Installation Wizard - Version 3.1.6</p>
        </div>
        
        <div class="installer-body">
            <div class="step-indicator">
                <div class="step active" id="step1-indicator">
                    <div class="step-number">1</div>
                    <span class="step-label">Requirements</span>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step2-indicator">
                    <div class="step-number">2</div>
                    <span class="step-label">Database</span>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step3-indicator">
                    <div class="step-number">3</div>
                    <span class="step-label">Site Settings</span>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step4-indicator">
                    <div class="step-number">4</div>
                    <span class="step-label">Complete</span>
                </div>
            </div>
            
            <div class="progress mb-4">
                <div class="progress-bar" id="progress-bar" style="width: 25%"></div>
            </div>
            
            <div id="alert-container"></div>
            
            <!-- Step 1: Requirements -->
            <div id="step-1" class="fade-in">
                <h4 class="mb-4"><i class="bi bi-check2-circle me-2"></i>System Requirements</h4>
                
                <div class="features-grid">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>PHP 7.4+</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>MySQL 5.7+</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>mbstring Extension</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>json Extension</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>curl Extension</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>fileinfo Extension</span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <?php
                    $script_path = dirname($_SERVER['SCRIPT_NAME']);
                    $base_path = ($script_path === '/' || $script_path === '\\') ? '/' : rtrim($script_path, '/');
                    $install_type = ($base_path === '/') ? 'Domain Root' : 'Subfolder: ' . $base_path;
                    ?>
                    <div class="feature-item">
                        <i class="bi bi-folder-fill"></i>
                        <span>Installation Path: <strong><?php echo $base_path; ?></strong></span>
                    </div>
                    <div class="feature-item mt-2">
                        <i class="bi bi-globe"></i>
                        <span>Install Type: <strong><?php echo $install_type; ?></strong></span>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-5">
                    <div></div>
                    <button class="btn btn-primary" onclick="nextStep(2)">
                        Continue <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Database -->
            <div id="step-2" class="fade-in" style="display: none;">
                <h4 class="mb-4"><i class="bi bi-database me-2"></i>Database Configuration</h4>
                
                <form id="database-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Host</label>
                            <input type="text" class="form-control" name="db_host" value="localhost" required>
                            <div class="form-text">Usually "localhost" or "127.0.0.1"</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="db_name" value="instafolio" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Username</label>
                            <input type="text" class="form-control" name="db_user" value="root" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Password</label>
                            <input type="password" class="form-control" name="db_pass">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="create_db" name="create_db" checked>
                            <label class="form-check-label" for="create_db">
                                Create database if not exists
                            </label>
                        </div>
                    </div>
                </form>
                
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-outline-secondary" onclick="prevStep(1)">
                        <i class="bi bi-arrow-left me-2"></i> Back
                    </button>
                    <button class="btn btn-primary" onclick="testDatabase()">
                        <span id="test-btn-text">Test Connection</span>
                        <span id="test-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Site Settings -->
            <div id="step-3" class="fade-in" style="display: none;">
                <h4 class="mb-4"><i class="bi bi-gear me-2"></i>Site Configuration</h4>
                
                <form id="site-form">
                    <div class="mb-3">
                        <label class="form-label">Site URL</label>
                        <input type="text" class="form-control" name="site_url" id="site_url" required>
                        <div class="form-text">The URL where this application will be accessible</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="company_name" value="My Company">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Timezone</label>
                            <select class="form-select" name="timezone">
                                <?php
                                $timezones = [
                                    'UTC',
                                    'Africa/Abidjan','Africa/Accra','Africa/Algiers','Africa/Cairo','Africa/Casablanca','Africa/Johannesburg','Africa/Lagos','Africa/Nairobi','Africa/Tunis',
                                    'America/Anchorage','America/Argentina/Buenos_Aires','America/Bogota','America/Chicago','America/Denver','America/Detroit','America/Halifax','America/Havana','America/Lima','America/Los_Angeles','America/Manaus','America/Mexico_City','America/New_York','America/Panama','America/Phoenix','America/Santiago','America/Sao_Paulo','America/Toronto','America/Vancouver',
                                    'Asia/Baghdad','Asia/Bangkok','Asia/Colombo','Asia/Dhaka','Asia/Dubai','Asia/Ho_Chi_Minh','Asia/Hong_Kong','Asia/Jakarta','Asia/Jerusalem','Asia/Karachi','Asia/Kathmandu','Asia/Kolkata','Asia/Kuala_Lumpur','Asia/Kuwait','Asia/Manila','Asia/Riyadh','Asia/Seoul','Asia/Shanghai','Asia/Singapore','Asia/Taipei','Asia/Tehran','Asia/Tokyo',
                                    'Atlantic/Azores','Atlantic/Reykjavik',
                                    'Australia/Adelaide','Australia/Brisbane','Australia/Darwin','Australia/Hobart','Australia/Melbourne','Australia/Perth','Australia/Sydney',
                                    'Europe/Amsterdam','Europe/Athens','Europe/Belgrade','Europe/Berlin','Europe/Brussels','Europe/Bucharest','Europe/Budapest','Europe/Copenhagen','Europe/Dublin','Europe/Helsinki','Europe/Istanbul','Europe/Kiev','Europe/Lisbon','Europe/London','Europe/Madrid','Europe/Moscow','Europe/Oslo','Europe/Paris','Europe/Prague','Europe/Riga','Europe/Rome','Europe/Sofia','Europe/Stockholm','Europe/Tallinn','Europe/Vienna','Europe/Vilnius','Europe/Warsaw','Europe/Zurich',
                                    'Pacific/Auckland','Pacific/Fiji','Pacific/Guam','Pacific/Honolulu','Pacific/Samoa'
                                ];
                                foreach ($timezones as $tz) {
                                    $selected = ($tz === 'UTC') ? 'selected' : '';
                                    echo '<option value="' . $tz . '" ' . $selected . '>' . $tz . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date Format</label>
                            <select class="form-select" name="date_format">
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>
                </form>
                
                <div class="d-flex justify-content-between mt-5">
                    <button class="btn btn-outline-secondary" onclick="prevStep(2)">
                        <i class="bi bi-arrow-left me-2"></i> Back
                    </button>
                    <button class="btn btn-primary" onclick="install()">
                        <span id="install-btn-text">Install Now</span>
                        <span id="install-btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </div>
            
            <!-- Step 4: Complete -->
            <div id="step-4" class="fade-in" style="display: none;">
                <div class="installation-status">
                    <div class="status-icon">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <h3 class="mb-3">Installation Complete!</h3>
                    <p class="text-muted mb-4">Instafolio CRM has been successfully installed on your server.</p>
                    
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-info-circle me-2"></i>Next Steps</h5>
                            <ul class="text-start mb-0">
                                <li>Log in to the admin panel using credentials below</li>
                                <li>Configure your company settings</li>
                                <li>Set up email SMTP settings</li>
                                <li>Import your data</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Delete the <code>installer</code> folder after installation for security.
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#" id="admin-link" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Go to Admin Panel
                        </a>
                        <a href="#" id="login-link" class="btn btn-outline-primary btn-lg mt-3">
                            <i class="bi bi-person me-2"></i> Go to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        let dbConnected = false;
        
        // Auto-detect site URL
        document.getElementById('site_url').value = window.location.protocol + '//' + window.location.host + window.location.pathname.replace('/installer', '');
        
        function showAlert(type, message) {
            const container = document.getElementById('alert-container');
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            container.innerHTML = `
                <div class="alert alert-${type} d-flex align-items-center">
                    <i class="bi bi-${icon} me-2"></i>
                    ${message}
                </div>
            `;
            setTimeout(() => container.innerHTML = '', 5000);
        }
        
        function updateStepIndicator(step) {
            for (let i = 1; i <= 4; i++) {
                const stepEl = document.getElementById(`step${i}-indicator`);
                const lineEl = stepEl.nextElementSibling;
                
                stepEl.classList.remove('active', 'completed');
                if (lineEl && lineEl.classList.contains('step-line')) {
                    lineEl.classList.remove('completed');
                }
                
                if (i < step) {
                    stepEl.classList.add('completed');
                    if (lineEl && lineEl.classList.contains('step-line')) {
                        lineEl.classList.add('completed');
                    }
                } else if (i === step) {
                    stepEl.classList.add('active');
                }
            }
            
            document.getElementById('progress-bar').style.width = ((step - 1) / 3 * 100) + 25 + '%';
        }
        
        function nextStep(step) {
            if (step === 3 && !dbConnected) {
                showAlert('danger', 'Please test database connection first');
                return;
            }
            
            document.getElementById(`step-${currentStep}`).style.display = 'none';
            document.getElementById(`step-${step}`).style.display = 'block';
            currentStep = step;
            updateStepIndicator(step);
        }
        
        function prevStep(step) {
            document.getElementById(`step-${currentStep}`).style.display = 'none';
            document.getElementById(`step-${step}`).style.display = 'block';
            currentStep = step;
            updateStepIndicator(step);
        }
        
        function testDatabase() {
            const form = new FormData(document.getElementById('database-form'));
            
            document.getElementById('test-btn-text').textContent = 'Testing...';
            document.getElementById('test-btn-spinner').style.display = 'inline-block';
            
            fetch('install_ajax.php?action=test_db', {
                method: 'POST',
                body: form
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('test-btn-text').textContent = 'Test Connection';
                document.getElementById('test-btn-spinner').style.display = 'none';
                
                if (data.success) {
                    dbConnected = true;
                    showAlert('success', 'Database connection successful!');
                    nextStep(3);
                } else {
                    showAlert('danger', data.message || 'Database connection failed');
                }
            })
            .catch(error => {
                document.getElementById('test-btn-text').textContent = 'Test Connection';
                document.getElementById('test-btn-spinner').style.display = 'none';
                showAlert('danger', 'Connection test failed: ' + error.message);
            });
        }
        
        function install() {
            const dbForm = new FormData(document.getElementById('database-form'));
            const siteForm = new FormData(document.getElementById('site-form'));
            
            // Merge forms
            for (let [key, value] of dbForm.entries()) {
                siteForm.append(key, value);
            }
            
            document.getElementById('install-btn-text').textContent = 'Installing...';
            document.getElementById('install-btn-spinner').style.display = 'inline-block';
            
            fetch('install_ajax.php?action=install', {
                method: 'POST',
                body: siteForm
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('install-btn-text').textContent = 'Install Now';
                document.getElementById('install-btn-spinner').style.display = 'none';
                
                if (data.success) {
                    document.getElementById('admin-link').href = data.admin_url;
                    if (data.login_url) {
                        document.getElementById('login-link').href = data.login_url;
                    } else {
                        document.getElementById('login-link').href = data.admin_url;
                    }
                    nextStep(4);
                } else {
                    showAlert('danger', data.message || 'Installation failed');
                }
            })
            .catch(error => {
                document.getElementById('install-btn-text').textContent = 'Install Now';
                document.getElementById('install-btn-spinner').style.display = 'none';
                showAlert('danger', 'Installation failed: ' + error.message);
            });
        }
    </script>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Instafolio CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #818cf8;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 30px 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        
        .register-brand {
            flex: 1;
            min-width: 350px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        
        .register-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .register-brand p {
            opacity: 0.9;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .brand-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .brand-features li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .brand-features li i {
            margin-right: 12px;
            font-size: 1.2rem;
        }
        
        .register-form-section {
            flex: 1.5;
            min-width: 400px;
            padding: 40px 50px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .register-form-section::-webkit-scrollbar {
            width: 6px;
        }
        
        .register-form-section::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .register-form-section::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .register-header p {
            color: var(--secondary);
            margin: 0;
        }
        
        .register-header p a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .register-header p a:hover {
            text-decoration: underline;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 6px;
            display: block;
            font-size: 0.9rem;
        }
        
        .form-label .required {
            color: var(--danger);
        }
        
        .form-control {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }
        
        .form-control.is-invalid {
            border-color: var(--danger);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
            font-size: 1rem;
        }
        
        .input-icon .form-control {
            padding-left: 40px;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn-register {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-login {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 12px 32px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-login:hover {
            background: var(--primary);
            color: white;
        }
        
        .alert {
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
            background: none;
            border: none;
            padding: 0;
        }
        
        .password-toggle:hover {
            color: var(--primary);
        }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 20px 0;
        }
        
        .terms-checkbox input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
        }
        
        .terms-checkbox label {
            font-size: 0.85rem;
            color: var(--secondary);
        }
        
        .terms-checkbox label a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .terms-checkbox label a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }
            
            .register-brand {
                padding: 40px 30px;
                text-align: center;
            }
            
            .brand-features {
                display: none;
            }
            
            .register-form-section {
                padding: 30px 25px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="register-container fade-in">
        <div class="register-brand">
            <h1><i class="bi bi-grid-3x3-gap-fill me-2"></i>Instafolio</h1>
            <p>Create your account</p>
            
            <ul class="brand-features">
                <li><i class="bi bi-check-circle-fill"></i> Customer Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Project Tracking</li>
                <li><i class="bi bi-check-circle-fill"></i> Invoice Generation</li>
                <li><i class="bi bi-check-circle-fill"></i> Lead Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Task Automation</li>
            </ul>
        </div>
        
        <div class="register-form-section">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Already have an account? <a href="<?php echo site_url('login'); ?>">Sign in</a></p>
            </div>
            
            <?php if ($this->session->flashdata('message')): ?>
                <?php $alert_type = $this->session->flashdata('alert_type') ?? 'danger'; ?>
                <div class="alert alert-<?php echo $alert_type; ?>">
                    <i class="bi bi-<?php echo $alert_type == 'danger' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <?php echo form_open('authentication/register', ['id' => 'register-form']); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">First Name <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" name="firstname" placeholder="First name" value="<?php echo set_value('firstname'); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" name="lastname" placeholder="Last name" value="<?php echo set_value('lastname'); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" value="<?php echo set_value('email'); ?>" required>
                </div>
                <?php echo form_error('email'); ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Company Name</label>
                <div class="input-icon">
                    <i class="bi bi-building"></i>
                    <input type="text" class="form-control" name="company" placeholder="Your company name" value="<?php echo set_value('company'); ?>">
                </div>
                <?php echo form_error('company'); ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="bi bi-lock"></i>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Create password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="bi bi-eye" id="toggle-password"></i>
                        </button>
                    </div>
                    <?php echo form_error('password'); ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password <span class="required">*</span></label>
                    <div class="input-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" class="form-control" name="passwordr" id="passwordr" placeholder="Confirm password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('passwordr')">
                            <i class="bi bi-eye" id="toggle-passwordr"></i>
                        </button>
                    </div>
                    <?php echo form_error('passwordr'); ?>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <div class="input-icon">
                    <i class="bi bi-telephone"></i>
                    <input type="text" class="form-control" name="contact_phonenumber" placeholder="Your phone number" value="<?php echo set_value('contact_phonenumber'); ?>">
                </div>
            </div>
            
            <?php if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions') == 1) { ?>
            <div class="terms-checkbox">
                <input type="checkbox" name="accept_terms_and_conditions" id="accept_terms" <?php echo set_checkbox('accept_terms_and_conditions', 'on'); ?> required>
                <label for="accept_terms">
                    I agree to the <a href="<?php echo terms_url(); ?>" target="_blank">Terms & Conditions</a> and <a href="<?php echo privacy_policy_url(); ?>" target="_blank">Privacy Policy</a>
                </label>
            </div>
            <?php echo form_error('accept_terms_and_conditions'); ?>
            <?php } ?>
            
            <button type="submit" class="btn-register" id="register-btn">
                <span id="btn-text">Create Account</span>
                <span id="btn-spinner" class="loading-spinner" style="display: none;"></span>
            </button>
            
            <div class="text-center mt-3">
                <a href="<?php echo site_url('login'); ?>" class="btn-login">
                    <i class="bi bi-arrow-left me-2"></i> Back to Login
                </a>
            </div>
            
            <?php echo form_close(); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggleIcon = document.getElementById('toggle-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
        
        document.getElementById('register-form').addEventListener('submit', function() {
            const btn = document.getElementById('register-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            
            btn.disabled = true;
            btnText.textContent = 'Creating account...';
            btnSpinner.style.display = 'inline-block';
        });
    </script>
</body>
</html>

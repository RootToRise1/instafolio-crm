<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Instafolio CRM</title>
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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        
        .login-brand {
            flex: 1;
            min-width: 350px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        
        .login-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-brand p {
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
        
        .login-form-section {
            flex: 1;
            min-width: 350px;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: var(--secondary);
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 1rem;
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
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }
        
        .input-icon .form-control {
            padding-left: 45px;
        }
        
        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
        }
        
        .btn-login:disabled {
            background: var(--secondary);
            cursor: not-allowed;
        }
        
        .forgot-links {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        
        .forgot-links a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .forgot-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 20px;
            border: none;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }
        
        @media (max-width: 768px) {
            .login-brand {
                padding: 40px 30px;
            }
            
            .login-form-section {
                padding: 40px 30px;
            }
            
            .login-brand h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-brand">
            <h1><i class="bi bi-grid-3x3-gap-fill me-2"></i>Instafolio</h1>
            <p>Powerful CRM for your business</p>
            
            <ul class="brand-features">
                <li><i class="bi bi-check-circle-fill"></i> Customer Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Project Tracking</li>
                <li><i class="bi bi-check-circle-fill"></i> Invoice Generation</li>
                <li><i class="bi bi-check-circle-fill"></i> Lead Management</li>
                <li><i class="bi bi-check-circle-fill"></i> Task Automation</li>
            </ul>
        </div>
        
        <div class="login-form-section">
            <?php echo form_open('authentication/login', ['id' => 'login-form']); ?>
            
            <?php if ($this->session->flashdata('message')): ?>
                <?php $alert_type = $this->session->flashdata('alert_type') ?? 'danger'; ?>
                <div class="alert alert-<?php echo $alert_type; ?>">
                    <i class="bi bi-<?php echo $alert_type == 'danger' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                    <i class="bi bi-eye-slash" id="toggle-icon" style="cursor: pointer; right: 16px; left: auto;" onclick="togglePassword()"></i>
                </div>
            </div>
            
            <div class="form-group d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
            </div>
            
            <button type="submit" class="btn-login" id="login-btn">
                <span id="btn-text">Sign In</span>
                <span id="btn-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
            </button>
            
            <div class="forgot-links">
                <a href="<?php echo site_url('authentication/forgot_password'); ?>">Forgot Password?</a>
                <a href="<?php echo site_url('authentication/register'); ?>">Create Account</a>
            </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            }
        }
        
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            
            btn.disabled = true;
            btnText.textContent = 'Signing in...';
            btnSpinner.style.display = 'inline-block';
        });
    </script>
</body>
</html>
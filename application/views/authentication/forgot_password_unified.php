<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Instafolio CRM</title>
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
        
        .forgot-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }
        
        .forgot-brand {
            flex: 1;
            min-width: 350px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .forgot-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .forgot-brand p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .brand-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .forgot-form-section {
            flex: 1;
            min-width: 350px;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .forgot-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .forgot-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .forgot-header p {
            color: var(--secondary);
            margin: 0;
        }
        
        .forgot-header p a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .forgot-header p a:hover {
            text-decoration: underline;
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
        
        .btn-forgot {
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
        
        .btn-forgot:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .btn-forgot:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-back {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            text-align: center;
        }
        
        .btn-back:hover {
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
        
        @media (max-width: 768px) {
            .forgot-container {
                flex-direction: column;
            }
            
            .forgot-brand {
                padding: 40px 30px;
            }
            
            .forgot-form-section {
                padding: 40px 30px;
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
    <div class="forgot-container fade-in">
        <div class="forgot-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1><i class="bi bi-grid-3x3-gap-fill me-2"></i>Instafolio</h1>
            <p>Reset your password</p>
        </div>
        
        <div class="forgot-form-section">
            <div class="forgot-header">
                <h2>Forgot Password?</h2>
                <p>Enter your email address and we'll send you a link to reset your password.</p>
            </div>
            
            <?php if ($this->session->flashdata('message')): ?>
                <?php $alert_type = $this->session->flashdata('alert_type') ?? 'danger'; ?>
                <div class="alert alert-<?php echo $alert_type; ?>">
                    <i class="bi bi-<?php echo $alert_type == 'danger' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo $this->session->flashdata('message'); ?>
                </div>
            <?php endif; ?>
            
            <?php echo form_open('authentication/forgot_password', ['id' => 'forgot-form']); ?>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email address" required>
                </div>
                <?php echo form_error('email'); ?>
            </div>
            
            <button type="submit" class="btn-forgot" id="forgot-btn">
                <span id="btn-text">Send Reset Link</span>
                <span id="btn-spinner" class="loading-spinner" style="display: none;"></span>
            </button>
            
            <div class="text-center">
                <a href="<?php echo site_url('login'); ?>" class="btn-back">
                    <i class="bi bi-arrow-left me-2"></i> Back to Login
                </a>
            </div>
            
            <?php echo form_close(); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgot-form').addEventListener('submit', function() {
            const btn = document.getElementById('forgot-btn');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            
            btn.disabled = true;
            btnText.textContent = 'Sending...';
            btnSpinner.style.display = 'inline-block';
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Career Mind</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Auth CSS (for both login & register) -->
    <link rel="stylesheet" href="/assets/css/auth.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
</head>
<body>
    <main class="register-page d-flex flex-column align-items-center justify-content-center min-vh-100 px-3">
        <div class="brand-logo mb-3 text-center">
            <i class="fas fa-brain"></i>
            <span>Career<span style="color: #4361ee;">Mind</span></span>
        </div>
        <div class="text-center mb-4">
            <h2 class="mb-1">Create Account</h2>
            <p class="text-muted">Start your AI-powered career journey with the right profile.</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert-danger w-100 py-2 px-3 mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert-success w-100 py-2 px-3 mb-3">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/register" class="auth-form w-100" style="max-width: 480px;">
                <!-- Name Field -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-user me-1"></i>
                        Full Name
                    </label>
                    <input type="text" name="name" class="form-control" 
                           placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                           required>
                    <small class="help-text">Your name as you want it to appear</small>
                </div>

                <!-- Email Field -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Enter your email address"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                    <small class="help-text">We'll never share your email with anyone</small>
                </div>

                <!-- Role Selection -->

                <!-- Password Field -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="password" 
                               class="form-control" 
                               placeholder="Create a strong password" 
                               required
                               pattern=".{8,}"
                               title="Password must be at least 8 characters">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="help-text">Minimum 8 characters with letters and numbers</small>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-1"></i>
                        Confirm Password
                    </label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" 
                               class="form-control" 
                               placeholder="Re-enter your password" 
                               required>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>
        <div class="text-center mt-3">
            <span class="text-muted">Already have an account?</span>
            <a href="/login" class="ms-2">Sign In</a>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggles
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const passwordInput = document.querySelector('input[name="password"]');
            const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
            
            // Toggle main password
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.querySelector('i').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
                });
            }
            
            // Toggle confirm password
            if (toggleConfirmPassword && confirmPasswordInput) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    this.querySelector('i').className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
                });
            }
            
            // Password strength indicator
            const passwordField = document.querySelector('input[name="password"]');
            const passwordHelp = document.querySelector('input[name="password"]').parentElement.nextElementSibling;
            
            if (passwordField && passwordHelp) {
                passwordField.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    let message = '';
                    
                    if (password.length >= 8) strength++;
                    if (/[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^A-Za-z0-9]/.test(password)) strength++;
                    
                    switch(strength) {
                        case 0:
                        case 1:
                            message = '<span style="color: #e74c3c;">Weak password</span>';
                            break;
                        case 2:
                            message = '<span style="color: #f39c12;">Medium password</span>';
                            break;
                        case 3:
                            message = '<span style="color: #3498db;">Good password</span>';
                            break;
                        case 4:
                            message = '<span style="color: #2ecc71;">Strong password</span>';
                            break;
                    }
                    
                    passwordHelp.innerHTML = message;
                });
            }
            
            // Form validation
            const form = document.querySelector('.auth-form');
            form?.addEventListener('submit', function(e) {
                const password = document.querySelector('input[name="password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
                const terms = document.querySelector('#terms').checked;
                
                // Check if passwords match
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match. Please try again.');
                    return;
                }
                
                // Check if terms are accepted
                if (!terms) {
                    e.preventDefault();
                    alert('Please accept the Terms of Service and Privacy Policy.');
                    return;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
                    submitBtn.disabled = true;
                }
            });
            
            // Role selection styling
            const roleSelect = document.querySelector('select[name="role"]');
            if (roleSelect) {
                roleSelect.addEventListener('change', function() {
                    if (this.value) {
                        this.style.borderColor = '#4361ee';
                        this.style.background = 'rgba(67, 97, 238, 0.05)';
                    }
                });
                
                // Apply initial styling if value exists
                if (roleSelect.value) {
                    roleSelect.style.borderColor = '#4361ee';
                    roleSelect.style.background = 'rgba(67, 97, 238, 0.05)';
                }
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Career Mind</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Login CSS -->
    <link rel="stylesheet" href="/assets/css/login.css">
    <style>
        /* Additional inline styles if needed */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
    </style>
</head>
<body>
    <main class="login-page d-flex flex-column align-items-center justify-content-center min-vh-100 px-3">
        <div class="brand-logo mb-3 text-center">
            <i class="fas fa-brain"></i>
            <span>Career<span style="color: #4361ee;">Mind</span></span>
        </div>
        <div class="text-center mb-4">
            <h2 class="mb-1">Welcome Back</h2>
            <p class="text-muted">Sign in to continue your career journey.</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert-danger w-100 py-2 px-3 mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/login" class="login-form w-100" style="max-width: 450px;">
                <!-- Email Field -->
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" 
                           placeholder="Enter your email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password"
                               class="form-control"
                               placeholder="Enter your password"
                               autocomplete="off"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="mb-4">
                    <label class="form-label">Select Your Role</label>
                    <div class="role-selector">
                        <input type="hidden" name="role" value="student" id="login-role-input">
                        <button type="button" class="role-toggle" id="login-role-toggle">
                            <span id="login-role-label">Student/Fresh Graduate</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="role-menu" id="login-role-menu">
                            <button type="button" class="role-option" data-role="student">
                                <i class="fas fa-user-graduate me-2"></i>
                                Student/Fresh Graduate
                            </button>
                            <button type="button" class="role-option" data-role="admin">
                                <i class="fas fa-user-shield me-2"></i>
                                Administrator
                            </button>
                        </div>
                    </div>
                    <small class="help-text">Choose the role that matches your account type</small>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>

                <!-- Forgot Password Link -->
                <div class="text-end mt-3">
                    <a href="/forgot-password" class="text-decoration-none small">
                        <i class="fas fa-key me-1"></i>
                        Forgot Password?
                    </a>
                </div>
            </form>
        <div class="text-center mt-3">
            <span class="text-muted">Don't have an account?</span>
            <a href="/register" class="ms-2">Create Account</a>
        </div>
        <div class="mt-3 text-center">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Secure login with your registered credentials
            </small>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Role Dropdown
            const dropdownToggle = document.getElementById('login-role-toggle');
            const dropdownMenu = document.getElementById('login-role-menu');
            const roleInput = document.getElementById('login-role-input');
            const roleLabel = document.getElementById('login-role-label');

            // Toggle dropdown
            dropdownToggle?.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownMenu?.classList.toggle('show');
            });

            // Handle role selection
            dropdownMenu?.addEventListener('click', function(event) {
                const option = event.target.closest('.role-option');
                if (!option) return;
                
                const role = option.getAttribute('data-role');
                const text = option.textContent.trim();
                roleInput.value = role;
                roleLabel.textContent = text;
                dropdownMenu.classList.remove('show');
                
                // Add visual feedback
                dropdownToggle.style.borderColor = '#4361ee';
                dropdownToggle.style.background = 'rgba(67, 97, 238, 0.05)';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                dropdownMenu?.classList.remove('show');
            });

            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.querySelector('input[name="password"]');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Toggle icon
                    const icon = this.querySelector('i');
                    if (type === 'password') {
                        icon.className = 'fas fa-eye';
                    } else {
                        icon.className = 'fas fa-eye-slash';
                    }
                });
            }

            // Form submission animation
            const form = document.querySelector('.login-form');
            form?.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
                    submitBtn.disabled = true;
                }
            });

            // Add focus effects to form inputs
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>
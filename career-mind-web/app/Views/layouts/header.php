<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Mind | AI Career Guidance</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/app.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2f3d63;
            --primary-soft: #6c7b9c;
            --secondary: #90a4ff;
            --light: #f6f7fb;
            --dark: #1b1d2b;
            --header-height: 70px;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(27, 29, 43, 0.08);
            padding: 0.5rem 0.5rem;
            min-height: var(--header-height);
            box-shadow: none;
        }
        
        .navbar > .container {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark) !important;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .navbar-brand i {
            color: var(--secondary);
        }

        .header-sidebar-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(27, 29, 43, 0.1);
            border-radius: 50px;
            padding: 0.45rem 0.9rem;
            color: var(--dark);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .header-sidebar-btn i {
            font-size: 1rem;
        }

        .header-sidebar-btn span {
            font-size: 0.9rem;
        }
        
        .btn-brand {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid rgba(27, 29, 43, 0.12);
            padding: 0.5rem 1.4rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-brand:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(27, 29, 43, 0.15);
        }
        
        .btn-outline-brand {
            border: 1px solid rgba(27, 29, 43, 0.2);
            color: var(--dark);
            background: transparent;
            padding: 0.5rem 1.4rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-brand:hover {
            background: white;
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .user-dropdown {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(27, 29, 43, 0.08);
            border-radius: 12px;
            padding: 0.35rem;
        }
        
        .user-dropdown .dropdown-toggle {
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            padding: 0.45rem 0.9rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background: rgba(145, 164, 255, 0.25);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            background: white;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 14px;
            margin-top: 12px;
            padding: 0.5rem;
        }
        
        .dropdown-item {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.25s ease;
            color: var(--dark);
        }
        
        .dropdown-item:hover {
            background: rgba(67, 97, 238, 0.12);
            color: var(--primary);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }
        
        .dropdown-divider {
            margin: 0.4rem 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <?php $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH); ?>
        <div class="d-flex align-items-center">

            <a class="navbar-brand" href="/">
                <i class="fas fa-brain"></i>
                <span>Career</span>Mind
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($currentPath === '/dashboard'): ?>
                    <a class="btn btn-outline-brand" href="/profile">
                        <i class="fas fa-id-card me-2"></i>Profile
                    </a>
                    <a class="btn btn-brand" href="/logout">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                <?php else: ?>
                    <!-- User Dropdown -->
                    <div class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down ms-1" style="font-size: 0.8rem;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item text-danger" href="/logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Guest Navigation -->
                <a class="btn btn-outline-brand me-2" href="/login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
                <a class="btn btn-brand" href="/register">
                    <i class="fas fa-user-plus me-2"></i>Get Started
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const headerSidebarBtn = document.getElementById('headerSidebarToggle');
        const dashboardToggle = document.getElementById('sidebarToggle');
        headerSidebarBtn?.addEventListener('click', function() {
            dashboardToggle?.click();
        });
    });
</script>

<div class="container py-4">
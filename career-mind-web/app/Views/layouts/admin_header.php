<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Mind | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #1f2a44;
            --admin-secondary: #4b6cb7;
            --admin-light: #f5f7ff;
            --admin-dark: #0f172a;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--admin-light);
        }

        .admin-navbar {
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .admin-brand {
            font-weight: 700;
            color: var(--admin-dark) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-brand i {
            color: var(--admin-secondary);
        }

        .admin-nav-link {
            color: var(--admin-dark) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .admin-nav-link:hover {
            background: rgba(75, 108, 183, 0.12);
            color: var(--admin-primary) !important;
        }

        .admin-user {
            background: rgba(75, 108, 183, 0.1);
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-weight: 600;
            color: var(--admin-dark);
        }

        .admin-user i {
            color: var(--admin-secondary);
        }

        .admin-dropdown .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
            border: none;
        }

        .admin-dropdown .dropdown-item {
            font-weight: 500;
            border-radius: 8px;
            padding: 0.55rem 1rem;
        }

        .admin-dropdown .dropdown-item:hover {
            background: rgba(75, 108, 183, 0.12);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg admin-navbar sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand admin-brand" href="/admin/dashboard">
            <i class="fas fa-shield-alt"></i>
            Admin Console
        </a>
        <div class="d-flex align-items-center gap-2">
            <a class="admin-nav-link" href="/admin/dashboard">Dashboard</a>
            <a class="admin-nav-link" href="/admin/users">Users</a>
            <a class="admin-nav-link" href="/admin/careers">Careers & Jobs</a>
            <a class="admin-nav-link" href="/admin/cv-analyses">CV Analyses</a>
            <a class="admin-nav-link" href="/admin/datasets">Datasets</a>
            <a class="admin-nav-link" href="/admin/system">System</a>
        </div>
        <a class="btn btn-outline-danger btn-sm" href="/logout">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
    </div>
</nav>

<div class="container-fluid px-4 py-4">

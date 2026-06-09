<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    public function showLogin(): void
    {
        $this->view('auth/login', [], false);
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [], false);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $requestedRole = $_POST['role'] ?? 'student';

        $allowedRoles = ['student', 'admin'];
        $role = in_array($requestedRole, $allowedRoles, true) ? $requestedRole : 'student';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // The session role is taken from the DB, never the form — so the role
            // toggle can't grant admin. If someone picks "Admin" on a non-admin
            // account, reject it rather than silently logging them in as a student.
            if ($role === 'admin' && $user['role'] !== 'admin') {
                $this->view('auth/login', ['error' => 'This account does not have admin access.'], false);
                return;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] === 'student') {
                $_SESSION['dashboard_hide_results'] = true;
            } else {
                unset($_SESSION['dashboard_hide_results']);
            }

            $this->redirect($user['role'] === 'admin' ? '/admin/dashboard' : '/dashboard');
        }

        $this->view('auth/login', ['error' => 'Invalid credentials.'], false);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Self-registration always creates a regular student. Admin access is never
        // self-assignable — an existing admin must promote a user via /admin/users/role.
        $role = 'student';

        if ($name === '' || $email === '' || $password === '') {
            $this->view('auth/register', ['error' => 'All fields are required.'], false);
            return;
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $this->view('auth/register', ['error' => 'Email already registered.'], false);
            return;
        }

        $userId = $userModel->create($name, $email, $password, $role);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['dashboard_hide_results'] = true;

        $this->redirect('/profile');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }
}

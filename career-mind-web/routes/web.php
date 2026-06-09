<?php

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\AdminController;

return [
    ['method' => 'GET', 'path' => '/', 'controller' => HomeController::class, 'action' => 'index'],
    ['method' => 'GET', 'path' => '/login', 'controller' => AuthController::class, 'action' => 'showLogin'],
    ['method' => 'POST', 'path' => '/login', 'controller' => AuthController::class, 'action' => 'login'],
    ['method' => 'GET', 'path' => '/register', 'controller' => AuthController::class, 'action' => 'showRegister'],
    ['method' => 'POST', 'path' => '/register', 'controller' => AuthController::class, 'action' => 'register'],
    ['method' => 'GET', 'path' => '/logout', 'controller' => AuthController::class, 'action' => 'logout'],

    ['method' => 'GET', 'path' => '/dashboard', 'controller' => ProfileController::class, 'action' => 'dashboard'],
    ['method' => 'POST', 'path' => '/dashboard/clear-results', 'controller' => ProfileController::class, 'action' => 'clearDashboardResults'],
    ['method' => 'GET', 'path' => '/admin/dashboard', 'controller' => AdminController::class, 'action' => 'dashboard'],
    ['method' => 'GET', 'path' => '/admin/users', 'controller' => AdminController::class, 'action' => 'users'],
    ['method' => 'POST', 'path' => '/admin/users/role', 'controller' => AdminController::class, 'action' => 'updateUserRole'],
    ['method' => 'POST', 'path' => '/admin/users/delete', 'controller' => AdminController::class, 'action' => 'deleteUser'],
    ['method' => 'GET', 'path' => '/admin/datasets', 'controller' => AdminController::class, 'action' => 'datasets'],
    ['method' => 'POST', 'path' => '/admin/datasets/upload', 'controller' => AdminController::class, 'action' => 'uploadDataset'],
    ['method' => 'GET', 'path' => '/admin/system', 'controller' => AdminController::class, 'action' => 'system'],
    ['method' => 'GET', 'path' => '/admin/cv-analyses', 'controller' => AdminController::class, 'action' => 'cvAnalyses'],
    ['method' => 'GET', 'path' => '/admin/cv-analyses/export', 'controller' => AdminController::class, 'action' => 'exportCvAnalyses'],
    ['method' => 'GET', 'path' => '/admin/careers', 'controller' => AdminController::class, 'action' => 'careers'],
    ['method' => 'POST', 'path' => '/admin/careers/add', 'controller' => AdminController::class, 'action' => 'addCareer'],
    ['method' => 'POST', 'path' => '/admin/careers/delete', 'controller' => AdminController::class, 'action' => 'deleteCareer'],
    ['method' => 'POST', 'path' => '/admin/jobs/add', 'controller' => AdminController::class, 'action' => 'addJob'],
    ['method' => 'POST', 'path' => '/admin/jobs/delete', 'controller' => AdminController::class, 'action' => 'deleteJob'],
    ['method' => 'GET', 'path' => '/profile', 'controller' => ProfileController::class, 'action' => 'editProfile'],
    ['method' => 'POST', 'path' => '/profile', 'controller' => ProfileController::class, 'action' => 'updateProfile'],
    ['method' => 'POST', 'path' => '/cv/upload', 'controller' => ProfileController::class, 'action' => 'uploadCv'],
    ['method' => 'POST', 'path' => '/recommendations', 'controller' => ProfileController::class, 'action' => 'requestRecommendations'],
];

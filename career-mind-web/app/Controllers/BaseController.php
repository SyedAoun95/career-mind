<?php

namespace App\Controllers;

class BaseController
{
    protected function view(string $view, array $data = [], bool $withLayout = true): void
    {
        extract($data);
        if ($withLayout) {
            require __DIR__ . '/../Views/layouts/header.php';
        }
        require __DIR__ . '/../Views/' . $view . '.php';
        if ($withLayout) {
            require __DIR__ . '/../Views/layouts/footer.php';
        }
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function isAjaxRequest(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }
}

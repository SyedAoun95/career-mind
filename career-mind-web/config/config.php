<?php

// Configuration reads from environment variables when present (used by Docker),
// and otherwise falls back to the local-development defaults below. This lets the
// same code run via ./start.sh locally and via `docker compose up` unchanged.
$env = static function (string $key, string $default): string {
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
};

return [
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => $env('DB_PORT', '3306'),
        'name' => $env('DB_NAME', 'career_mind'),
        'user' => $env('DB_USER', 'root'),
        // Empty string is a valid local password, so read it directly rather than
        // through the helper (which treats '' as "use default").
        'pass' => getenv('DB_PASS') === false ? '' : getenv('DB_PASS'),
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => $env('APP_BASE_URL', ''),
    ],
    'ai' => [
        // Use 127.0.0.1 (not "localhost") so PHP's curl always connects over IPv4.
        // "localhost" can resolve to IPv6 ::1 first, which Flask isn't listening on,
        // causing intermittent connection failures/timeouts on the /predict call.
        // In Docker this is overridden to http://ai:5001 via the AI_BASE_URL env var.
        'base_url' => $env('AI_BASE_URL', 'http://127.0.0.1:5001'),
    ],
];

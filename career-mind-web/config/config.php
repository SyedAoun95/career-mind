<?php

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'career_mind',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '',
    ],
    'ai' => [
        // Use 127.0.0.1 (not "localhost") so PHP's curl always connects over IPv4.
        // "localhost" can resolve to IPv6 ::1 first, which Flask isn't listening on,
        // causing intermittent connection failures/timeouts on the /predict call.
        'base_url' => 'http://127.0.0.1:5001',
    ],
];

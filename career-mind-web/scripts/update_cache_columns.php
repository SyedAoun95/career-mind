<?php
$cfg = include __DIR__ . '/../config/config.php';
$db = $cfg['db'];
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']);
$pdo = new PDO($dsn, $db['user'], $db['pass']);
function columnExists(PDO $pdo, string $column): bool
{
    $stmt = $pdo->prepare('SHOW COLUMNS FROM career_prediction_cache LIKE :column');
    $stmt->execute([':column' => $column]);
    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
}

$columnsToAdd = [
    'confidence' => 'DECIMAL(5,4) NULL',
    'status' => "VARCHAR(32) NOT NULL DEFAULT 'live'",
    'last_refreshed' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
];

foreach ($columnsToAdd as $column => $definition) {
    if (!columnExists($pdo, $column)) {
        $pdo->exec("ALTER TABLE career_prediction_cache ADD COLUMN $column $definition");
    }
}
echo "Updated prediction cache columns.\n";

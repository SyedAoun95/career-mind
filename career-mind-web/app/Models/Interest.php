<?php

namespace App\Models;

class Interest
{
    public function syncUserInterests(int $userId, string $interests): void
    {
        $names = $this->normalizeList($interests);
        $interestIds = $this->ensureInterests($names);

        Database::connection()->prepare('DELETE FROM user_interests WHERE user_id = ?')->execute([$userId]);

        $stmt = Database::connection()->prepare('INSERT INTO user_interests (user_id, interest_id) VALUES (?, ?)');
        foreach ($interestIds as $id) {
            $stmt->execute([$userId, $id]);
        }
    }

    public function getUserInterests(int $userId): array
    {
        $sql = 'SELECT i.interest_name FROM interests i INNER JOIN user_interests ui ON ui.interest_id = i.id WHERE ui.user_id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(), 'interest_name');
    }

    private function ensureInterests(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $stmt = Database::connection()->prepare('SELECT id FROM interests WHERE interest_name = ? LIMIT 1');
            $stmt->execute([$name]);
            $existing = $stmt->fetch();

            if ($existing) {
                $ids[] = (int)$existing['id'];
                continue;
            }

            $insert = Database::connection()->prepare('INSERT INTO interests (interest_name) VALUES (?)');
            $insert->execute([$name]);
            $ids[] = (int)Database::connection()->lastInsertId();
        }
        return $ids;
    }

    private function normalizeList(string $input): array
    {
        $parts = array_filter(array_map('trim', explode(',', $input)));
        return array_values(array_unique($parts));
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM interests';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}

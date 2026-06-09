<?php

namespace App\Models;

class Skill
{
    public function syncUserSkills(int $userId, string $skills): void
    {
        $names = $this->normalizeList($skills);
        $skillIds = $this->ensureSkills($names);

        Database::connection()->prepare('DELETE FROM user_skills WHERE user_id = ?')->execute([$userId]);

        $stmt = Database::connection()->prepare('INSERT INTO user_skills (user_id, skill_id) VALUES (?, ?)');
        foreach ($skillIds as $id) {
            $stmt->execute([$userId, $id]);
        }
    }

    public function getUserSkills(int $userId): array
    {
        $sql = 'SELECT s.skill_name FROM skills s INNER JOIN user_skills us ON us.skill_id = s.id WHERE us.user_id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(), 'skill_name');
    }

    private function ensureSkills(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $stmt = Database::connection()->prepare('SELECT id FROM skills WHERE skill_name = ? LIMIT 1');
            $stmt->execute([$name]);
            $existing = $stmt->fetch();

            if ($existing) {
                $ids[] = (int)$existing['id'];
                continue;
            }

            $insert = Database::connection()->prepare('INSERT INTO skills (skill_name) VALUES (?)');
            $insert->execute([$name]);
            $ids[] = (int)Database::connection()->lastInsertId();
        }
        return $ids;
    }

    public function createIfMissing(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            return;
        }

        $stmt = Database::connection()->prepare('SELECT id FROM skills WHERE skill_name = ? LIMIT 1');
        $stmt->execute([$name]);
        $existing = $stmt->fetch();
        if ($existing) {
            return;
        }

        $insert = Database::connection()->prepare('INSERT INTO skills (skill_name) VALUES (?)');
        $insert->execute([$name]);
    }

    private function normalizeList(string $input): array
    {
        $parts = array_map(function ($part) {
            // Strip stray quotes/whitespace so "digital growth, analytics" no longer
            // becomes the broken rows ["digital growth] and [analytics"].
            return trim($part, " \t\n\r\0\x0B\"'");
        }, explode(',', $input));

        $parts = array_filter($parts, function ($part) {
            // Drop empties, single characters, and sentinel non-skills.
            return strlen($part) >= 2 && !in_array(strtolower($part), ['noskills', 'none', 'n/a'], true);
        });

        return array_values(array_unique($parts));
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM skills';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    /**
     * All catalog skill names, alphabetically — used to populate the
     * autocomplete suggestions on the profile form.
     */
    public function allNames(): array
    {
        $sql = 'SELECT skill_name FROM skills ORDER BY skill_name ASC';
        $stmt = Database::connection()->query($sql);
        return array_column($stmt->fetchAll(), 'skill_name');
    }
}

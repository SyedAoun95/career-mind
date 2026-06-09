<?php

namespace App\Models;

class Career
{
    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM careers';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();

        return (int)($result['total'] ?? 0);
    }

    public function all(): array
    {
        $sql = 'SELECT * FROM careers ORDER BY id DESC';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetchAll();

        return $result ?: [];
    }

    public function create(string $title, string $description, string $requiredSkills): void
    {
        $sql = 'INSERT INTO careers (title, description, required_skills) VALUES (?, ?, ?)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$title, $description, $requiredSkills]);
    }

    public function findIdByTitle(string $title): ?int
    {
        $sql = 'SELECT id FROM careers WHERE title = ? LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$title]);
        $result = $stmt->fetch();

        return $result ? (int)$result['id'] : null;
    }

    /** Required skills for a career title, as a lowercased array. */
    public function requiredSkillsFor(string $title): array
    {
        $sql = 'SELECT required_skills FROM careers WHERE title = ? LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$title]);
        $row = $stmt->fetch();
        if (!$row || empty($row['required_skills'])) {
            return [];
        }
        $parts = array_map(fn($s) => strtolower(trim($s)), explode(',', $row['required_skills']));
        return array_values(array_filter($parts));
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM careers WHERE id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
    }
}

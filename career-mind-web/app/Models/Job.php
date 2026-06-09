<?php

namespace App\Models;

class Job
{
    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM jobs';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();

        return (int)($result['total'] ?? 0);
    }

    public function all(): array
    {
        $sql = 'SELECT jobs.*, careers.title AS career_title FROM jobs LEFT JOIN careers ON jobs.career_id = careers.id ORDER BY jobs.id DESC';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetchAll();

        return $result ?: [];
    }

    public function create(?int $careerId, string $title, string $level, string $location, string $requiredSkills): void
    {
        $sql = 'INSERT INTO jobs (career_id, title, level, location, required_skills) VALUES (?, ?, ?, ?, ?)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$careerId, $title, $level, $location, $requiredSkills]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM jobs WHERE id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
    }
}

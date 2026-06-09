<?php

namespace App\Models;

class JobRecommendation
{
    public function clearByUser(int $userId): void
    {
        $sql = 'DELETE FROM job_recommendations WHERE user_id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
    }

    public function createMany(int $userId, array $items): void
    {
        $sql = 'INSERT INTO job_recommendations (user_id, job_title, reason, created_at) VALUES (?, ?, ?, NOW())';
        $stmt = Database::connection()->prepare($sql);

        foreach ($items as $item) {
            $stmt->execute([
                $userId,
                $item['title'] ?? 'Job Recommendation',
                $item['reason'] ?? null,
            ]);
        }
    }

    public function getLatestByUser(int $userId, int $limit = 5): array
    {
        $limit = max(1, (int)$limit);
        $sql = 'SELECT job_title, reason, created_at FROM job_recommendations WHERE user_id = ? ORDER BY created_at DESC LIMIT ' . $limit;
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetchAll();

        return $result ?: [];
    }
}

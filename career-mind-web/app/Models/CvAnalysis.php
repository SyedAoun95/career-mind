<?php

namespace App\Models;

class CvAnalysis
{
    public function create(int $cvId, string $summary, array $missingSkills, array $feedback, ?int $score, array $extractedSkills = []): void
    {
        $sql = 'INSERT INTO cv_analyses (cv_id, summary, missing_skills, feedback, extracted_skills, score, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            $cvId,
            $summary,
            json_encode($missingSkills),
            json_encode($feedback),
            json_encode(array_values($extractedSkills)),
            $score,
        ]);
    }

    /**
     * Skills extracted from the user's most recent CV — used to drive a
     * CV-specific career prediction that survives page reloads and re-logins.
     */
    public function getLatestExtractedSkills(int $userId): array
    {
        $sql = 'SELECT ca.extracted_skills FROM cv_analyses ca JOIN cv_files cf ON ca.cv_id = cf.id WHERE cf.user_id = ? ORDER BY ca.created_at DESC LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        if (!$row || empty($row['extracted_skills'])) {
            return [];
        }
        $decoded = json_decode($row['extracted_skills'], true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getLatestByUser(int $userId): ?array
    {
        $sql = 'SELECT ca.*, cf.file_name, cf.uploaded_at FROM cv_analyses ca JOIN cv_files cf ON ca.cv_id = cf.id WHERE cf.user_id = ? ORDER BY ca.created_at DESC LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        $result['missing_skills'] = json_decode($result['missing_skills'] ?? '[]', true) ?? [];
        $result['feedback'] = json_decode($result['feedback'] ?? '[]', true) ?? [];

        return $result;
    }

    public function getAllWithUser(int $limit = 200): array
    {
        $limit = max(1, (int)$limit);
        $sql = 'SELECT ca.*, cf.file_name, cf.uploaded_at, u.name AS user_name, u.email AS user_email '
            . 'FROM cv_analyses ca '
            . 'JOIN cv_files cf ON ca.cv_id = cf.id '
            . 'JOIN users u ON cf.user_id = u.id '
            . 'ORDER BY ca.created_at DESC LIMIT ' . $limit;
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetchAll();

        if (!$result) {
            return [];
        }

        foreach ($result as &$row) {
            $row['missing_skills'] = json_decode($row['missing_skills'] ?? '[]', true) ?? [];
            $row['feedback'] = json_decode($row['feedback'] ?? '[]', true) ?? [];
        }

        return $result;
    }
}

<?php

namespace App\Models;

class CvFile
{
    public function create(int $userId, string $fileName, string $storedPath): int
    {
        $sql = 'INSERT INTO cv_files (user_id, file_name, stored_path, uploaded_at) VALUES (?, ?, ?, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId, $fileName, $storedPath]);

        return (int)Database::connection()->lastInsertId();
    }

    public function getLatestByUser(int $userId): ?array
    {
        $sql = 'SELECT * FROM cv_files WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}

<?php

namespace App\Models;

class Profile
{
    public function getByUserId(int $userId): ?array
    {
        $sql = 'SELECT * FROM profiles WHERE user_id = ? LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function upsert(int $userId, array $data): void
    {
        $existing = $this->getByUserId($userId);
        if ($existing) {
            $sql = 'UPDATE profiles SET age = ?, education_level = ?, institution = ?, graduation_year = ? WHERE user_id = ?';
            $stmt = Database::connection()->prepare($sql);
            $stmt->execute([
                $data['age'],
                $data['education_level'],
                $data['institution'],
                $data['graduation_year'],
                $userId,
            ]);
            return;
        }

        $sql = 'INSERT INTO profiles (user_id, age, education_level, institution, graduation_year) VALUES (?, ?, ?, ?, ?)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([
            $userId,
            $data['age'],
            $data['education_level'],
            $data['institution'],
            $data['graduation_year'],
        ]);
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM profiles';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}

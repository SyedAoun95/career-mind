<?php

namespace App\Models;

use App\Models\Database;
use PDO;

class CareerPredictionCache
{
    public function upsert(int $userId, array $prediction, ?float $confidence = null, string $status = 'live'): void
    {
        $db = Database::connection();
        $stmt = $db->prepare(
            'INSERT INTO career_prediction_cache (user_id, prediction_data, confidence, status, last_refreshed)
            VALUES (:user_id, :prediction_data, :confidence, :status, NOW())
            ON DUPLICATE KEY UPDATE
                prediction_data = VALUES(prediction_data),
                confidence = VALUES(confidence),
                status = VALUES(status),
                last_refreshed = NOW()'
        );

        $stmt->execute([
            ':user_id' => $userId,
            ':prediction_data' => json_encode($prediction, JSON_UNESCAPED_UNICODE),
            ':confidence' => $confidence,
            ':status' => $status,
        ]);
    }

    public function getByUser(int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT prediction_data FROM career_prediction_cache WHERE user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || empty($row['prediction_data'])) {
            return null;
        }

        $decoded = json_decode($row['prediction_data'], true);
        return is_array($decoded) ? $decoded : null;
    }

    public function getMetadata(int $userId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT confidence, status, last_refreshed FROM career_prediction_cache WHERE user_id = :user_id'
        );
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return ['confidence' => null, 'status' => null, 'last_refreshed' => null];
        }

        return [
            'confidence' => $row['confidence'] !== null ? (float)$row['confidence'] : null,
            'status' => $row['status'],
            'last_refreshed' => $row['last_refreshed'],
        ];
    }
}

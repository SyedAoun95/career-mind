<?php

namespace App\Models;

class User
{
    public function all(): array
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetchAll();

        return $result ?: [];
    }

    public function updateRole(int $id, string $role): void
    {
        $sql = 'UPDATE users SET role = ? WHERE id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$role, $id]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM users WHERE id = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
    }

    public function create(string $name, string $email, string $password, string $role): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = 'INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$name, $email, $hash, $role]);

        return (int)Database::connection()->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM users WHERE id = ? LIMIT 1';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) as total FROM users';
        $stmt = Database::connection()->query($sql);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }

    public function countByRole(string $role): int
    {
        $sql = 'SELECT COUNT(*) as total FROM users WHERE role = ?';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute([$role]);
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}

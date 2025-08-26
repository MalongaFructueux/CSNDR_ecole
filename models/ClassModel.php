<?php
require_once __DIR__ . '/../config/database.php';

class ClassModel {
    public static function getAll(): array {
        return db()->query('SELECT * FROM classes ORDER BY nom')->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM classes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $nom): int {
        $now = date('Y-m-d H:i:s');
        $stmt = db()->prepare('INSERT INTO classes (nom, created_at, updated_at) VALUES (?,?,?)');
        $stmt->execute([$nom, $now, $now]);
        return (int)db()->lastInsertId();
    }

    public static function update(int $id, string $nom): void {
        $stmt = db()->prepare('UPDATE classes SET nom = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$nom, date('Y-m-d H:i:s'), $id]);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM classes WHERE id = ?');
        $stmt->execute([$id]);
    }
}

<?php
require_once __DIR__ . '/../config/database.php';

class Homework {
    public static function getAll(): array {
        $sql = 'SELECT h.id,
                       h.titre AS title,
                       h.description,
                       h.date_limite AS due_at,
                       h.professeur_id AS created_by,
                       h.classe_id,
                       u.nom AS creator_nom, u.prenom AS creator_prenom,
                       c.nom AS class_name,
                       h.created_at, h.updated_at
                FROM devoirs h
                LEFT JOIN users u ON u.id = h.professeur_id
                LEFT JOIN classes c ON c.id = h.classe_id
                ORDER BY h.date_limite ASC, h.created_at DESC';
        return db()->query($sql)->fetchAll();
    }

    public static function getByClass(int $classe_id): array {
        $stmt = db()->prepare('SELECT id, titre AS title, description, date_limite AS due_at, professeur_id AS created_by, classe_id, created_at, updated_at FROM devoirs WHERE classe_id = ? ORDER BY date_limite ASC');
        $stmt->execute([$classe_id]);
        return $stmt->fetchAll();
    }

    public static function getByParent(int $parent_id): array {
        // Homeworks for any class where the parent has at least one child
        $sql = 'SELECT h.id,
                       h.titre AS title,
                       h.description,
                       h.date_limite AS due_at,
                       h.professeur_id AS created_by,
                       h.classe_id,
                       c.nom AS class_name,
                       u.nom AS creator_nom, u.prenom AS creator_prenom,
                       h.created_at, h.updated_at
                FROM devoirs h
                JOIN classes c ON c.id = h.classe_id
                LEFT JOIN users u ON u.id = h.professeur_id
                WHERE h.classe_id IN (
                    SELECT DISTINCT classe_id FROM users WHERE parent_id = ? AND classe_id IS NOT NULL
                )
                ORDER BY h.date_limite ASC, h.created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = db()->prepare('SELECT id, titre AS title, description, date_limite AS due_at, professeur_id AS created_by, classe_id, created_at, updated_at FROM devoirs WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $d): int {
        $now = date('Y-m-d H:i:s');
        $stmt = db()->prepare('INSERT INTO devoirs (titre, description, date_limite, professeur_id, classe_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$d['title'], $d['description'], $d['due_at'], $d['created_by'], $d['classe_id'], $now, $now]);
        return (int)db()->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $stmt = db()->prepare('UPDATE devoirs SET titre = ?, description = ?, date_limite = ?, classe_id = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$d['title'], $d['description'], $d['due_at'], $d['classe_id'], date('Y-m-d H:i:s'), $id]);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM devoirs WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function validate(array $d): array {
        $e = [];
        if (($d['title'] ?? '') === '') $e[] = 'Titre requis';
        if (($d['due_at'] ?? '') === '') $e[] = 'Date d\'échéance requise';
        if (empty($d['classe_id'])) $e[] = 'Classe requise';
        return $e;
    }
}

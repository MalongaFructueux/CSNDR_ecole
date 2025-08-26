<?php
require_once __DIR__ . '/../config/database.php';

class Event {
    private static function dateColumn(): string {
        static $col = null;
        if ($col !== null) return $col;
        $candidates = ['date', 'start_at', 'date_event', 'date_evenement'];
        foreach ($candidates as $cand) {
            $like = db()->quote($cand);
            $sql = "SHOW COLUMNS FROM evenements LIKE $like";
            $stmt = db()->query($sql);
            if ($stmt && $stmt->fetch()) { $col = $cand; return $col; }
        }
        // Fallback to created_at if none found
        $col = 'created_at';
        return $col;
    }

    public static function getAll(): array {
        $col = self::dateColumn();
        $sql = "SELECT e.id,
                       e.titre AS title,
                       e.description,
                       e.`$col` AS start_at,
                       NULL AS end_at,
                       e.auteur_id AS created_by,
                       u.nom AS creator_nom,
                       u.prenom AS creator_prenom,
                       e.created_at, e.updated_at
                FROM evenements e
                JOIN users u ON u.id = e.auteur_id
                ORDER BY e.`$col` DESC";
        return db()->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array {
        $col = self::dateColumn();
        $stmt = db()->prepare("SELECT id, titre AS title, description, `$col` AS start_at, NULL AS end_at, auteur_id AS created_by, created_at, updated_at FROM evenements WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $d): int {
        $now = date('Y-m-d H:i:s');
        $col = self::dateColumn();
        // Build columns/values conditionally to avoid duplicating created_at
        $cols = ['titre', 'description', 'auteur_id', 'created_at', 'updated_at'];
        $vals = [$d['title'], $d['description'], $d['created_by'], $now, $now];
        if (!in_array($col, ['created_at','updated_at'], true)) {
            array_splice($cols, 2, 0, [$col]); // insert before auteur_id
            array_splice($vals, 2, 0, [$d['start_at']]);
        }
        $colsSql = implode(', ', array_map(fn($c) => "`$c`", $cols));
        $placeholders = rtrim(str_repeat('?,', count($vals)), ',');
        $sql = "INSERT INTO evenements ($colsSql) VALUES ($placeholders)";
        $stmt = db()->prepare($sql);
        $stmt->execute($vals);
        return (int)db()->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $col = self::dateColumn();
        $sets = ['`titre` = ?', '`description` = ?'];
        $vals = [$d['title'], $d['description']];
        if (!in_array($col, ['created_at','updated_at'], true)) {
            $sets[] = "`$col` = ?";
            $vals[] = $d['start_at'];
        }
        $sets[] = '`updated_at` = ?';
        $vals[] = date('Y-m-d H:i:s');
        $vals[] = $id;
        $sql = 'UPDATE evenements SET ' . implode(', ', $sets) . ' WHERE id = ?';
        $stmt = db()->prepare($sql);
        $stmt->execute($vals);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM evenements WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function validate(array $d): array {
        $errors = [];
        if (trim($d['title'] ?? '') === '') $errors[] = 'Titre requis';
        if (trim($d['start_at'] ?? '') === '') $errors[] = 'Date de début requise';
        return $errors;
    }
}

<?php
require_once __DIR__ . '/../config/database.php';

class Note {
    public static function getByStudent(int $eleve_id): array {
        $stmt = db()->prepare('SELECT n.*, u2.nom AS prof_nom, u2.prenom AS prof_prenom FROM notes n JOIN users u2 ON u2.id = n.professeur_id WHERE n.eleve_id = ? ORDER BY n.created_at DESC');
        $stmt->execute([$eleve_id]);
        return $stmt->fetchAll();
    }

    public static function getByParent(int $parent_id): array {
        $sql = 'SELECT n.*, child.nom AS eleve_nom, child.prenom AS eleve_prenom, t.nom AS prof_nom, t.prenom AS prof_prenom
                FROM notes n
                JOIN users child ON child.id = n.eleve_id
                JOIN users t ON t.id = n.professeur_id
                WHERE child.parent_id = ?
                ORDER BY n.created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$parent_id]);
        return $stmt->fetchAll();
    }

    public static function getByTeacher(int $professeur_id): array {
        $sql = 'SELECT n.*, s.nom AS eleve_nom, s.prenom AS eleve_prenom FROM notes n JOIN users s ON s.id = n.eleve_id WHERE n.professeur_id = ? ORDER BY n.created_at DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$professeur_id]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM notes WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $d): int {
        $now = date('Y-m-d H:i:s');
        $stmt = db()->prepare('INSERT INTO notes (eleve_id, matiere, note, type, professeur_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$d['eleve_id'], $d['matiere'], $d['note'], $d['type'], $d['professeur_id'], $now, $now]);
        return (int)db()->lastInsertId();
    }

    public static function update(int $id, array $d): void {
        $stmt = db()->prepare('UPDATE notes SET eleve_id = ?, matiere = ?, note = ?, type = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$d['eleve_id'], $d['matiere'], $d['note'], $d['type'], date('Y-m-d H:i:s'), $id]);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM notes WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function validate(array $d): array {
        $errors = [];
        if ($d['eleve_id'] <= 0) $errors[] = "Étudiant requis";
        if ($d['matiere'] === '') $errors[] = 'Matière requise';
        if (!is_numeric($d['note']) || $d['note'] < 0 || $d['note'] > 20) $errors[] = 'Note entre 0 et 20';
        if (!in_array($d['type'], ['Devoir','Examen','Participation'], true)) $errors[] = 'Type invalide';
        return $errors;
    }
}

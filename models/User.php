<?php
require_once __DIR__ . '/../config/database.php';

class User {
    public static function findByEmail(string $email): ?array {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function emailExists(string $email): bool {
        $stmt = db()->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return (bool)$stmt->fetchColumn();
    }

    public static function create(array $data): int {
        $now = date('Y-m-d H:i:s');
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = db()->prepare('INSERT INTO users (nom, prenom, email, password, role, classe_id, parent_id, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['nom'], $data['prenom'], $data['email'], $hash, $data['role'], $data['classe_id'], $data['parent_id'], $now, $now
        ]);
        return (int)db()->lastInsertId();
    }

    public static function findById(int $id): ?array {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getAll(?string $role = null): array {
        if ($role) {
            $stmt = db()->prepare('SELECT * FROM users WHERE role = ? ORDER BY nom, prenom');
            $stmt->execute([$role]);
            return $stmt->fetchAll();
        }
        return db()->query('SELECT * FROM users ORDER BY nom, prenom')->fetchAll();
    }

    public static function update(int $id, array $data): void {
        $fields = ['nom = ?', 'prenom = ?', 'email = ?', 'role = ?', 'classe_id = ?', 'parent_id = ?'];
        $params = [$data['nom'], $data['prenom'], $data['email'], $data['role'], $data['classe_id'], $data['parent_id']];
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $fields[] = 'updated_at = ?';
        $params[] = date('Y-m-d H:i:s');
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void {
        $stmt = db()->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function validateRegistration(array $d): array {
        $errors = [];
        if ($d['nom'] === '') $errors[] = 'Nom requis';
        if ($d['prenom'] === '') $errors[] = 'Prénom requis';
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($d['password']) < 6) $errors[] = 'Mot de passe trop court';
        if (!in_array($d['role'], ['eleve','professeur','parent','admin'], true)) $errors[] = 'Rôle invalide';
        return $errors;
    }
}

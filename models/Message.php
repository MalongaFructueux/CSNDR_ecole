<?php
require_once __DIR__ . '/../config/database.php';

class Message {
    public static function send(int $from_id, int $to_id, string $content): int {
        $now = date('Y-m-d H:i:s');
        // Map to schema: expediteur_id, destinataire_id, contenu, date_envoi
        $stmt = db()->prepare('INSERT INTO messages (expediteur_id, destinataire_id, contenu, date_envoi, created_at, updated_at) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$from_id, $to_id, $content, $now, $now, $now]);
        return (int)db()->lastInsertId();
    }

    public static function getInbox(int $user_id): array {
        // Latest message per counterpart
        $sql = "SELECT * FROM (
                  SELECT m.id, m.contenu AS content, m.date_envoi AS created_at,
                         m.expediteur_id AS from_id, m.destinataire_id AS to_id,
                         u1.nom AS from_nom, u1.prenom AS from_prenom, u2.nom AS to_nom, u2.prenom AS to_prenom,
                         CASE WHEN m.expediteur_id = ? THEN m.destinataire_id ELSE m.expediteur_id END AS other_id
                  FROM messages m
                  JOIN users u1 ON u1.id = m.expediteur_id
                  JOIN users u2 ON u2.id = m.destinataire_id
                  WHERE m.expediteur_id = ? OR m.destinataire_id = ?
                  ORDER BY m.date_envoi DESC
                ) t
                GROUP BY other_id
                ORDER BY created_at DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id]);
        return $stmt->fetchAll();
    }

    public static function getThread(int $user_id, int $other_id): array {
        $sql = 'SELECT m.id, m.contenu AS content, m.date_envoi AS created_at,
                       m.expediteur_id AS from_id, m.destinataire_id AS to_id,
                       u1.nom AS from_nom, u1.prenom AS from_prenom
                FROM messages m
                JOIN users u1 ON u1.id = m.expediteur_id
                WHERE (m.expediteur_id = ? AND m.destinataire_id = ?) OR (m.expediteur_id = ? AND m.destinataire_id = ?)
                ORDER BY m.date_envoi ASC';
        $stmt = db()->prepare($sql);
        $stmt->execute([$user_id, $other_id, $other_id, $user_id]);
        return $stmt->fetchAll();
    }
}

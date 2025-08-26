<?php

// Script direct pour créer la table notes
try {
    // Essayer différentes configurations de DB
    $configs = [
        ['host' => 'localhost', 'db' => 'csndr_db', 'user' => 'root', 'pass' => ''],
        ['host' => '127.0.0.1', 'db' => 'csndr_db', 'user' => 'root', 'pass' => ''],
        ['host' => 'localhost', 'db' => 'csndr_ecole', 'user' => 'root', 'pass' => ''],
    ];
    
    $pdo = null;
    foreach ($configs as $config) {
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['db']}";
            $pdo = new PDO($dsn, $config['user'], $config['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connexion réussie: {$config['host']}/{$config['db']}\n";
            break;
        } catch (Exception $e) {
            continue;
        }
    }
    
    if (!$pdo) {
        throw new Exception("Aucune connexion DB réussie");
    }
    
    // Supprimer la table si elle existe
    $pdo->exec("DROP TABLE IF EXISTS notes");
    echo "Table notes supprimée si elle existait\n";
    
    // Créer la table notes
    $createTable = "
    CREATE TABLE notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note DECIMAL(4,2) NOT NULL,
        matiere VARCHAR(100) NOT NULL,
        commentaire TEXT,
        eleve_id INT NOT NULL,
        professeur_id INT NOT NULL,
        coefficient DECIMAL(3,2) DEFAULT 1.0,
        date DATE,
        type_evaluation VARCHAR(100) DEFAULT 'Contrôle',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($createTable);
    echo "Table notes créée avec succès\n";
    
    // Insérer des données de test
    $insertData = "
    INSERT INTO notes (note, matiere, commentaire, eleve_id, professeur_id, coefficient, date, type_evaluation) VALUES
    (15.5, 'Mathématiques', 'Très bon travail', 1, 2, 2.0, CURDATE(), 'Contrôle'),
    (12.0, 'Français', 'Peut mieux faire', 1, 2, 1.5, CURDATE(), 'Devoir'),
    (18.0, 'Sciences', 'Excellent !', 1, 2, 1.0, CURDATE(), 'Exposé')
    ";
    
    $pdo->exec($insertData);
    echo "Données de test insérées\n";
    
    // Vérifier
    $stmt = $pdo->query("SELECT COUNT(*) FROM notes");
    $count = $stmt->fetchColumn();
    echo "Nombre de notes: $count\n";
    
    echo "=== SUCCÈS ===\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

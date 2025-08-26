<?php

// Test simple de connexion DB et table notes
echo "=== TEST CONNEXION BASE DE DONNÉES ===\n";

try {
    // Test connexion directe
    $pdo = new PDO('mysql:host=localhost;dbname=csndr_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connexion DB réussie\n";
    
    // Vérifier table notes
    $stmt = $pdo->query("SHOW TABLES LIKE 'notes'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Table 'notes' existe\n";
        
        // Compter les notes
        $stmt = $pdo->query("SELECT COUNT(*) FROM notes");
        $count = $stmt->fetchColumn();
        echo "✓ Nombre de notes: $count\n";
        
        // Test insertion simple
        $stmt = $pdo->prepare("INSERT INTO notes (note, matiere, eleve_id, professeur_id, date) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([15.0, 'Test Direct', 1, 2, date('Y-m-d')]);
        
        if ($result) {
            echo "✓ Insertion test réussie\n";
            $lastId = $pdo->lastInsertId();
            echo "✓ ID créé: $lastId\n";
            
            // Test suppression
            $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
            $stmt->execute([$lastId]);
            echo "✓ Suppression test réussie\n";
        }
        
    } else {
        echo "✗ Table 'notes' n'existe pas\n";
        
        // Créer la table
        $createSql = "
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
        
        $pdo->exec($createSql);
        echo "✓ Table 'notes' créée\n";
        
        // Insérer données test
        $insertSql = "
        INSERT INTO notes (note, matiere, commentaire, eleve_id, professeur_id, date) VALUES
        (15.5, 'Mathématiques', 'Bon travail', 1, 2, CURDATE()),
        (12.0, 'Français', 'Peut mieux faire', 1, 2, CURDATE()),
        (18.0, 'Sciences', 'Excellent', 1, 2, CURDATE())
        ";
        
        $pdo->exec($insertSql);
        echo "✓ Données test insérées\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n";
    
    // Essayer d'autres configurations
    $configs = [
        ['host' => '127.0.0.1', 'db' => 'csndr_db'],
        ['host' => 'localhost', 'db' => 'csndr_ecole'],
        ['host' => '127.0.0.1', 'db' => 'csndr_ecole'],
    ];
    
    foreach ($configs as $config) {
        try {
            $pdo = new PDO("mysql:host={$config['host']};dbname={$config['db']}", 'root', '');
            echo "✓ Connexion alternative réussie: {$config['host']}/{$config['db']}\n";
            break;
        } catch (Exception $e2) {
            echo "✗ Échec: {$config['host']}/{$config['db']}\n";
        }
    }
}

echo "\n=== FIN TEST ===\n";

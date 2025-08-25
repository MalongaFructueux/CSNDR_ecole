<?php

// Test de la table notes
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
            echo "=== CONNEXION RÉUSSIE ===\n";
            echo "Host: {$config['host']}, DB: {$config['db']}\n";
            break;
        } catch (Exception $e) {
            echo "Échec: {$config['host']}/{$config['db']} - " . $e->getMessage() . "\n";
        }
    }
    
    if (!$pdo) {
        throw new Exception("Aucune connexion DB réussie");
    }
    
    // Test de la table notes
    echo "\n=== TEST TABLE NOTES ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'notes'");
    if ($stmt->rowCount() == 0) {
        echo "ERREUR: Table 'notes' n'existe pas\n";
        
        // Lister toutes les tables
        $stmt = $pdo->query("SHOW TABLES");
        echo "Tables disponibles:\n";
        while ($row = $stmt->fetch()) {
            echo "- " . $row[0] . "\n";
        }
        
        // Créer la table notes si elle n'existe pas
        echo "\n=== CRÉATION TABLE NOTES ===\n";
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
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (eleve_id) REFERENCES users(id),
            FOREIGN KEY (professeur_id) REFERENCES users(id)
        )";
        
        try {
            $pdo->exec($createTable);
            echo "Table 'notes' créée avec succès\n";
        } catch (Exception $e) {
            echo "Erreur création table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Table 'notes' existe\n";
        
        // Vérifier la structure
        $stmt = $pdo->query("DESCRIBE notes");
        echo "Structure de la table notes:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']} ({$row['Null']}, {$row['Key']})\n";
        }
        
        // Compter les notes
        $stmt = $pdo->query("SELECT COUNT(*) FROM notes");
        $count = $stmt->fetchColumn();
        echo "Nombre de notes: $count\n";
        
        // Tester une insertion
        echo "\n=== TEST INSERTION ===\n";
        try {
            $stmt = $pdo->prepare("INSERT INTO notes (note, matiere, eleve_id, professeur_id, date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([15.5, 'Test Matière', 1, 2, date('Y-m-d')]);
            echo "Insertion test réussie\n";
            
            // Supprimer le test
            $pdo->exec("DELETE FROM notes WHERE matiere = 'Test Matière'");
            echo "Test nettoyé\n";
        } catch (Exception $e) {
            echo "Erreur insertion: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}

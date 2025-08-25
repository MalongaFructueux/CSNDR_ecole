<?php

echo "=== Configuration directe de la base de données ===\n\n";

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=csndr_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Connexion à la base de données réussie\n";

    // Supprimer la table si elle existe
    $pdo->exec("DROP TABLE IF EXISTS notes");
    echo "✓ Ancienne table notes supprimée\n";

    // Créer la nouvelle table
    $createTable = "
    CREATE TABLE notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        note DECIMAL(4,2) NOT NULL,
        matiere VARCHAR(100) NOT NULL,
        eleve_id INT NOT NULL,
        professeur_id INT NOT NULL,
        commentaire TEXT,
        type_evaluation VARCHAR(100) DEFAULT 'Contrôle',
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_eleve (eleve_id),
        INDEX idx_professeur (professeur_id),
        INDEX idx_matiere (matiere),
        INDEX idx_date (date)
    )";
    
    $pdo->exec($createTable);
    echo "✓ Table notes créée avec succès\n";

    // Insérer des données de test
    $insertData = "
    INSERT INTO notes (note, matiere, eleve_id, professeur_id, commentaire, type_evaluation, date) VALUES
    (15.5, 'Mathématiques', 1, 2, 'Bon travail', 'Contrôle', '2024-01-15'),
    (12.0, 'Français', 1, 3, 'Peut mieux faire', 'Devoir', '2024-01-16'),
    (18.0, 'Histoire', 1, 2, 'Excellent', 'Exposé', '2024-01-17'),
    (14.5, 'Mathématiques', 2, 2, 'Correct', 'Contrôle', '2024-01-15'),
    (16.0, 'Sciences', 2, 3, 'Très bien', 'TP', '2024-01-18')
    ";
    
    $pdo->exec($insertData);
    echo "✓ Données de test insérées\n";

    // Vérifier le nombre d'enregistrements
    $stmt = $pdo->query("SELECT COUNT(*) FROM notes");
    $count = $stmt->fetchColumn();
    echo "✓ Nombre de notes dans la table: $count\n";

    // Tester une insertion simple
    $stmt = $pdo->prepare("INSERT INTO notes (note, matiere, eleve_id, professeur_id, commentaire, type_evaluation, date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        17.5,
        'Test API',
        1,
        2,
        'Test insertion directe',
        'Test',
        date('Y-m-d'),
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s')
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "✓ Test insertion réussi - ID: $newId\n";
        
        // Supprimer le test
        $pdo->exec("DELETE FROM notes WHERE id = $newId");
        echo "✓ Test nettoyé\n";
    }

    echo "\n=== Configuration terminée avec succès ===\n";
    echo "La table 'notes' est prête pour les opérations CRUD\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Ligne: " . $e->getLine() . "\n";
    echo "Fichier: " . $e->getFile() . "\n";
}

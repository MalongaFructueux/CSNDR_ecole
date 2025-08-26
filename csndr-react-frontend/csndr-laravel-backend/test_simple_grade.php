<?php

/**
 * Test simple pour diagnostiquer l'erreur 500 des notes
 */

require_once 'vendor/autoload.php';

echo "=== DIAGNOSTIC ERREUR 500 GRADES ===\n\n";

// Test 1: Vérifier la connexion à la base de données
echo "1. 🔍 Test de connexion à la base de données...\n";
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=csndr_db', 'root', '');
    echo "   ✅ Connexion DB réussie\n\n";
} catch (Exception $e) {
    echo "   ❌ Erreur DB: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 2: Vérifier l'existence de la table notes
echo "2. 📋 Vérification de la table 'notes'...\n";
try {
    $stmt = $pdo->query("DESCRIBE notes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   ✅ Table 'notes' existe avec " . count($columns) . " colonnes:\n";
    foreach ($columns as $col) {
        echo "      - {$col['Field']} ({$col['Type']})\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur table notes: " . $e->getMessage() . "\n\n";
}

// Test 3: Vérifier l'existence d'un professeur avec ID 1
echo "3. 👨‍🏫 Vérification du professeur ID 1...\n";
try {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, role FROM users WHERE id = 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "   ✅ Utilisateur ID 1 trouvé: {$user['prenom']} {$user['nom']} (rôle: {$user['role']})\n";
    } else {
        echo "   ❌ Aucun utilisateur avec ID 1 trouvé\n";
        echo "   🔧 Création d'un utilisateur admin par défaut...\n";
        
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'Système', 'admin@csndr.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
        echo "   ✅ Utilisateur admin créé avec ID: " . $pdo->lastInsertId() . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur utilisateur: " . $e->getMessage() . "\n\n";
}

// Test 4: Vérifier l'existence d'élèves
echo "4. 👨‍🎓 Vérification des élèves...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'eleve'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   📊 Nombre d'élèves: {$result['count']}\n";
    
    if ($result['count'] == 0) {
        echo "   🔧 Création d'un élève de test...\n";
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Dupont', 'Jean', 'jean.dupont@test.com', password_hash('test123', PASSWORD_DEFAULT), 'eleve']);
        echo "   ✅ Élève de test créé avec ID: " . $pdo->lastInsertId() . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur élèves: " . $e->getMessage() . "\n\n";
}

// Test 5: Test d'insertion directe d'une note
echo "5. 📝 Test d'insertion directe d'une note...\n";
try {
    // Récupérer un élève
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'eleve' LIMIT 1");
    $eleve = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$eleve) {
        echo "   ❌ Aucun élève disponible pour le test\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO notes (eleve_id, matiere, note, coefficient, commentaire, professeur_id, date, type_evaluation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $eleve['id'],
            'Test Diagnostic',
            15.5,
            1.0,
            'Test automatique',
            1,
            date('Y-m-d'),
            'Contrôle'
        ]);
        
        if ($result) {
            $noteId = $pdo->lastInsertId();
            echo "   ✅ Note créée avec succès (ID: $noteId)\n";
            
            // Supprimer la note de test
            $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ?");
            $stmt->execute([$noteId]);
            echo "   🗑️ Note de test supprimée\n";
        } else {
            echo "   ❌ Échec de création de la note\n";
        }
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur insertion note: " . $e->getMessage() . "\n\n";
}

echo "=== DIAGNOSTIC TERMINÉ ===\n";
echo "Si tous les tests sont ✅, le problème vient probablement du code Laravel.\n";
echo "Sinon, corrigez les problèmes identifiés ci-dessus.\n\n";

?>

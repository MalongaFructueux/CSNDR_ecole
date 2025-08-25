<?php

/**
 * Script de test de connexion à la base de données
 * 
 * Ce script tente de se connecter à la base de données en utilisant les informations
 * du fichier .env et affiche les résultats ou les erreurs rencontrées.
 */

echo "=== Test de connexion à la base de données ===\n\n";

// Définir directement les variables d'environnement
$envVars = [
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => 'db5018439262.hosting-data.io',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'dbs14660302',
    'DB_USERNAME' => 'dbu3034450',
    'DB_PASSWORD' => '@Alomlikeg1ming1234'
];

// Afficher les informations de connexion (sans le mot de passe)
echo "Informations de connexion utilisées:\n";
echo "DB_CONNECTION: {$envVars['DB_CONNECTION']}\n";
echo "DB_HOST: {$envVars['DB_HOST']}\n";
echo "DB_PORT: {$envVars['DB_PORT']}\n";
echo "DB_DATABASE: {$envVars['DB_DATABASE']}\n";
echo "DB_USERNAME: {$envVars['DB_USERNAME']}\n";
echo "DB_PASSWORD: ********\n\n";

// Tester la connexion à la base de données
try {
    $dsn = "{$envVars['DB_CONNECTION']}:host={$envVars['DB_HOST']};port={$envVars['DB_PORT']};dbname={$envVars['DB_DATABASE']}";
    $pdo = new PDO($dsn, $envVars['DB_USERNAME'], $envVars['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base de données réussie!\n";
    
    // Vérifier si la table personal_access_tokens existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'personal_access_tokens'");
    if ($stmt->rowCount() > 0) {
        echo "✅ La table 'personal_access_tokens' existe.\n";
    } else {
        echo "❌ La table 'personal_access_tokens' n'existe pas.\n";
        echo "Exécutez la commande 'php artisan migrate' pour créer les tables nécessaires.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion à la base de données:\n";
    echo $e->getMessage() . "\n\n";
    
    // Vérifier si l'erreur est liée à un problème de résolution DNS
    if (strpos($e->getMessage(), 'getaddrinfo failed') !== false) {
        echo "Le serveur ne peut pas résoudre le nom d'hôte de la base de données.\n";
        echo "Vérifiez que le nom d'hôte est correct et que vous avez accès au réseau.\n";
        
        // Tester la résolution DNS
        echo "\nTest de résolution DNS pour {$envVars['DB_HOST']}:\n";
        $ip = gethostbyname($envVars['DB_HOST']);
        if ($ip === $envVars['DB_HOST']) {
            echo "❌ Impossible de résoudre le nom d'hôte.\n";
        } else {
            echo "✅ Résolution DNS réussie: {$ip}\n";
        }
    }
    
    // Vérifier si l'erreur est liée à un problème d'authentification
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "Le nom d'utilisateur ou le mot de passe est incorrect.\n";
        echo "Vérifiez vos identifiants de connexion.\n";
    }
}

echo "\n=== Test terminé ===\n";
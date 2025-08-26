<?php

/**
 * Script de configuration du fichier .env pour IONOS
 * 
 * Ce script génère automatiquement le fichier .env à partir du fichier .env.ionos
 * et demande à l'utilisateur de saisir les informations sensibles.
 */

echo "=== Configuration du fichier .env pour IONOS ===\n\n";

// Vérification de l'existence du fichier .env.ionos
if (!file_exists(__DIR__ . '/.env.ionos')) {
    echo "❌ Le fichier .env.ionos n'existe pas.\n";
    echo "Veuillez créer ce fichier à partir du fichier .env.example.\n";
    exit(1);
}

// Lecture du contenu du fichier .env.ionos
$envContent = file_get_contents(__DIR__ . '/.env.ionos');

// Demande des informations sensibles
echo "Veuillez saisir les informations suivantes :\n";

// APP_KEY
echo "APP_KEY (laissez vide pour générer automatiquement) : ";
$appKey = trim(fgets(STDIN));
if (empty($appKey)) {
    // Génération d'une clé aléatoire de 32 caractères encodée en base64
    $appKey = 'base64:' . base64_encode(random_bytes(32));
}
$envContent = preg_replace('/APP_KEY=.*/', "APP_KEY=$appKey", $envContent);

// DB_PASSWORD
echo "Mot de passe de la base de données : ";
$dbPassword = trim(fgets(STDIN));
$envContent = preg_replace('/DB_PASSWORD=.*/', "DB_PASSWORD=$dbPassword", $envContent);

// Écriture du fichier .env
file_put_contents(__DIR__ . '/.env', $envContent);

echo "\n✅ Le fichier .env a été créé avec succès.\n";

// Vérification de l'existence du fichier .env
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ Le fichier .env existe.\n";
} else {
    echo "❌ Le fichier .env n'a pas pu être créé.\n";
    exit(1);
}

echo "\n=== Configuration terminée ===\n";
echo "Veuillez exécuter la commande suivante pour vérifier la configuration :\n";
echo "php check_ionos_config.php\n";
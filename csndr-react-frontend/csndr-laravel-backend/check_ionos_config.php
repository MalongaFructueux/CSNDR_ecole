<?php

/**
 * Script de vérification de la configuration pour IONOS
 * 
 * Ce script vérifie que toutes les configurations nécessaires sont correctes
 * pour résoudre les problèmes de connexion sur IONOS.
 */

echo "=== Vérification de la configuration IONOS ===\n\n";

// Vérification du fichier .env
echo "Vérification du fichier .env...\n";
$env = file_exists(__DIR__ . '/.env');
echo $env ? "✅ Le fichier .env existe.\n" : "❌ Le fichier .env n'existe pas.\n";

if ($env) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    
    // Vérification de APP_URL
    $appUrl = preg_match('/APP_URL=https:\/\/csndr-gestion\.com/', $envContent);
    echo $appUrl ? "✅ APP_URL est correctement configuré.\n" : "❌ APP_URL n'est pas correctement configuré.\n";
    
    // Vérification de SANCTUM_STATEFUL_DOMAINS
    $sanctumDomains = preg_match('/SANCTUM_STATEFUL_DOMAINS=csndr-gestion\.com/', $envContent);
    echo $sanctumDomains ? "✅ SANCTUM_STATEFUL_DOMAINS est correctement configuré.\n" : "❌ SANCTUM_STATEFUL_DOMAINS n'est pas correctement configuré.\n";
    
    // Vérification de SESSION_DOMAIN
    $sessionDomain = preg_match('/SESSION_DOMAIN=csndr-gestion\.com/', $envContent);
    echo $sessionDomain ? "✅ SESSION_DOMAIN est correctement configuré.\n" : "❌ SESSION_DOMAIN n'est pas correctement configuré.\n";
}

// Vérification du middleware Sanctum
echo "\nVérification du middleware Sanctum...\n";
$kernel = file_get_contents(__DIR__ . '/app/Http/Kernel.php');
$sanctumMiddleware = strpos($kernel, '\\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class') !== false && 
                    strpos($kernel, '// \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class') === false;
echo $sanctumMiddleware ? "✅ Le middleware EnsureFrontendRequestsAreStateful est activé.\n" : "❌ Le middleware EnsureFrontendRequestsAreStateful n'est pas activé.\n";

// Vérification de la configuration CORS
echo "\nVérification de la configuration CORS...\n";
$cors = file_get_contents(__DIR__ . '/app/Http/Middleware/Cors.php');
$corsConfig = strpos($cors, "'Access-Control-Allow-Origin', 'https://csndr-gestion.com'") !== false;
echo $corsConfig ? "✅ La configuration CORS est correcte.\n" : "❌ La configuration CORS n'est pas correcte.\n";

// Vérification de la table personal_access_tokens
echo "\nVérification de la table personal_access_tokens...\n";
echo "⚠️ Veuillez vérifier manuellement que la table personal_access_tokens existe dans votre base de données.\n";
echo "Exécutez la commande suivante : php artisan migrate:status\n";

// Vérification de la configuration frontend
echo "\nVérification de la configuration frontend...\n";
if (file_exists(__DIR__ . '/../src/services/api.js')) {
    $api = file_get_contents(__DIR__ . '/../src/services/api.js');
    $apiUrl = strpos($api, "'https://csndr-gestion.com/api'") !== false;
    echo $apiUrl ? "✅ L'URL de l'API dans le frontend est correcte.\n" : "❌ L'URL de l'API dans le frontend n'est pas correcte.\n";
} else {
    echo "❌ Le fichier api.js n'a pas été trouvé.\n";
}

// Vérification des permissions
echo "\nVérification des permissions...\n";
$storageWritable = is_writable(__DIR__ . '/storage');
echo $storageWritable ? "✅ Le dossier storage est accessible en écriture.\n" : "❌ Le dossier storage n'est pas accessible en écriture.\n";

$bootstrapCacheWritable = is_writable(__DIR__ . '/bootstrap/cache');
echo $bootstrapCacheWritable ? "✅ Le dossier bootstrap/cache est accessible en écriture.\n" : "❌ Le dossier bootstrap/cache n'est pas accessible en écriture.\n";

echo "\n=== Fin de la vérification ===\n";
echo "Si des problèmes ont été détectés, veuillez consulter le GUIDE_INSTALLATION_IONOS.md pour les résoudre.\n";
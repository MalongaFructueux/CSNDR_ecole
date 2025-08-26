<?php

/**
 * Script de vérification des logs d'erreur Laravel
 * 
 * Ce script vérifie les logs d'erreur Laravel pour identifier les problèmes de connexion.
 */

echo "=== Vérification des logs d'erreur Laravel ===\n\n";

// Chemin vers le fichier de log Laravel
$logFile = __DIR__ . '/storage/logs/laravel.log';

// Vérification de l'existence du fichier de log
if (!file_exists($logFile)) {
    echo "❌ Le fichier de log n'existe pas.\n";
    echo "Veuillez vérifier que le dossier storage/logs est accessible en écriture.\n";
    exit(1);
}

// Lecture des dernières lignes du fichier de log
$logContent = shell_exec("tail -n 100 " . escapeshellarg($logFile));

if (empty($logContent)) {
    echo "✅ Aucune erreur récente dans les logs.\n";
} else {
    echo "⚠️ Dernières erreurs dans les logs :\n\n";
    
    // Recherche des erreurs liées à l'authentification ou à CORS
    $authErrors = preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?auth.*?\n/i', $logContent, $authMatches);
    $corsErrors = preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?cors.*?\n/i', $logContent, $corsMatches);
    $sanctumErrors = preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?sanctum.*?\n/i', $logContent, $sanctumMatches);
    $dbErrors = preg_match_all('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?database.*?\n/i', $logContent, $dbMatches);
    
    // Affichage des erreurs liées à l'authentification
    if ($authErrors > 0) {
        echo "Erreurs d'authentification :\n";
        foreach ($authMatches[0] as $match) {
            echo $match . "\n";
        }
    }
    
    // Affichage des erreurs liées à CORS
    if ($corsErrors > 0) {
        echo "Erreurs CORS :\n";
        foreach ($corsMatches[0] as $match) {
            echo $match . "\n";
        }
    }
    
    // Affichage des erreurs liées à Sanctum
    if ($sanctumErrors > 0) {
        echo "Erreurs Sanctum :\n";
        foreach ($sanctumMatches[0] as $match) {
            echo $match . "\n";
        }
    }
    
    // Affichage des erreurs liées à la base de données
    if ($dbErrors > 0) {
        echo "Erreurs de base de données :\n";
        foreach ($dbMatches[0] as $match) {
            echo $match . "\n";
        }
    }
    
    // Si aucune erreur spécifique n'a été trouvée
    if ($authErrors == 0 && $corsErrors == 0 && $sanctumErrors == 0 && $dbErrors == 0) {
        echo "Aucune erreur spécifique liée à l'authentification, CORS, Sanctum ou la base de données n'a été trouvée.\n";
        echo "Voici les 10 dernières lignes du log :\n\n";
        echo shell_exec("tail -n 10 " . escapeshellarg($logFile)) . "\n";
    }
}

echo "\n=== Fin de la vérification ===\n";
echo "Pour voir le fichier de log complet, exécutez la commande suivante :\n";
echo "tail -f storage/logs/laravel.log\n";
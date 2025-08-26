<?php

/**
 * Script de vérification de la table personal_access_tokens
 * 
 * Ce script vérifie que la table personal_access_tokens existe dans la base de données
 * et qu'elle a la structure correcte pour Laravel Sanctum.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Vérification de la table personal_access_tokens ===\n\n";

try {
    // Vérification de l'existence de la table
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('personal_access_tokens');
    
    if ($tableExists) {
        echo "✅ La table personal_access_tokens existe.\n";
        
        // Vérification de la structure de la table
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('personal_access_tokens');
        
        $requiredColumns = [
            'id',
            'tokenable_type',
            'tokenable_id',
            'name',
            'token',
            'abilities',
            'last_used_at',
            'created_at',
            'updated_at'
        ];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "✅ La structure de la table personal_access_tokens est correcte.\n";
        } else {
            echo "❌ La structure de la table personal_access_tokens est incorrecte.\n";
            echo "Colonnes manquantes : " . implode(', ', $missingColumns) . "\n";
            echo "Veuillez exécuter la commande suivante : php artisan migrate\n";
        }
    } else {
        echo "❌ La table personal_access_tokens n'existe pas.\n";
        echo "Veuillez exécuter la commande suivante : php artisan migrate\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur lors de la vérification de la table personal_access_tokens : " . $e->getMessage() . "\n";
    echo "Veuillez vérifier les informations de connexion à la base de données dans le fichier .env\n";
}

echo "\n=== Fin de la vérification ===\n";
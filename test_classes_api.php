<?php
// Test simple pour vérifier la récupération des classes
echo "=== TEST API CLASSES ===\n";

// Test 1: Vérifier la connexion à la base de données
try {
    require_once 'csndr-react-frontend/csndr-laravel-backend/vendor/autoload.php';
    
    // Charger l'application Laravel
    $app = require_once 'csndr-react-frontend/csndr-laravel-backend/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "✅ Laravel chargé avec succès\n";
    
    // Test 2: Vérifier le modèle Classe
    $classesCount = \App\Models\Classe::count();
    echo "✅ Nombre de classes en DB: $classesCount\n";
    
    // Test 3: Récupérer les classes avec la relation eleves
    $classes = \App\Models\Classe::withCount('eleves')->get();
    echo "✅ Classes récupérées: " . $classes->count() . "\n";
    
    foreach ($classes as $classe) {
        echo "  - {$classe->nom} ({$classe->eleves_count} élèves)\n";
    }
    
    // Test 4: Simuler la réponse JSON
    $jsonResponse = response()->json($classes);
    echo "✅ Réponse JSON générée avec succès\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DU TEST ===\n";

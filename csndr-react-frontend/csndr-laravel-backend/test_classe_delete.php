<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\ClassController;
use App\Models\Classe;
use App\Models\User;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST SUPPRESSION CLASSE ===\n\n";

try {
    // Lister toutes les classes
    echo "1. Classes existantes :\n";
    $classes = Classe::all();
    foreach ($classes as $classe) {
        $elevesCount = User::where('classe_id', $classe->id)->count();
        echo "   - ID: {$classe->id}, Nom: {$classe->nom}, Élèves: {$elevesCount}\n";
    }
    
    echo "\n2. Test suppression classe vide :\n";
    
    // Créer une classe test sans élèves
    $classeTest = Classe::create([
        'nom' => 'Classe Test Suppression ' . time(),
        'niveau' => 'Test',
        'annee_scolaire' => '2024-2025'
    ]);
    
    echo "   - Classe créée: ID {$classeTest->id}, Nom: {$classeTest->nom}\n";
    
    // Tester la suppression
    $controller = new ClassController();
    $request = new Request();
    
    $response = $controller->destroy($classeTest->id);
    $responseData = json_decode($response->getContent(), true);
    
    echo "   - Réponse suppression: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    echo "   - Status Code: " . $response->getStatusCode() . "\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

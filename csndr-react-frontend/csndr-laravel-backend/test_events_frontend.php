<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\EventController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST EVENTS AVEC DONNEES FRONTEND ===\n\n";

try {
    $controller = new EventController();
    
    // Test avec les données exactes du frontend
    echo "1. Test POST /events avec données frontend :\n";
    $request = new Request();
    $request->merge([
        'titre' => 'Test Event Frontend',
        'description' => 'Description test',
        'date_debut' => '2024-12-25',
        'date_fin' => '2024-12-25'
    ]);
    
    echo "   Données envoyées: " . json_encode($request->all()) . "\n";
    
    $response = $controller->store($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test avec données manquantes
    echo "2. Test POST /events avec date_fin manquante :\n";
    $request2 = new Request();
    $request2->merge([
        'titre' => 'Test Event Sans Date Fin',
        'description' => 'Description test',
        'date_debut' => '2024-12-25'
        // date_fin manquante
    ]);
    
    echo "   Données envoyées: " . json_encode($request2->all()) . "\n";
    
    $response2 = $controller->store($request2);
    echo "   Status: " . $response2->getStatusCode() . "\n";
    echo "   Content: " . $response2->getContent() . "\n\n";
    
    // Test avec date invalide
    echo "3. Test POST /events avec date invalide :\n";
    $request3 = new Request();
    $request3->merge([
        'titre' => 'Test Event Date Invalide',
        'description' => 'Description test',
        'date_debut' => 'invalid-date',
        'date_fin' => 'invalid-date'
    ]);
    
    echo "   Données envoyées: " . json_encode($request3->all()) . "\n";
    
    $response3 = $controller->store($request3);
    echo "   Status: " . $response3->getStatusCode() . "\n";
    echo "   Content: " . $response3->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

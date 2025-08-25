<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\EventController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST EVENTS CRUD ===\n\n";

try {
    $controller = new EventController();
    
    // Test 1: Lister les événements
    echo "1. Test GET /events :\n";
    $response = $controller->index();
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . substr($response->getContent(), 0, 200) . "...\n\n";
    
    // Test 2: Créer un événement
    echo "2. Test POST /events :\n";
    $request = new Request();
    $request->merge([
        'titre' => 'Test Event ' . time(),
        'description' => 'Description test',
        'date_debut' => '2024-12-25',
        'date_fin' => '2024-12-25'
    ]);
    
    $response = $controller->store($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    if ($response->getStatusCode() === 201) {
        $eventData = json_decode($response->getContent(), true);
        $eventId = $eventData['id'];
        
        // Test 3: Modifier l'événement
        echo "3. Test PUT /events/{$eventId} :\n";
        $updateRequest = new Request();
        $updateRequest->merge([
            'titre' => 'Test Event Updated',
            'description' => 'Description updated',
            'date_debut' => '2024-12-26',
            'date_fin' => '2024-12-26'
        ]);
        
        $response = $controller->update($updateRequest, $eventId);
        echo "   Status: " . $response->getStatusCode() . "\n";
        echo "   Content: " . $response->getContent() . "\n\n";
        
        // Test 4: Supprimer l'événement
        echo "4. Test DELETE /events/{$eventId} :\n";
        $response = $controller->destroy($eventId);
        echo "   Status: " . $response->getStatusCode() . "\n";
        echo "   Content: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\MessageController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST MESSAGES CRUD ===\n\n";

try {
    $controller = new MessageController();
    
    // Test 1: Lister les messages
    echo "1. Test GET /messages :\n";
    $response = $controller->index();
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . substr($response->getContent(), 0, 200) . "...\n\n";
    
    // Test 2: Récupérer les utilisateurs disponibles
    echo "2. Test GET /messages/available-users :\n";
    $response = $controller->getAvailableUsers();
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . substr($response->getContent(), 0, 200) . "...\n\n";
    
    // Test 3: Envoyer un message
    echo "3. Test POST /messages :\n";
    $request = new Request();
    $request->merge([
        'destinataire_id' => 2, // Supposons qu'il existe un utilisateur avec ID 2
        'contenu' => 'Test message ' . time()
    ]);
    
    $response = $controller->store($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 4: Récupérer les conversations
    echo "4. Test GET /messages/conversations :\n";
    $response = $controller->conversations();
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . substr($response->getContent(), 0, 200) . "...\n\n";
    
    // Test 5: Récupérer une conversation spécifique
    echo "5. Test GET /messages/conversations/2 :\n";
    $response = $controller->show(2);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . substr($response->getContent(), 0, 200) . "...\n\n";
    
    // Test 6: Marquer comme lu
    echo "6. Test POST /messages/read/2 :\n";
    $response = $controller->markAsRead(2);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

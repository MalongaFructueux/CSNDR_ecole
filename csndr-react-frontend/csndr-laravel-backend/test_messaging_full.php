<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\MessageController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AUDIT COMPLET MESSAGERIE ===\n\n";

try {
    $controller = new MessageController();
    
    // Test 1: Index des messages
    echo "1. Test GET /messages (index) :\n";
    $response = $controller->index();
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n";
    $data = json_decode($content, true);
    echo "   Nombre de messages: " . (is_array($data) ? count($data) : 'N/A') . "\n\n";
    
    // Test 2: Conversations
    echo "2. Test GET /messages/conversations :\n";
    $response = $controller->conversations();
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n";
    $data = json_decode($content, true);
    echo "   Type de données: " . gettype($data) . "\n";
    if (is_array($data)) {
        echo "   Nombre d'éléments: " . count($data) . "\n";
        echo "   Clés: " . implode(', ', array_keys($data)) . "\n";
    }
    echo "\n";
    
    // Test 3: Available users
    echo "3. Test GET /messages/available-users :\n";
    $response = $controller->getAvailableUsers();
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n";
    $data = json_decode($content, true);
    echo "   Nombre d'utilisateurs: " . (is_array($data) ? count($data) : 'N/A') . "\n\n";
    
    // Test 4: Envoyer un message
    echo "4. Test POST /messages :\n";
    $request = new Request();
    $request->merge([
        'destinataire_id' => 2,
        'contenu' => 'Test audit complet ' . time()
    ]);
    $response = $controller->store($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n\n";
    
    // Test 5: Messages d'une conversation
    echo "5. Test GET /messages/conversations/2 :\n";
    $response = $controller->show(2);
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n";
    $data = json_decode($content, true);
    echo "   Nombre de messages dans conversation: " . (is_array($data) ? count($data) : 'N/A') . "\n\n";
    
    // Test 6: Marquer comme lu
    echo "6. Test POST /messages/read/2 :\n";
    $response = $controller->markAsRead(2);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 7: Re-vérifier conversations après ajout
    echo "7. Test GET /messages/conversations (après ajout) :\n";
    $response = $controller->conversations();
    echo "   Status: " . $response->getStatusCode() . "\n";
    $content = $response->getContent();
    echo "   Content: " . substr($content, 0, 300) . "...\n";
    $data = json_decode($content, true);
    echo "   Type de données: " . gettype($data) . "\n";
    if (is_array($data)) {
        echo "   Nombre d'éléments: " . count($data) . "\n";
        echo "   Structure: ";
        foreach ($data as $key => $value) {
            echo "[$key => " . (is_array($value) ? count($value) . " items" : gettype($value)) . "] ";
        }
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

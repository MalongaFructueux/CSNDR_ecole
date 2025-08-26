<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST SEARCH USERS ===\n\n";

try {
    $controller = new UserController();
    
    // Test 1: Recherche vide
    echo "1. Test recherche vide :\n";
    $request = new Request();
    $request->merge(['query' => '']);
    $response = $controller->searchUsers($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 2: Recherche avec 1 caractère
    echo "2. Test recherche 1 caractère :\n";
    $request = new Request();
    $request->merge(['query' => 'a']);
    $response = $controller->searchUsers($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 3: Recherche avec nom
    echo "3. Test recherche nom 'Admin' :\n";
    $request = new Request();
    $request->merge(['query' => 'Admin']);
    $response = $controller->searchUsers($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 4: Recherche avec prénom
    echo "4. Test recherche prénom 'Fructueux' :\n";
    $request = new Request();
    $request->merge(['query' => 'Fructueux']);
    $response = $controller->searchUsers($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n\n";
    
    // Test 5: Recherche avec email
    echo "5. Test recherche email 'malonga' :\n";
    $request = new Request();
    $request->merge(['query' => 'malonga']);
    $response = $controller->searchUsers($request);
    echo "   Status: " . $response->getStatusCode() . "\n";
    echo "   Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

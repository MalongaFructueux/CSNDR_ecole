<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

// Charger Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST RECHERCHE DIRECTE ===\n\n";

try {
    $controller = new UserController();
    
    // Test avec différents termes
    $searchTerms = ['Admin', 'Fructueux', 'malonga', 'test', 'a', ''];
    
    foreach ($searchTerms as $term) {
        echo "Recherche pour '$term' :\n";
        $request = new Request();
        $request->merge(['query' => $term]);
        
        $response = $controller->searchUsers($request);
        echo "   Status: " . $response->getStatusCode() . "\n";
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        if (is_array($data)) {
            echo "   Résultats: " . count($data) . " utilisateurs\n";
            foreach ($data as $user) {
                echo "     - {$user['prenom']} {$user['nom']} ({$user['email']}) - {$user['role']}\n";
            }
        } else {
            echo "   Erreur: " . $content . "\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

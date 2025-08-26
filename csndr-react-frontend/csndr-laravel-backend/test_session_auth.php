<?php

// Test rapide pour vérifier le middleware SessionAuth
require_once 'vendor/autoload.php';

echo "=== Test du Middleware SessionAuth ===\n\n";

// Test 1: Vérifier les routes publiques
echo "1. Test des routes publiques (doivent fonctionner):\n";
$publicRoutes = [
    '/api/classes',
    '/api/events', 
    '/api/grades',
    '/api/homework'
];

foreach ($publicRoutes as $route) {
    $url = "http://127.0.0.1:8000$route";
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\n",
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
        $httpCode = intval($matches[1]);
    }
    
    echo "  $route: " . ($httpCode === 200 ? "✓ OK ($httpCode)" : "✗ Erreur ($httpCode)") . "\n";
}

echo "\n2. Test des routes protégées (doivent retourner 401):\n";
$protectedRoutes = [
    '/api/users',
    '/api/messages/conversations'
];

foreach ($protectedRoutes as $route) {
    $url = "http://127.0.0.1:8000$route";
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\n",
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
        $httpCode = intval($matches[1]);
    }
    
    echo "  $route: " . ($httpCode === 401 ? "✓ 401 (attendu)" : "✗ $httpCode (attendu 401)") . "\n";
}

echo "\n3. Test de login:\n";
$loginUrl = "http://127.0.0.1:8000/api/auth/login";
$loginData = json_encode([
    'email' => 'admin@csndr.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
        'content' => $loginData,
        'ignore_errors' => true
    ]
]);

$response = @file_get_contents($loginUrl, false, $context);
$httpCode = 0;
if (isset($http_response_header)) {
    preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
    $httpCode = intval($matches[1]);
}

if ($response !== false) {
    $data = json_decode($response, true);
    echo "  Login ($httpCode): " . ($httpCode === 200 ? "✓ Succès" : "✗ Échec") . "\n";
    if (isset($data['message'])) {
        echo "  Message: " . $data['message'] . "\n";
    }
} else {
    echo "  ✗ Login impossible - serveur non accessible\n";
}

echo "\n=== Instructions ===\n";
echo "Si les routes publiques ne fonctionnent pas:\n";
echo "1. Vérifiez que le serveur Laravel tourne sur http://127.0.0.1:8000\n";
echo "2. Redémarrez le serveur: php artisan serve\n\n";

echo "Si les routes protégées ne retournent pas 401:\n";
echo "1. Le middleware SessionAuth n'est pas actif\n";
echo "2. Redémarrez le serveur Laravel\n\n";

echo "Pour tester l'authentification complète:\n";
echo "1. Connectez-vous via le frontend React\n";
echo "2. Vérifiez que les routes protégées fonctionnent après login\n";

echo "\nTest terminé.\n";

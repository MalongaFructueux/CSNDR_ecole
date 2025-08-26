<?php

echo "=== Test Complet du Système d'Authentification ===\n\n";

// Démarrer la session pour simuler le navigateur
session_start();

// Test 1: Login
echo "1. Test de connexion:\n";
$loginUrl = "http://127.0.0.1:8000/api/auth/login";
$loginData = json_encode([
    'email' => 'admin@csndr.com',
    'password' => 'admin123'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n" .
                   "Accept: application/json\r\n",
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

if ($httpCode === 200) {
    echo "✓ Login réussi\n";
    $loginData = json_decode($response, true);
    
    // Extraire les cookies de session
    $cookies = [];
    foreach ($http_response_header as $header) {
        if (strpos($header, 'Set-Cookie:') === 0) {
            $cookie = trim(substr($header, 11));
            $cookies[] = $cookie;
        }
    }
    
    if (!empty($cookies)) {
        echo "✓ Cookies de session reçus\n";
        
        // Test 2: Utiliser la session pour accéder aux routes protégées
        echo "\n2. Test d'accès aux routes protégées avec session:\n";
        
        $cookieHeader = 'Cookie: ' . implode('; ', array_map(function($cookie) {
            return explode(';', $cookie)[0];
        }, $cookies));
        
        $protectedUrl = "http://127.0.0.1:8000/api/users";
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n" .
                           "Content-Type: application/json\r\n" .
                           "$cookieHeader\r\n",
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($protectedUrl, false, $context);
        $httpCode = 0;
        if (isset($http_response_header)) {
            preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
            $httpCode = intval($matches[1]);
        }
        
        if ($httpCode === 200) {
            echo "✓ Accès autorisé avec session\n";
            echo "Le système d'authentification fonctionne!\n";
        } else {
            echo "✗ Accès refusé ($httpCode) - Le middleware SessionAuth ne fonctionne pas\n";
        }
    } else {
        echo "✗ Aucun cookie de session reçu\n";
    }
} else {
    echo "✗ Login échoué ($httpCode)\n";
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['message'])) {
            echo "Message: " . $data['message'] . "\n";
        }
    }
}

echo "\n=== Recommandations ===\n";
echo "1. Si le login fonctionne mais pas l'accès aux routes:\n";
echo "   - Le middleware SessionAuth a besoin d'ajustements\n";
echo "   - Vérifier la gestion des sessions Laravel\n\n";

echo "2. Une fois l'auth fonctionnelle:\n";
echo "   - SUPPRIMER immédiatement les routes publiques temporaires\n";
echo "   - Remettre toutes les routes dans le groupe middleware('auth')\n\n";

echo "3. En attendant (DÉVELOPPEMENT UNIQUEMENT):\n";
echo "   - Les routes publiques sont acceptables TEMPORAIREMENT\n";
echo "   - JAMAIS en production\n";

echo "\nTest terminé.\n";

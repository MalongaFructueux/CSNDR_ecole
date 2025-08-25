<?php
/**
 * Script de test pour les routes d'authentification
 * Teste l'inscription et la connexion via l'API
 */

// Configuration
$baseUrl = 'http://localhost:8000/api';

function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
    }

    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== Test des routes d'authentification ===\n\n";

// Test 1: Vérifier les classes disponibles
echo "1. Test des classes disponibles:\n";
$response = makeRequest($baseUrl . '/auth/available-classes');
echo "Code: " . $response['code'] . "\n";
echo "Réponse: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Vérifier les parents disponibles
echo "2. Test des parents disponibles:\n";
$response = makeRequest($baseUrl . '/auth/available-parents');
echo "Code: " . $response['code'] . "\n";
echo "Réponse: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Test d'inscription d'un parent
echo "3. Test d'inscription d'un parent:\n";
$parentData = [
    'nom' => 'Test',
    'prenom' => 'Parent',
    'email' => 'parent.test@example.com',
    'password' => 'Password123',
    'role' => 'parent'
];

$response = makeRequest($baseUrl . '/auth/register', 'POST', $parentData);
echo "Code: " . $response['code'] . "\n";
echo "Réponse: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Test de connexion
echo "4. Test de connexion:\n";
$loginData = [
    'email' => 'parent.test@example.com',
    'password' => 'Password123'
];

$response = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
echo "Code: " . $response['code'] . "\n";
echo "Réponse: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Vérification d'email existant
echo "5. Test de vérification d'email:\n";
$emailData = ['email' => 'parent.test@example.com'];
$response = makeRequest($baseUrl . '/auth/check-email', 'POST', $emailData);
echo "Code: " . $response['code'] . "\n";
echo "Réponse: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

echo "=== Tests terminés ===\n";
?>

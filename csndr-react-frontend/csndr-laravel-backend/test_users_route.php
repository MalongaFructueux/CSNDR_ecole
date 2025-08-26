<?php

echo "=== Test Route /users ===\n\n";

$url = "http://127.0.0.1:8000/api/users";

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Accept: application/json\r\n" .
                   "Content-Type: application/json\r\n",
        'ignore_errors' => true
    ]
]);

echo "Test de la route: $url\n";

$response = @file_get_contents($url, false, $context);
$httpCode = 0;

if (isset($http_response_header)) {
    preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
    $httpCode = intval($matches[1]);
    echo "Code HTTP: $httpCode\n";
} else {
    echo "Erreur: Impossible de contacter le serveur\n";
    exit;
}

if ($response !== false) {
    $data = json_decode($response, true);
    
    if ($httpCode === 200) {
        echo "✓ Succès! Route /users accessible\n";
        echo "Nombre d'utilisateurs: " . (is_array($data) ? count($data) : 'N/A') . "\n";
    } else {
        echo "✗ Erreur $httpCode\n";
        if (is_array($data) && isset($data['message'])) {
            echo "Message: " . $data['message'] . "\n";
        }
        echo "Réponse: " . substr($response, 0, 200) . "\n";
    }
} else {
    echo "✗ Aucune réponse du serveur\n";
}

echo "\n=== Instructions ===\n";
if ($httpCode === 401) {
    echo "La route est encore protégée. Solutions:\n";
    echo "1. Redémarrer le serveur Laravel: php artisan serve\n";
    echo "2. Vider le cache: php artisan route:clear\n";
} elseif ($httpCode === 200) {
    echo "La route fonctionne! Le problème vient du frontend.\n";
} else {
    echo "Vérifier que le serveur Laravel tourne sur http://127.0.0.1:8000\n";
}

echo "\nTest terminé.\n";

<?php

echo "=== Test de Toutes les Routes Publiques ===\n\n";

$routes = [
    'Classes' => '/api/classes',
    'Events' => '/api/events',
    'Grades' => '/api/grades',
    'Homework' => '/api/homework',
    'Users (temporaire)' => '/api/users'
];

foreach ($routes as $name => $route) {
    $url = "http://127.0.0.1:8000$route";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Accept: application/json\r\n" .
                       "Content-Type: application/json\r\n",
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $httpCode = 0;
    
    if (isset($http_response_header)) {
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $http_response_header[0], $matches);
        $httpCode = intval($matches[1]);
    }
    
    if ($response !== false) {
        $data = json_decode($response, true);
        
        if ($httpCode === 200) {
            $count = is_array($data) ? count($data) : 'N/A';
            echo "✓ $name: OK ($count éléments)\n";
        } else {
            echo "✗ $name: Erreur $httpCode\n";
            if (is_array($data) && isset($data['message'])) {
                echo "  Message: " . $data['message'] . "\n";
            }
        }
    } else {
        echo "✗ $name: Serveur non accessible\n";
    }
}

echo "\n=== Test Routes Protégées (doivent retourner 401) ===\n";

$protectedRoutes = [
    'Messages' => '/api/messages/conversations',
    'Available Users' => '/api/messages/available-users'
];

foreach ($protectedRoutes as $name => $route) {
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
    
    if ($httpCode === 401) {
        echo "✓ $name: 401 (correct - protégé)\n";
    } else {
        echo "✗ $name: $httpCode (attendu 401)\n";
    }
}

echo "\nTest terminé.\n";

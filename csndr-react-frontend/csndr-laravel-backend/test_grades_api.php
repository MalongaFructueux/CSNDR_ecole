<?php

// Test direct des API grades via cURL
echo "=== Test des API Grades ===\n\n";

// Configuration
$baseUrl = 'http://127.0.0.1:8000/api';

function testAPI($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "Method: $method\n";
    echo "URL: $url\n";
    echo "HTTP Code: $httpCode\n";
    if ($error) echo "cURL Error: $error\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
    echo str_repeat("-", 50) . "\n\n";
    
    return json_decode($response, true);
}

// Test 1: Récupérer toutes les notes
echo "1. GET /grades\n";
$grades = testAPI('GET', "$baseUrl/grades");

// Test 2: Créer une nouvelle note
echo "2. POST /grades\n";
$newGrade = [
    'note' => 16.5,
    'matiere' => 'Mathematiques',
    'eleve_id' => 1,
    'commentaire' => 'Test API'
];
$created = testAPI('POST', "$baseUrl/grades", $newGrade);

// Test 3: Modifier une note (utiliser l'ID de la note créée)
if (isset($created['id'])) {
    echo "3. PUT /grades/{$created['id']}\n";
    $updateData = [
        'note' => 18.0,
        'matiere' => 'Mathematiques',
        'eleve_id' => 1,
        'commentaire' => 'Note modifiée'
    ];
    testAPI('PUT', "$baseUrl/grades/{$created['id']}", $updateData);
    
    // Test 4: Supprimer la note
    echo "4. DELETE /grades/{$created['id']}\n";
    testAPI('DELETE', "$baseUrl/grades/{$created['id']}");
} else {
    echo "Impossible de tester PUT/DELETE - création échouée\n";
}

echo "=== Fin des tests ===\n";

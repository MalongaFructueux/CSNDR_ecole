<?php

// Test simple des API grades
echo "Test API Grades - " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("=", 50) . "\n";

// Test GET /api/grades
echo "1. Test GET /api/grades\n";
$ch = curl_init('http://127.0.0.1:8000/api/grades');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

// Test POST /api/grades
echo "2. Test POST /api/grades\n";
$data = json_encode([
    'note' => 15.5,
    'matiere' => 'Test',
    'eleve_id' => 1,
    'commentaire' => 'Test API'
]);

$ch = curl_init('http://127.0.0.1:8000/api/grades');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

$created = json_decode($response, true);
if (isset($created['id'])) {
    $gradeId = $created['id'];
    
    // Test PUT
    echo "3. Test PUT /api/grades/$gradeId\n";
    $updateData = json_encode([
        'note' => 17.0,
        'matiere' => 'Test Modifié',
        'eleve_id' => 1,
        'commentaire' => 'Modifié'
    ]);
    
    $ch = curl_init("http://127.0.0.1:8000/api/grades/$gradeId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $updateData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . "...\n\n";
    
    // Test DELETE
    echo "4. Test DELETE /api/grades/$gradeId\n";
    $ch = curl_init("http://127.0.0.1:8000/api/grades/$gradeId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . "...\n";
}

echo "\nTest terminé\n";

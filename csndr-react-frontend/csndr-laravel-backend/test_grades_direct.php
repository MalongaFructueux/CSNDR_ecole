<?php

// Test direct des opérations CRUD des notes
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TEST DIRECT GRADE CONTROLLER ===\n";

try {
    // Test 1: Index (GET /grades)
    echo "\n1. TEST INDEX\n";
    $request = Illuminate\Http\Request::create('/api/grades', 'GET');
    $controller = new App\Http\Controllers\GradeController();
    $response = $controller->index();
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 200) . "...\n";
    
    // Test 2: Store (POST /grades)
    echo "\n2. TEST STORE\n";
    $request = Illuminate\Http\Request::create('/api/grades', 'POST', [
        'note' => 15.5,
        'matiere' => 'Test Matière',
        'eleve_id' => 1,
        'commentaire' => 'Test commentaire'
    ]);
    $response = $controller->store($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 200) . "...\n";
    
    // Test 3: Update (PUT /grades/1)
    echo "\n3. TEST UPDATE\n";
    $request = Illuminate\Http\Request::create('/api/grades/1', 'PUT', [
        'note' => 16.0,
        'matiere' => 'Test Matière Modifiée',
        'eleve_id' => 1,
        'commentaire' => 'Test commentaire modifié'
    ]);
    $response = $controller->update($request, 1);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 200) . "...\n";
    
    // Test 4: Delete (DELETE /grades/1)
    echo "\n4. TEST DELETE\n";
    $response = $controller->destroy(1);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
} catch (\Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

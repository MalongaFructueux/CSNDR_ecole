<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Simulate a test request to check authentication
$baseUrl = 'http://127.0.0.1:8000/api';

// Test 1: Check if public routes work
echo "=== Test 1: Public Routes ===\n";
$publicRoutes = ['/classes', '/events', '/grades', '/homework'];

foreach ($publicRoutes as $route) {
    $url = $baseUrl . $route;
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        echo "✓ GET $route: " . (is_array($data) ? count($data) . " items" : "OK") . "\n";
    } else {
        echo "✗ GET $route: Failed\n";
    }
}

echo "\n=== Test 2: Protected Routes (should return 401) ===\n";
$protectedRoutes = ['/users', '/messages/conversations'];

foreach ($protectedRoutes as $route) {
    $url = $baseUrl . $route;
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Content-Type: application/json\r\n" .
                       "Accept: application/json\r\n",
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
        echo "✓ GET $route: 401 (expected)\n";
    } else {
        echo "✗ GET $route: $httpCode (expected 401)\n";
    }
}

echo "\n=== Test 3: Login Test ===\n";
$loginUrl = $baseUrl . '/auth/login';
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

if ($response !== false) {
    $data = json_decode($response, true);
    echo "Login response ($httpCode): " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "✗ Login failed\n";
}

echo "\nTest completed.\n";

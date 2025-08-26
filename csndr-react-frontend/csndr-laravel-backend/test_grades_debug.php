<?php

/**
 * Test de débogage pour les erreurs 500 des notes
 */

require_once 'vendor/autoload.php';

echo "=== TEST DEBUG GRADES API ===\n\n";

// Simuler une requête POST vers l'API grades
$url = 'http://127.0.0.1:8000/api/grades';
$data = [
    'note' => 15.5,
    'matiere' => 'Test Debug',
    'eleve_id' => 1,
    'commentaire' => 'Test automatique'
];

$options = [
    'http' => [
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest'
        ],
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);

echo "1. Test création note sans authentification...\n";
$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "   ❌ Erreur lors de la requête\n";
    $error = error_get_last();
    echo "   Détails: " . $error['message'] . "\n";
} else {
    echo "   ✅ Réponse reçue: " . $result . "\n";
}

echo "\n=== FIN TEST ===\n";

?>

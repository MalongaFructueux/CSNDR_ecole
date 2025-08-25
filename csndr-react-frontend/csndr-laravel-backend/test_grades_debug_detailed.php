<?php

/**
 * Script de debug détaillé pour reproduire exactement les erreurs 500
 */

require_once 'vendor/autoload.php';

echo "=== DEBUG DÉTAILLÉ ERREURS 500 GRADES ===\n\n";

// Test 1: Simulation de createGrade
echo "1. 🔍 Test createGrade (handleSubmit ligne 110)...\n";
testCreateGrade();

echo "\n2. 🔍 Test updateGrade (handleSubmit ligne 108)...\n";
testUpdateGrade();

echo "\n3. 🔍 Test deleteGrade (handleDelete ligne 145)...\n";
testDeleteGrade();

function testCreateGrade()
{
    $baseUrl = 'http://127.0.0.1:8000/api';
    
    // Données exactes envoyées par React
    $gradeData = [
        'note' => 15.5,
        'matiere' => 'Test Debug',
        'eleve_id' => 1, // Utiliser un ID qui existe
        'commentaire' => 'Test debug'
    ];

    echo "   Données envoyées: " . json_encode($gradeData, JSON_PRETTY_PRINT) . "\n";

    $ch = curl_init($baseUrl . '/grades');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gradeData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies_debug.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "   Code réponse: $httpCode\n";
    if ($httpCode === 500) {
        echo "   ❌ ERREUR 500 REPRODUITE!\n";
        echo "   Réponse: $response\n";
    } else {
        echo "   ✅ Succès: $response\n";
    }
}

function testUpdateGrade()
{
    $baseUrl = 'http://127.0.0.1:8000/api';
    
    // D'abord créer une note pour la modifier
    $gradeData = [
        'note' => 12.0,
        'matiere' => 'Test Update',
        'eleve_id' => 1,
        'commentaire' => 'Test pour modification'
    ];

    // Créer
    $ch = curl_init($baseUrl . '/grades');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gradeData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        $createdGrade = json_decode($response, true);
        $gradeId = $createdGrade['id'];
        echo "   Note créée avec ID: $gradeId\n";

        // Maintenant tester la modification
        $updateData = [
            'note' => 18.0,
            'matiere' => 'Test Update Modifié',
            'eleve_id' => 1,
            'commentaire' => 'Commentaire modifié'
        ];

        $ch = curl_init($baseUrl . '/grades/' . $gradeId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "   Code réponse UPDATE: $httpCode\n";
        if ($httpCode === 500) {
            echo "   ❌ ERREUR 500 UPDATE REPRODUITE!\n";
            echo "   Réponse: $response\n";
        } else {
            echo "   ✅ Update succès: $response\n";
        }
    } else {
        echo "   ❌ Impossible de créer une note pour le test update\n";
    }
}

function testDeleteGrade()
{
    $baseUrl = 'http://127.0.0.1:8000/api';
    
    // D'abord créer une note pour la supprimer
    $gradeData = [
        'note' => 10.0,
        'matiere' => 'Test Delete',
        'eleve_id' => 1,
        'commentaire' => 'Test pour suppression'
    ];

    // Créer
    $ch = curl_init($baseUrl . '/grades');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gradeData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) {
        $createdGrade = json_decode($response, true);
        $gradeId = $createdGrade['id'];
        echo "   Note créée avec ID: $gradeId\n";

        // Maintenant tester la suppression
        $ch = curl_init($baseUrl . '/grades/' . $gradeId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies_debug.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "   Code réponse DELETE: $httpCode\n";
        if ($httpCode === 500) {
            echo "   ❌ ERREUR 500 DELETE REPRODUITE!\n";
            echo "   Réponse: $response\n";
        } else {
            echo "   ✅ Delete succès: $response\n";
        }
    } else {
        echo "   ❌ Impossible de créer une note pour le test delete\n";
    }
}

// Nettoyage
if (file_exists('cookies_debug.txt')) {
    unlink('cookies_debug.txt');
}

echo "\n=== DEBUG TERMINÉ ===\n";

?>

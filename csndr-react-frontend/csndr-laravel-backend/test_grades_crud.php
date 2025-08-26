<?php

/**
 * Script de test pour les fonctionnalités CRUD des notes (Grades)
 * 
 * Ce script teste :
 * - Récupération des notes (GET /api/grades)
 * - Création d'une note (POST /api/grades)
 * - Modification d'une note (PUT /api/grades/{id})
 * - Suppression d'une note (DELETE /api/grades/{id})
 * 
 * Usage: php test_grades_crud.php
 */

require_once 'vendor/autoload.php';

class GradesCRUDTest
{
    private $baseUrl = 'http://127.0.0.1:8000/api';
    private $sessionCookie = null;
    private $createdGradeId = null;

    public function __construct()
    {
        echo "=== TEST CRUD GRADES - Centre Scolaire Notre Dame du Rosaire ===\n\n";
    }

    /**
     * Authentification pour obtenir une session
     */
    public function authenticate()
    {
        echo "1. 🔐 Test d'authentification...\n";
        
        $loginData = [
            'email' => 'admin@csndr.com',
            'password' => 'admin123'
        ];

        $ch = curl_init($this->baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpCode === 200) {
            echo "   ✅ Authentification réussie\n\n";
            return true;
        } else {
            echo "   ❌ Échec de l'authentification (Code: $httpCode)\n";
            echo "   Réponse: " . substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE)) . "\n\n";
            return false;
        }
        
        curl_close($ch);
    }

    /**
     * Test de récupération des notes
     */
    public function testGetGrades()
    {
        echo "2. 📚 Test de récupération des notes...\n";
        
        $ch = curl_init($this->baseUrl . '/grades');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $grades = json_decode($response, true);
            echo "   ✅ Récupération réussie (" . count($grades) . " notes trouvées)\n";
            
            if (count($grades) > 0) {
                $firstGrade = $grades[0];
                echo "   📝 Exemple de note: {$firstGrade['matiere']} - {$firstGrade['note']}/20\n";
                echo "       Élève: " . ($firstGrade['eleve']['prenom'] ?? 'N/A') . " " . ($firstGrade['eleve']['nom'] ?? 'N/A') . "\n";
            }
        } else {
            echo "   ❌ Échec de récupération (Code: $httpCode)\n";
            echo "   Réponse: $response\n";
        }
        echo "\n";
    }

    /**
     * Test de création d'une note
     */
    public function testCreateGrade()
    {
        echo "3. ➕ Test de création d'une note...\n";
        
        // D'abord, récupérer un élève pour le test
        $eleve = $this->getFirstStudent();
        if (!$eleve) {
            echo "   ❌ Aucun élève trouvé pour le test\n\n";
            return false;
        }

        $gradeData = [
            'note' => 15.5,
            'matiere' => 'Test Mathématiques',
            'eleve_id' => $eleve['id'],
            'commentaire' => 'Test automatique - Très bon travail',
            'coefficient' => 2.0,
            'type_evaluation' => 'Contrôle'
        ];

        $ch = curl_init($this->baseUrl . '/grades');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($gradeData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 201) {
            $createdGrade = json_decode($response, true);
            $this->createdGradeId = $createdGrade['id'];
            echo "   ✅ Création réussie (ID: {$this->createdGradeId})\n";
            echo "   📝 Note créée: {$createdGrade['matiere']} - {$createdGrade['note']}/20\n";
            echo "       Coefficient: {$createdGrade['coefficient']}\n";
            return true;
        } else {
            echo "   ❌ Échec de création (Code: $httpCode)\n";
            echo "   Réponse: $response\n";
            return false;
        }
        echo "\n";
    }

    /**
     * Test de modification d'une note
     */
    public function testUpdateGrade()
    {
        if (!$this->createdGradeId) {
            echo "4. ❌ Pas de note à modifier (création échouée)\n\n";
            return false;
        }

        echo "4. ✏️ Test de modification d'une note...\n";
        
        $eleve = $this->getFirstStudent();
        $updateData = [
            'note' => 18.0,
            'matiere' => 'Test Mathématiques (Modifié)',
            'eleve_id' => $eleve['id'],
            'commentaire' => 'Test automatique - Excellent travail après modification',
            'coefficient' => 3.0,
            'type_evaluation' => 'Examen'
        ];

        $ch = curl_init($this->baseUrl . '/grades/' . $this->createdGradeId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $updatedGrade = json_decode($response, true);
            echo "   ✅ Modification réussie\n";
            echo "   📝 Note modifiée: {$updatedGrade['matiere']} - {$updatedGrade['note']}/20\n";
            echo "       Nouveau coefficient: {$updatedGrade['coefficient']}\n";
            return true;
        } else {
            echo "   ❌ Échec de modification (Code: $httpCode)\n";
            echo "   Réponse: $response\n";
            return false;
        }
        echo "\n";
    }

    /**
     * Test de suppression d'une note
     */
    public function testDeleteGrade()
    {
        if (!$this->createdGradeId) {
            echo "5. ❌ Pas de note à supprimer (création échouée)\n\n";
            return false;
        }

        echo "5. 🗑️ Test de suppression d'une note...\n";
        
        $ch = curl_init($this->baseUrl . '/grades/' . $this->createdGradeId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            echo "   ✅ Suppression réussie\n";
            echo "   🗑️ Note supprimée (ID: {$this->createdGradeId})\n";
            return true;
        } else {
            echo "   ❌ Échec de suppression (Code: $httpCode)\n";
            echo "   Réponse: $response\n";
            return false;
        }
        echo "\n";
    }

    /**
     * Récupère le premier élève disponible pour les tests
     */
    private function getFirstStudent()
    {
        $ch = curl_init($this->baseUrl . '/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $users = json_decode($response, true);
            $usersData = $users['data'] ?? $users ?? [];
            
            foreach ($usersData as $user) {
                if ($user['role'] === 'eleve') {
                    return $user;
                }
            }
        }
        
        return null;
    }

    /**
     * Test de validation des données
     */
    public function testValidation()
    {
        echo "6. 🔍 Test de validation des données...\n";
        
        // Test avec note invalide (> 20)
        $invalidData = [
            'note' => 25,
            'matiere' => 'Test',
            'eleve_id' => 999999, // ID inexistant
            'commentaire' => ''
        ];

        $ch = curl_init($this->baseUrl . '/grades');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invalidData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 422) {
            echo "   ✅ Validation fonctionne correctement (erreur 422 attendue)\n";
            $errorResponse = json_decode($response, true);
            echo "   📝 Erreurs détectées: " . json_encode($errorResponse['errors'] ?? [], JSON_PRETTY_PRINT) . "\n";
        } else {
            echo "   ❌ Validation ne fonctionne pas correctement (Code: $httpCode)\n";
            echo "   Réponse: $response\n";
        }
        echo "\n";
    }

    /**
     * Exécute tous les tests
     */
    public function runAllTests()
    {
        if (!$this->authenticate()) {
            echo "❌ Impossible de continuer sans authentification\n";
            return;
        }

        $this->testGetGrades();
        $this->testCreateGrade();
        $this->testUpdateGrade();
        $this->testDeleteGrade();
        $this->testValidation();

        echo "=== RÉSUMÉ DES TESTS ===\n";
        echo "✅ Tests CRUD des notes terminés\n";
        echo "📝 Vérifiez les résultats ci-dessus pour les détails\n\n";
        
        // Nettoyage
        if (file_exists('cookies.txt')) {
            unlink('cookies.txt');
        }
    }
}

// Exécution des tests
$test = new GradesCRUDTest();
$test->runAllTests();

?>

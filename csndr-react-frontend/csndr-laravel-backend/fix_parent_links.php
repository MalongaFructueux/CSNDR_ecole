<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Classe;
use App\Models\Homework;
use App\Models\Grade;

echo "=== Correction automatique des liens parent-enfant ===\n\n";

// Récupérer tous les parents
$parents = User::where('role', 'parent')->get();
echo "Parents trouvés: " . $parents->count() . "\n";

if ($parents->isEmpty()) {
    echo "❌ Aucun parent trouvé!\n";
    exit(1);
}

// Récupérer tous les élèves sans parent
$elevesLibres = User::where('role', 'eleve')->whereNull('parent_id')->get();
echo "Élèves sans parent: " . $elevesLibres->count() . "\n\n";

if ($elevesLibres->isEmpty()) {
    echo "✅ Tous les élèves ont déjà un parent assigné!\n";
} else {
    // Distribuer les élèves aux parents
    $parentIndex = 0;
    $elevesParParent = ceil($elevesLibres->count() / $parents->count());
    
    foreach ($elevesLibres as $index => $eleve) {
        $parent = $parents[$parentIndex];
        
        $eleve->parent_id = $parent->id;
        $eleve->save();
        
        echo "✅ Élève {$eleve->prenom} {$eleve->nom} assigné au parent {$parent->prenom} {$parent->nom}\n";
        
        // Passer au parent suivant après avoir assigné le nombre d'élèves par parent
        if (($index + 1) % $elevesParParent === 0) {
            $parentIndex++;
        }
    }
}

echo "\n=== Vérification des données après correction ===\n";

// Vérifier chaque parent
foreach ($parents as $parent) {
    $enfants = User::where('parent_id', $parent->id)->get();
    echo "\n👨‍👩‍👧‍👦 Parent: {$parent->prenom} {$parent->nom} ({$parent->email})\n";
    echo "   Enfants: " . $enfants->count() . "\n";
    
    if ($enfants->count() > 0) {
        // Classes des enfants
        $classesIds = $enfants->pluck('classe_id')->filter()->unique();
        echo "   Classes: " . $classesIds->implode(', ') . "\n";
        
        // Devoirs pour ces classes
        $devoirs = Homework::whereIn('classe_id', $classesIds)->count();
        echo "   Devoirs visibles: {$devoirs}\n";
        
        // Notes pour ces enfants
        $enfantsIds = $enfants->pluck('id');
        $notes = Grade::whereIn('eleve_id', $enfantsIds)->count();
        echo "   Notes visibles: {$notes}\n";
        
        // Afficher les enfants
        foreach ($enfants as $enfant) {
            echo "   - {$enfant->prenom} {$enfant->nom} (classe_id: {$enfant->classe_id})\n";
        }
    }
}

// Créer quelques devoirs et notes de test si nécessaire
echo "\n=== Création de données de test supplémentaires ===\n";

$classes = Classe::all();
$professeurs = User::where('role', 'professeur')->get();

if ($classes->count() > 0 && $professeurs->count() > 0) {
    $classe = $classes->first();
    $professeur = $professeurs->first();
    
    // Créer un devoir de test
    $devoir = Homework::firstOrCreate(
        ['titre' => 'Devoir Test Parent-Enfant'],
        [
            'description' => 'Devoir créé automatiquement pour tester le système parent-enfant',
            'date_limite' => now()->addDays(7)->format('Y-m-d'),
            'classe_id' => $classe->id,
            'professeur_id' => $professeur->id
        ]
    );
    
    echo "✅ Devoir de test créé: {$devoir->titre}\n";
    
    // Créer des notes de test pour quelques élèves
    $elevesAvecParent = User::where('role', 'eleve')->whereNotNull('parent_id')->take(3)->get();
    
    foreach ($elevesAvecParent as $eleve) {
        $note = Grade::firstOrCreate(
            [
                'eleve_id' => $eleve->id,
                'matiere' => 'Test Parent-Enfant',
                'professeur_id' => $professeur->id
            ],
            [
                'note' => rand(10, 20),
                'commentaire' => 'Note de test pour vérifier le système parent-enfant',
                'coefficient' => 1,
                'type_evaluation' => 'Devoir'
            ]
        );
        
        echo "✅ Note de test créée pour {$eleve->prenom} {$eleve->nom}: {$note->note}/20\n";
    }
}

echo "\n=== Correction terminée ===\n";
echo "🎯 Maintenant tous les parents devraient voir les devoirs et notes de leurs enfants!\n";
echo "📱 Teste en te connectant avec un compte parent.\n";

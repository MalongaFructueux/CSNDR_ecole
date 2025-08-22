<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Classe;
use App\Models\Homework;
use App\Models\Grade;

echo "=== Création des données de test parent-enfant ===\n";

// Vérifier si le parent existe
$parent = User::where('email', 'parent@csndr.test')->first();
if (!$parent) {
    echo "❌ Parent parent@csndr.test non trouvé!\n";
    echo "Utilisateurs existants:\n";
    User::all(['id', 'email', 'role'])->each(function($u) {
        echo "- {$u->id}: {$u->email} ({$u->role})\n";
    });
    exit(1);
}

echo "✅ Parent trouvé: {$parent->prenom} {$parent->nom} (ID: {$parent->id})\n";

// Trouver des élèves sans parent
$eleves = User::where('role', 'eleve')->whereNull('parent_id')->get();
echo "Élèves disponibles sans parent: " . $eleves->count() . "\n";

if ($eleves->count() === 0) {
    echo "❌ Aucun élève sans parent trouvé!\n";
    exit(1);
}

// Lier les 2 premiers élèves au parent
$count = 0;
foreach ($eleves as $eleve) {
    if ($count >= 2) break;
    
    $eleve->parent_id = $parent->id;
    $eleve->save();
    echo "✅ Élève {$eleve->prenom} {$eleve->nom} lié au parent\n";
    $count++;
}

// Vérifier les classes des enfants
$enfants = User::where('parent_id', $parent->id)->get();
echo "\nEnfants du parent:\n";
foreach ($enfants as $enfant) {
    echo "- {$enfant->prenom} {$enfant->nom} (classe_id: {$enfant->classe_id})\n";
}

// Vérifier s'il y a des devoirs pour leurs classes
$classesIds = $enfants->pluck('classe_id')->filter()->unique();
echo "\nClasses des enfants: " . $classesIds->implode(', ') . "\n";

$devoirs = Homework::whereIn('classe_id', $classesIds)->get();
echo "Devoirs pour ces classes: " . $devoirs->count() . "\n";

// Vérifier s'il y a des notes pour les enfants
$enfantsIds = $enfants->pluck('id');
$notes = Grade::whereIn('eleve_id', $enfantsIds)->get();
echo "Notes pour ces enfants: " . $notes->count() . "\n";

echo "\n=== Test terminé ===\n";

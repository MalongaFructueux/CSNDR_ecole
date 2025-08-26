<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Homework;
use App\Models\Grade;
use App\Models\Classe;

echo "=== Vérification des données parent-enfant ===\n\n";

// Vérifier les parents
echo "Parents dans la base :\n";
$parents = User::where('role', 'parent')->get(['id', 'nom', 'prenom', 'email']);
foreach ($parents as $parent) {
    echo "- {$parent->id}: {$parent->prenom} {$parent->nom} ({$parent->email})\n";
}

if ($parents->isEmpty()) {
    echo "❌ Aucun parent trouvé !\n\n";
} else {
    echo "\n";
}

// Vérifier les enfants
echo "Élèves avec parent_id :\n";
$enfants = User::whereNotNull('parent_id')->get(['id', 'nom', 'prenom', 'parent_id', 'classe_id', 'role']);
foreach ($enfants as $enfant) {
    echo "- {$enfant->id}: {$enfant->prenom} {$enfant->nom} (parent: {$enfant->parent_id}, classe: {$enfant->classe_id}, role: {$enfant->role})\n";
}

if ($enfants->isEmpty()) {
    echo "❌ Aucun élève avec parent_id trouvé !\n\n";
} else {
    echo "\n";
}

// Vérifier les classes
echo "Classes disponibles :\n";
$classes = Classe::all(['id', 'nom']);
foreach ($classes as $classe) {
    echo "- {$classe->id}: {$classe->nom}\n";
}
echo "\n";

// Vérifier les devoirs
echo "Devoirs dans la base :\n";
$devoirs = Homework::with(['classe', 'professeur'])->get();
foreach ($devoirs as $devoir) {
    $classeNom = $devoir->classe ? $devoir->classe->nom : 'N/A';
    $profPrenom = $devoir->professeur ? $devoir->professeur->prenom : 'N/A';
    $profNom = $devoir->professeur ? $devoir->professeur->nom : 'N/A';
    echo "- {$devoir->id}: {$devoir->titre} (classe: {$classeNom}, prof: {$profPrenom} {$profNom})\n";
}

if ($devoirs->isEmpty()) {
    echo "❌ Aucun devoir trouvé !\n\n";
} else {
    echo "\n";
}

// Vérifier les notes
echo "Notes dans la base :\n";
$notes = Grade::with(['eleve', 'professeur'])->get();
foreach ($notes as $note) {
    $elevePrenom = $note->eleve ? $note->eleve->prenom : 'N/A';
    $eleveNom = $note->eleve ? $note->eleve->nom : 'N/A';
    $profPrenom = $note->professeur ? $note->professeur->prenom : 'N/A';
    $profNom = $note->professeur ? $note->professeur->nom : 'N/A';
    echo "- {$note->id}: {$note->matiere} - {$note->note}/20 (élève: {$elevePrenom} {$eleveNom}, prof: {$profPrenom} {$profNom})\n";
}

if ($notes->isEmpty()) {
    echo "❌ Aucune note trouvée !\n\n";
} else {
    echo "\n";
}

// Test du filtrage pour un parent spécifique
if (!$parents->isEmpty()) {
    $parent = $parents->first();
    echo "=== Test filtrage pour parent {$parent->prenom} {$parent->nom} (ID: {$parent->id}) ===\n";
    
    // Récupérer les enfants
    $enfantsIds = User::where('parent_id', $parent->id)->pluck('id');
    echo "Enfants IDs: " . $enfantsIds->implode(', ') . "\n";
    
    // Récupérer les classes des enfants
    $classesIds = User::whereIn('id', $enfantsIds)->pluck('classe_id')->filter();
    echo "Classes IDs des enfants: " . $classesIds->implode(', ') . "\n";
    
    // Devoirs filtrés
    $devoirsParent = Homework::whereIn('classe_id', $classesIds)->with(['classe', 'professeur'])->get();
    echo "Devoirs pour ce parent: " . $devoirsParent->count() . "\n";
    
    // Notes filtrées
    $notesParent = Grade::whereIn('eleve_id', $enfantsIds)->with(['eleve', 'professeur'])->get();
    echo "Notes pour ce parent: " . $notesParent->count() . "\n";
}

echo "\n=== Fin de la vérification ===\n";

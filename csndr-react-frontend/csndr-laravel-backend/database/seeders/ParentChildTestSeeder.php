<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Classe;
use App\Models\Homework;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;

class ParentChildTestSeeder extends Seeder
{
    public function run()
    {
        // Créer une classe de test si elle n'existe pas
        $classe = Classe::firstOrCreate(['nom' => 'CE1-Test']);
        
        // Créer un professeur de test
        $professeur = User::firstOrCreate(
            ['email' => 'prof.test@csndr.test'],
            [
                'nom' => 'Professeur',
                'prenom' => 'Test',
                'mot_de_passe' => Hash::make('password'),
                'role' => 'professeur',
                'classe_id' => $classe->id
            ]
        );

        // Créer un parent de test
        $parent = User::firstOrCreate(
            ['email' => 'parent.test@csndr.test'],
            [
                'nom' => 'Parent',
                'prenom' => 'Test',
                'mot_de_passe' => Hash::make('password'),
                'role' => 'parent',
                'classe_id' => null
            ]
        );

        // Créer des enfants pour ce parent
        $enfant1 = User::firstOrCreate(
            ['email' => 'enfant1.test@csndr.test'],
            [
                'nom' => 'Enfant1',
                'prenom' => 'Test',
                'mot_de_passe' => Hash::make('password'),
                'role' => 'eleve',
                'classe_id' => $classe->id,
                'parent_id' => $parent->id
            ]
        );

        $enfant2 = User::firstOrCreate(
            ['email' => 'enfant2.test@csndr.test'],
            [
                'nom' => 'Enfant2',
                'prenom' => 'Test',
                'mot_de_passe' => Hash::make('password'),
                'role' => 'eleve',
                'classe_id' => $classe->id,
                'parent_id' => $parent->id
            ]
        );

        // Créer des devoirs pour la classe
        $devoir1 = Homework::firstOrCreate(
            ['titre' => 'Devoir Test Mathématiques'],
            [
                'description' => 'Exercices de mathématiques pour tester le système parent',
                'date_limite' => now()->addDays(7)->format('Y-m-d'),
                'classe_id' => $classe->id,
                'professeur_id' => $professeur->id
            ]
        );

        $devoir2 = Homework::firstOrCreate(
            ['titre' => 'Devoir Test Français'],
            [
                'description' => 'Exercices de français pour tester le système parent',
                'date_limite' => now()->addDays(10)->format('Y-m-d'),
                'classe_id' => $classe->id,
                'professeur_id' => $professeur->id
            ]
        );

        // Créer des notes pour les enfants
        Grade::firstOrCreate(
            [
                'eleve_id' => $enfant1->id,
                'matiere' => 'Mathématiques Test',
                'professeur_id' => $professeur->id
            ],
            [
                'note' => 15.5,
                'commentaire' => 'Très bon travail en mathématiques',
                'coefficient' => 1,
                'type_evaluation' => 'Devoir'
            ]
        );

        Grade::firstOrCreate(
            [
                'eleve_id' => $enfant1->id,
                'matiere' => 'Français Test',
                'professeur_id' => $professeur->id
            ],
            [
                'note' => 17.0,
                'commentaire' => 'Excellent en français',
                'coefficient' => 1,
                'type_evaluation' => 'Devoir'
            ]
        );

        Grade::firstOrCreate(
            [
                'eleve_id' => $enfant2->id,
                'matiere' => 'Mathématiques Test',
                'professeur_id' => $professeur->id
            ],
            [
                'note' => 13.0,
                'commentaire' => 'Bon travail, peut mieux faire',
                'coefficient' => 1,
                'type_evaluation' => 'Devoir'
            ]
        );

        Grade::firstOrCreate(
            [
                'eleve_id' => $enfant2->id,
                'matiere' => 'Français Test',
                'professeur_id' => $professeur->id
            ],
            [
                'note' => 16.5,
                'commentaire' => 'Très bien en français',
                'coefficient' => 1,
                'type_evaluation' => 'Devoir'
            ]
        );

        echo "✅ Données de test parent-enfant créées :\n";
        echo "- Parent: {$parent->email} (ID: {$parent->id})\n";
        echo "- Enfant 1: {$enfant1->email} (ID: {$enfant1->id})\n";
        echo "- Enfant 2: {$enfant2->email} (ID: {$enfant2->id})\n";
        echo "- Classe: {$classe->nom} (ID: {$classe->id})\n";
        echo "- 2 devoirs créés\n";
        echo "- 4 notes créées\n";
    }
}

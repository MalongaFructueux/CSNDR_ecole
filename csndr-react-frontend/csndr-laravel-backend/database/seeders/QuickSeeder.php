<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QuickSeeder extends Seeder
{
    public function run()
    {
        // Créer des classes
        DB::table('classes')->insert([
            ['nom' => '6ème A', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => '5ème B', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => '4ème C', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Créer des utilisateurs
        DB::table('users')->insert([
            [
                'nom' => 'Admin',
                'prenom' => 'System',
                'email' => 'admin@csndr.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'classe_id' => null,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'prof@csndr.com',
                'password' => Hash::make('password'),
                'role' => 'professeur',
                'classe_id' => 1,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'email' => 'parent@csndr.com',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'classe_id' => null,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Pierre',
                'email' => 'eleve@csndr.com',
                'password' => Hash::make('password'),
                'role' => 'eleve',
                'classe_id' => 1,
                'parent_id' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Créer des événements
        DB::table('evenements')->insert([
            [
                'titre' => 'Rentrée scolaire',
                'description' => 'Début de l\'année scolaire 2025',
                'date' => '2025-09-01',
                'auteur_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'titre' => 'Réunion parents',
                'description' => 'Réunion d\'information pour les parents',
                'date' => '2025-09-15',
                'auteur_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Créer des devoirs
        DB::table('devoirs')->insert([
            [
                'titre' => 'Mathématiques - Exercices',
                'description' => 'Exercices page 45-46',
                'date_limite' => '2025-09-10',
                'classe_id' => 1,
                'professeur_id' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Créer des notes
        DB::table('notes')->insert([
            [
                'note' => 15.5,
                'matiere' => 'Mathématiques',
                'eleve_id' => 4,
                'professeur_id' => 2,
                'commentaire' => 'Bon travail',
                'type_evaluation' => 'Contrôle',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Créer des messages
        DB::table('messages')->insert([
            [
                'contenu' => 'Bienvenue dans le système de messagerie',
                'expediteur_id' => 1,
                'destinataire_id' => 3,
                'date_envoi' => now(),
                'lu' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si la table notes existe
        if (!DB::getSchemaBuilder()->hasTable('notes')) {
            echo "Table 'notes' n'existe pas. Création...\n";
            
            DB::statement("
                CREATE TABLE notes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    note DECIMAL(4,2) NOT NULL,
                    matiere VARCHAR(100) NOT NULL,
                    commentaire TEXT,
                    eleve_id INT NOT NULL,
                    professeur_id INT NOT NULL,
                    coefficient DECIMAL(3,2) DEFAULT 1.0,
                    date DATE,
                    type_evaluation VARCHAR(100) DEFAULT 'Contrôle',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            
            echo "Table 'notes' créée.\n";
        }

        // Vider la table
        DB::table('notes')->truncate();

        // Insérer des données de test
        $notes = [
            [
                'note' => 15.5,
                'matiere' => 'Mathématiques',
                'commentaire' => 'Très bon travail sur les équations',
                'eleve_id' => 1,
                'professeur_id' => 2,
                'coefficient' => 2.0,
                'date' => Carbon::now()->subDays(10),
                'type_evaluation' => 'Contrôle',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'note' => 12.0,
                'matiere' => 'Français',
                'commentaire' => 'Peut mieux faire en orthographe',
                'eleve_id' => 1,
                'professeur_id' => 2,
                'coefficient' => 1.5,
                'date' => Carbon::now()->subDays(5),
                'type_evaluation' => 'Devoir',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'note' => 18.0,
                'matiere' => 'Sciences',
                'commentaire' => 'Excellent travail !',
                'eleve_id' => 1,
                'professeur_id' => 2,
                'coefficient' => 1.0,
                'date' => Carbon::now()->subDays(3),
                'type_evaluation' => 'Exposé',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('notes')->insert($notes);

        echo "Notes de test insérées avec succès.\n";
    }
}

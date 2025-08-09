<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email' => 'admin@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'admin',
                'classe_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Dupont',
                'prenom' => 'Paul',
                'email' => 'prof@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'professeur',
                'classe_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Claire',
                'email' => 'parent@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'parent',
                'classe_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Amadou',
                'email' => 'eleve@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Upsert to avoid duplicates on re-seed
        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}

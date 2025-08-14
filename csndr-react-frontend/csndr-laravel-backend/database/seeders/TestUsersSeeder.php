<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Classe;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer d'abord quelques classes de test (structure simple)
        $classes = [
            ['nom' => 'CP-A'],
            ['nom' => 'CE1-A'],
            ['nom' => 'CE2-A'],
            ['nom' => 'CM1-A'],
            ['nom' => 'CM2-A'],
        ];

        foreach ($classes as $classeData) {
            $classe = Classe::firstOrCreate(
                ['nom' => $classeData['nom']],
                $classeData
            );
        }

        // Récupérer les IDs des classes
        $cpClass = Classe::where('nom', 'CP-A')->first();
        $ce1Class = Classe::where('nom', 'CE1-A')->first();
        $ce2Class = Classe::where('nom', 'CE2-A')->first();
        $cm1Class = Classe::where('nom', 'CM1-A')->first();
        $cm2Class = Classe::where('nom', 'CM2-A')->first();

        // 1. Créer un compte ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@csndr.test'],
            [
                'nom' => 'Admin',
                'prenom' => 'Principal',
                'email' => 'admin@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'admin',
                'classe_id' => null,
                'parent_id' => null,
            ]
        );

        // 2. Créer des comptes PROFESSEURS
        $prof1 = User::firstOrCreate(
            ['email' => 'prof1@csndr.test'],
            [
                'nom' => 'Martin',
                'prenom' => 'Sophie',
                'email' => 'prof1@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'professeur',
                'classe_id' => $cpClass->id,
                'parent_id' => null,
            ]
        );

        $prof2 = User::firstOrCreate(
            ['email' => 'prof2@csndr.test'],
            [
                'nom' => 'Dubois',
                'prenom' => 'Pierre',
                'email' => 'prof2@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'professeur',
                'classe_id' => $ce1Class->id,
                'parent_id' => null,
            ]
        );

        $prof3 = User::firstOrCreate(
            ['email' => 'prof3@csndr.test'],
            [
                'nom' => 'Leroy',
                'prenom' => 'Marie',
                'email' => 'prof3@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'professeur',
                'classe_id' => $ce2Class->id,
                'parent_id' => null,
            ]
        );

        // 3. Créer des comptes PARENTS
        $parent1 = User::firstOrCreate(
            ['email' => 'parent1@csndr.test'],
            [
                'nom' => 'Durand',
                'prenom' => 'Jean',
                'email' => 'parent1@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'parent',
                'classe_id' => null,
                'parent_id' => null,
            ]
        );

        $parent2 = User::firstOrCreate(
            ['email' => 'parent2@csndr.test'],
            [
                'nom' => 'Moreau',
                'prenom' => 'Claire',
                'email' => 'parent2@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'parent',
                'classe_id' => null,
                'parent_id' => null,
            ]
        );

        $parent3 = User::firstOrCreate(
            ['email' => 'parent3@csndr.test'],
            [
                'nom' => 'Simon',
                'prenom' => 'Marc',
                'email' => 'parent3@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'parent',
                'classe_id' => null,
                'parent_id' => null,
            ]
        );

        // 4. Créer des comptes ÉLÈVES
        $eleve1 = User::firstOrCreate(
            ['email' => 'eleve1@csndr.test'],
            [
                'nom' => 'Durand',
                'prenom' => 'Emma',
                'email' => 'eleve1@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => $cpClass->id,
                'parent_id' => $parent1->id,
            ]
        );

        $eleve2 = User::firstOrCreate(
            ['email' => 'eleve2@csndr.test'],
            [
                'nom' => 'Moreau',
                'prenom' => 'Lucas',
                'email' => 'eleve2@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => $ce1Class->id,
                'parent_id' => $parent2->id,
            ]
        );

        $eleve3 = User::firstOrCreate(
            ['email' => 'eleve3@csndr.test'],
            [
                'nom' => 'Simon',
                'prenom' => 'Léa',
                'email' => 'eleve3@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => $ce2Class->id,
                'parent_id' => $parent3->id,
            ]
        );

        $eleve4 = User::firstOrCreate(
            ['email' => 'eleve4@csndr.test'],
            [
                'nom' => 'Durand',
                'prenom' => 'Thomas',
                'email' => 'eleve4@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => $cm1Class->id,
                'parent_id' => $parent1->id,
            ]
        );

        $eleve5 = User::firstOrCreate(
            ['email' => 'eleve5@csndr.test'],
            [
                'nom' => 'Moreau',
                'prenom' => 'Jade',
                'email' => 'eleve5@csndr.test',
                'mot_de_passe' => Hash::make('Password123!'),
                'role' => 'eleve',
                'classe_id' => $cm2Class->id,
                'parent_id' => $parent2->id,
            ]
        );

        echo "✅ Comptes de test créés avec succès dans csndr_db !\n\n";
        echo "🔑 Comptes de test disponibles :\n";
        echo "   ADMIN: admin@csndr.test / Password123!\n";
        echo "   PROFESSEURS:\n";
        echo "     - prof1@csndr.test / Password123! (CP-A)\n";
        echo "     - prof2@csndr.test / Password123! (CE1-A)\n";
        echo "     - prof3@csndr.test / Password123! (CE2-A)\n";
        echo "   PARENTS:\n";
        echo "     - parent1@csndr.test / Password123!\n";
        echo "     - parent2@csndr.test / Password123!\n";
        echo "     - parent3@csndr.test / Password123!\n";
        echo "   ÉLÈVES:\n";
        echo "     - eleve1@csndr.test / Password123! (CP-A, Parent: Durand)\n";
        echo "     - eleve2@csndr.test / Password123! (CE1-A, Parent: Moreau)\n";
        echo "     - eleve3@csndr.test / Password123! (CE2-A, Parent: Simon)\n";
        echo "     - eleve4@csndr.test / Password123! (CM1-A, Parent: Durand)\n";
        echo "     - eleve5@csndr.test / Password123! (CM2-A, Parent: Moreau)\n\n";
        echo "📚 Classes créées : CP-A, CE1-A, CE2-A, CM1-A, CM2-A\n";
    }
}

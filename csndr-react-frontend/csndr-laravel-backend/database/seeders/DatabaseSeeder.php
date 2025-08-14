<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les comptes de test dans csndr_db
        $this->call([
            TestUsersSeeder::class,
        ]);
    }
}

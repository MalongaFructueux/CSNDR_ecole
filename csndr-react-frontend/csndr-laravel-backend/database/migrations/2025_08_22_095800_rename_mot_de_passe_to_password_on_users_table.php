<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomme la colonne mot_de_passe -> password si elle existe encore
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'mot_de_passe')) {
            // ATTENTION: renameColumn requiert le package doctrine/dbal
            // Exécutez si besoin: composer require doctrine/dbal
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('mot_de_passe', 'password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir en arrière uniquement si le renommage a été fait
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'password') && !Schema::hasColumn('users', 'mot_de_passe')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('password', 'mot_de_passe');
            });
        }
    }
};

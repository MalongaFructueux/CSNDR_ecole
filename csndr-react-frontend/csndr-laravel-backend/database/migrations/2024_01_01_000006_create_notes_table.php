<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->decimal('note', 4, 2); // Note sur 20 avec 2 décimales
            $table->string('matiere', 100);
            $table->text('commentaire')->nullable();
            $table->unsignedBigInteger('eleve_id');
            $table->unsignedBigInteger('professeur_id');
            $table->decimal('coefficient', 3, 2)->default(1.0);
            $table->date('date')->nullable();
            $table->string('type_evaluation', 100)->default('Contrôle');
            $table->timestamps();

            // Clés étrangères
            $table->foreign('eleve_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professeur_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index pour les performances
            $table->index(['eleve_id', 'matiere']);
            $table->index('professeur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};

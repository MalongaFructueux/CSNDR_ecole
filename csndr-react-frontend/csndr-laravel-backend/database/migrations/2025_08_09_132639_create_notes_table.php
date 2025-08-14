<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des notes
class CreateNotesTable extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->unsignedBigInteger('eleve_id'); // Clé étrangère
            $table->string('matiere'); // Matière
            $table->decimal('note', 5, 2); // Note (ex. : 15.50)
            $table->decimal('coefficient', 3, 1)->default(1.0); // Coefficient de la note
            $table->text('commentaire')->nullable(); // Commentaire du professeur
            $table->unsignedBigInteger('professeur_id'); // Clé étrangère
            $table->date('date'); // Date de la note
            $table->timestamps();

            $table->foreign('eleve_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professeur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
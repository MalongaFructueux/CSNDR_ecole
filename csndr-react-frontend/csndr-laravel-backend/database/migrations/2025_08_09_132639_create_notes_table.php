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
            $table->unsignedBigInteger('professeur_id'); // Clé étrangère
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
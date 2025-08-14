<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des devoirs
class CreateDevoirsTable extends Migration
{
    public function up()
    {
        Schema::create('devoirs', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->string('titre'); // Titre du devoir
            $table->text('description'); // Description
            $table->date('date_limite'); // Date limite
            $table->unsignedBigInteger('professeur_id'); // Clé étrangère
            $table->unsignedBigInteger('classe_id'); // Clé étrangère
            $table->string('fichier_attachment')->nullable(); // Chemin vers le fichier joint
            $table->string('nom_fichier_original')->nullable(); // Nom original du fichier
            $table->string('type_fichier')->nullable(); // Type MIME du fichier
            $table->timestamps();

            $table->foreign('professeur_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devoirs');
    }
}
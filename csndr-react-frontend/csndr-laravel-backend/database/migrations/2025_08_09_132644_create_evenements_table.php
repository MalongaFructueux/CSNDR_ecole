<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des événements
class CreateEvenementsTable extends Migration
{
    public function up()
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->string('titre'); // Titre
            $table->text('description'); // Description
            $table->datetime('date_debut'); // Date et heure de début
            $table->datetime('date_fin'); // Date et heure de fin
            $table->unsignedBigInteger('auteur_id'); // Clé étrangère
            $table->timestamps();

            $table->foreign('auteur_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evenements');
    }
}
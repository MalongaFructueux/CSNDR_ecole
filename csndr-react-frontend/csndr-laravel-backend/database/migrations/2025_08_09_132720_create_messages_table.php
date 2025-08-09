<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des messages internes
class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->unsignedBigInteger('expediteur_id'); // Clé étrangère pour l'expéditeur
            $table->unsignedBigInteger('destinataire_id'); // Clé étrangère pour le destinataire
            $table->text('contenu'); // Contenu du message
            $table->dateTime('date_envoi'); // Date et heure d'envoi
            $table->timestamps();

            $table->foreign('expediteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('destinataire_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
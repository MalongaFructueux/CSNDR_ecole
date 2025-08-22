<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des utilisateurs
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Identifiant unique auto-incrémenté (clé primaire)
            $table->id();
            // Nom de l'utilisateur
            $table->string('nom');
            // Prénom de l'utilisateur
            $table->string('prenom');
            // Email unique (limité à 191 caractères pour MySQL avec utf8mb4)
            $table->string('email', 191)->unique();
            // Mot de passe (sera chiffré)
            $table->string('mot_de_passe');
            // Rôle de l'utilisateur (admin, professeur, parent, élève)
            $table->enum('role', ['admin', 'professeur', 'parent', 'eleve']);
            // Clé étrangère vers la table des classes (peut être nulle)
            $table->unsignedBigInteger('classe_id')->nullable();
            // Clé étrangère vers la table des parents (peut être nulle)
            $table->unsignedBigInteger('parent_id')->nullable();
            // Timestamps pour suivre la création et les mises à jour
            $table->timestamps();

            // Définition de la contrainte de clé étrangère pour la classe
            $table->foreign('classe_id')->references('id')->on('classes')->onDelete('set null');
            // Définition de la contrainte de clé étrangère pour le parent
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        // Supprime la table si la migration est annulée
        Schema::dropIfExists('users');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration pour la table des menus cantine
class CreateMenusTable extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // Identifiant unique
            $table->date('jour'); // Date du menu
            $table->text('plat'); // Description du plat
            $table->text('allergenes')->nullable(); // Allergènes (peut être vide)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
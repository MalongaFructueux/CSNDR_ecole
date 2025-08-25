<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeEvaluationToNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            // Ajoute le type d'évaluation s'il n'existe pas
            if (!Schema::hasColumn('notes', 'type_evaluation')) {
                $table->string('type_evaluation', 100)->nullable()->default('Devoir')->after('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            if (Schema::hasColumn('notes', 'type_evaluation')) {
                $table->dropColumn('type_evaluation');
            }
        });
    }
}

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Structure de la table 'notes':\n";
$columns = Schema::getColumnListing('notes');
foreach ($columns as $column) {
    $type = DB::getSchemaBuilder()->getColumnType('notes', $column);
    echo "- $column ($type)\n";
}

echo "\nStructure de la table 'devoirs':\n";
$columns = Schema::getColumnListing('devoirs');
foreach ($columns as $column) {
    $type = DB::getSchemaBuilder()->getColumnType('devoirs', $column);
    echo "- $column ($type)\n";
}

echo "\nStructure de la table 'messages':\n";
$columns = Schema::getColumnListing('messages');
foreach ($columns as $column) {
    $type = DB::getSchemaBuilder()->getColumnType('messages', $column);
    echo "- $column ($type)\n";
}

<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Hash;

// Configuration de la base de données
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'csndr_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Vérifier si l'admin existe déjà
    $existingAdmin = $capsule->table('users')
        ->where('email', 'admin@csndr.com')
        ->first();
    
    if ($existingAdmin) {
        echo "L'utilisateur admin existe déjà.\n";
        echo "Email: admin@csndr.com\n";
        echo "Mot de passe: admin123\n";
    } else {
        // Créer l'utilisateur admin
        $adminId = $capsule->table('users')->insertGetId([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@csndr.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'classe_id' => null,
            'parent_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($adminId) {
            echo "✅ Utilisateur admin créé avec succès !\n";
            echo "Email: admin@csndr.com\n";
            echo "Mot de passe: admin123\n";
            echo "Rôle: admin\n";
        } else {
            echo "❌ Erreur lors de la création de l'admin\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

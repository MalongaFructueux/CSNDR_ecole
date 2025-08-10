<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\GradeController;
use App\Models\Classe;

/**
 * Routes API pour le Centre Scolaire Notre Dame du Rosaire
 * 
 * Ce fichier contient toutes les routes API de l'application.
 * Toutes les routes sont protégées par authentification sauf la connexion.
 * 
 * Structure des routes :
 * - Routes d'authentification (publiques)
 * - Routes protégées par middleware auth:sanctum
 * - Routes organisées par fonctionnalité
 */

// ============================================================================
// ROUTES D'AUTHENTIFICATION (PUBLIQUES)
// ============================================================================

/**
 * Route de connexion - Authentification des utilisateurs
 * POST /api/auth/login
 * Paramètres : email, password
 * Retourne : token JWT et informations utilisateur
 */
Route::post('/auth/login', [AuthController::class, 'login']);

/**
 * Route de déconnexion - Invalidation du token
 * POST /api/auth/logout
 * Nécessite : Token JWT dans le header Authorization
 */
Route::middleware('auth:sanctum')->post('/auth/logout', [AuthController::class, 'logout']);

// ============================================================================
// ROUTES PROTÉGÉES (NÉCESSITENT UNE AUTHENTIFICATION)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // ========================================================================
    // GESTION DES UTILISATEURS
    // ========================================================================
    
    /**
     * Récupération de tous les utilisateurs
     * GET /api/users
     * Retourne : Liste des utilisateurs avec leurs rôles
     */
    Route::get('/users', [UserController::class, 'index']);
    
    /**
     * Création d'un nouvel utilisateur
     * POST /api/users
     * Paramètres : nom, prenom, email, mot_de_passe, role, classe_id, parent_id
     * Restriction : Admin uniquement
     */
    Route::post('/users', [UserController::class, 'store']);
    
    /**
     * Suppression d'un utilisateur
     * DELETE /api/users/{id}
     * Restriction : Admin uniquement
     */
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // ========================================================================
    // GESTION DES CLASSES
    // ========================================================================
    
    /**
     * Récupération de toutes les classes
     * GET /api/classes
     * Retourne : Liste des classes avec leurs informations
     */
    Route::get('/classes', function () {
        return response()->json(Classe::select('id','nom','created_at','updated_at')->get());
    });
    
    /**
     * Création d'une nouvelle classe
     * POST /api/classes
     * Paramètres : nom
     * Restriction : Admin uniquement
     */
    Route::post('/classes', function (Request $request) {
        $validated = $request->validate([
            'nom' => ['required','string','max:255'],
        ]);
        $classe = Classe::create(['nom' => $validated['nom']]);
        return response()->json(['message' => 'Classe créée', 'classe' => $classe], 201);
    });
    
    /**
     * Suppression d'une classe
     * DELETE /api/classes/{id}
     * Restriction : Admin uniquement
     */
    Route::delete('/classes/{id}', function ($id) {
        $classe = Classe::findOrFail($id);
        $classe->delete();
        return response()->json(['message' => 'Classe supprimée']);
    });

    // ========================================================================
    // GESTION DES MESSAGES
    // ========================================================================
    
    /**
     * Récupération de tous les messages de l'utilisateur connecté
     * GET /api/messages
     * Retourne : Messages envoyés et reçus
     */
    Route::get('/messages', [MessageController::class, 'index']);
    
    /**
     * Récupération des conversations de l'utilisateur connecté
     * GET /api/messages/conversations
     * Retourne : Conversations groupées par utilisateur
     */
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    
    /**
     * Récupération des utilisateurs disponibles pour la discussion
     * GET /api/messages/available-users
     * Retourne : Liste des utilisateurs avec qui on peut discuter
     */
    Route::get('/messages/available-users', [MessageController::class, 'getAvailableUsers']);
    
    /**
     * Envoi d'un nouveau message
     * POST /api/messages
     * Paramètres : destinataire_id, contenu
     * Restriction : Admin, Professeur, Parent uniquement
     */
    Route::post('/messages', [MessageController::class, 'store']);
    
    /**
     * Récupération des messages d'une conversation spécifique
     * GET /api/messages/{id}
     * Paramètres : id (ID de l'utilisateur avec qui on converse)
     * Retourne : Messages de la conversation
     */
    Route::get('/messages/{id}', [MessageController::class, 'show']);

    // ========================================================================
    // GESTION DES ÉVÉNEMENTS
    // ========================================================================
    
    /**
     * Récupération de tous les événements
     * GET /api/events
     * Retourne : Liste des événements avec les informations des auteurs
     */
    Route::get('/events', [EventController::class, 'index']);
    
    /**
     * Création d'un nouvel événement
     * POST /api/events
     * Paramètres : titre, description, date
     * Restriction : Admin uniquement
     */
    Route::post('/events', [EventController::class, 'store']);
    
    /**
     * Récupération d'un événement spécifique
     * GET /api/events/{id}
     * Retourne : Détails de l'événement
     */
    Route::get('/events/{id}', [EventController::class, 'show']);
    
    /**
     * Modification d'un événement
     * PUT /api/events/{id}
     * Paramètres : titre, description, date
     * Restriction : Admin uniquement
     */
    Route::put('/events/{id}', [EventController::class, 'update']);
    
    /**
     * Suppression d'un événement
     * DELETE /api/events/{id}
     * Restriction : Admin uniquement
     */
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    // ========================================================================
    // GESTION DES DEVOIRS
    // ========================================================================
    
    /**
     * Récupération des devoirs selon le rôle
     * GET /api/homework
     * Retourne : Devoirs filtrés selon le rôle de l'utilisateur
     */
    Route::get('/homework', [HomeworkController::class, 'index']);
    
    /**
     * Création d'un nouveau devoir
     * POST /api/homework
     * Paramètres : titre, description, date_limite, classe_id
     * Restriction : Admin et Professeur uniquement
     */
    Route::post('/homework', [HomeworkController::class, 'store']);
    
    /**
     * Récupération d'un devoir spécifique
     * GET /api/homework/{id}
     * Retourne : Détails du devoir avec les relations
     */
    Route::get('/homework/{id}', [HomeworkController::class, 'show']);
    
    /**
     * Modification d'un devoir
     * PUT /api/homework/{id}
     * Paramètres : titre, description, date_limite, classe_id
     * Restriction : Admin et Professeur créateur uniquement
     */
    Route::put('/homework/{id}', [HomeworkController::class, 'update']);
    
    /**
     * Suppression d'un devoir
     * DELETE /api/homework/{id}
     * Restriction : Admin et Professeur créateur uniquement
     */
    Route::delete('/homework/{id}', [HomeworkController::class, 'destroy']);

    // ========================================================================
    // GESTION DES NOTES
    // ========================================================================
    
    /**
     * Récupération des notes selon le rôle
     * GET /api/grades
     * Retourne : Notes filtrées selon le rôle de l'utilisateur
     */
    Route::get('/grades', [GradeController::class, 'index']);
    
    /**
     * Création d'une nouvelle note
     * POST /api/grades
     * Paramètres : note, matiere, commentaire, eleve_id
     * Restriction : Admin et Professeur uniquement
     */
    Route::post('/grades', [GradeController::class, 'store']);
    
    /**
     * Récupération d'une note spécifique
     * GET /api/grades/{id}
     * Retourne : Détails de la note avec les relations
     */
    Route::get('/grades/{id}', [GradeController::class, 'show']);
    
    /**
     * Modification d'une note
     * PUT /api/grades/{id}
     * Paramètres : note, matiere, commentaire, eleve_id
     * Restriction : Admin et Professeur créateur uniquement
     */
    Route::put('/grades/{id}', [GradeController::class, 'update']);
    
    /**
     * Suppression d'une note
     * DELETE /api/grades/{id}
     * Restriction : Admin et Professeur créateur uniquement
     */
    Route::delete('/grades/{id}', [GradeController::class, 'destroy']);
});
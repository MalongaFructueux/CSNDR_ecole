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
// Middleware 'auth' retiré: toutes les routes suivantes sont publiques
// Authentification
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/check', [AuthController::class, 'checkAuth']);

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
 * Modification d'un utilisateur existant
 * PUT /api/users/{id}
 * Paramètres : nom, prenom, email, mot_de_passe (optionnel), role, classe_id, parent_id
 * Restriction : Admin uniquement
 */
Route::put('/users/{id}', [UserController::class, 'update']);

/**
 * Suppression d'un utilisateur
 * DELETE /api/users/{id}
 * Restriction : Admin uniquement
 */
Route::delete('/users/{id}', [UserController::class, 'destroy']);

/**
 * Recherche d'utilisateurs par nom/prénom/email
 * GET /api/users/search?query=terme
 * Retourne : Liste des utilisateurs correspondants
 */
Route::get('/users/search', [UserController::class, 'searchUsers']);

/**
 * Récupération des élèves d'un professeur
 * GET /api/users/teacher/{teacherId}/students
 * Restriction : Professeur concerné ou Admin
 */
Route::get('/users/teacher/{teacherId}/students', [UserController::class, 'getStudentsByTeacher']);

/**
 * Récupération des enfants d'un parent
 * GET /api/users/parent/{parentId}/children
 * Restriction : Parent concerné ou Admin
 */
Route::get('/users/parent/{parentId}/children', [UserController::class, 'getChildrenByParent']);

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
 * Modification d'une classe existante
 * PUT /api/classes/{id}
 * Paramètres : nom
 * Restriction : Admin uniquement
 */
Route::put('/classes/{id}', function (Request $request, $id) {
    $classe = Classe::findOrFail($id);
    $validated = $request->validate([
        'nom' => ['required','string','max:255'],
    ]);
    $classe->update(['nom' => $validated['nom']]);
    return response()->json(['message' => 'Classe modifiée', 'classe' => $classe]);
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
 * Récupération des messages
 * GET /api/messages
 * Retourne : Messages selon le rôle de l'utilisateur
 */
Route::get('/messages', [MessageController::class, 'index']);

/**
 * Récupération des conversations
 * GET /api/messages/conversations
 * Retourne : Conversations de l'utilisateur connecté
 */
Route::get('/messages/conversations', [MessageController::class, 'conversations']);

// Récupération des utilisateurs disponibles pour la discussion
Route::get('/messages/available-users', [MessageController::class, 'getAvailableUsers']);

/**
 * Envoi d'un message
 * POST /api/messages
 * Paramètres : destinataire_id, contenu
 * Retourne : Message créé
 */
Route::post('/messages', [MessageController::class, 'store']);

/**
 * Récupération des messages d'une conversation
 * GET /api/messages/conversations/{id}
 * Retourne : Messages d'une conversation spécifique
 */
Route::get('/messages/conversations/{id}', [MessageController::class, 'show']);

/**
 * Marque les messages d'une conversation comme lus
 * POST /api/messages/read/{conversationId}
 */
Route::post('/messages/read/{conversationId}', [MessageController::class, 'markAsRead']);

// ========================================================================
// GESTION DES ÉVÉNEMENTS
// ========================================================================

/**
 * Récupération des événements
 * GET /api/events
 * Retourne : Tous les événements (visibles par tous)
 */
Route::get('/events', [EventController::class, 'index']);

/**
 * Création d'un événement
 * POST /api/events
 * Paramètres : titre, description, date_debut, date_fin
 * Restriction : Admin uniquement
 */
Route::post('/events', [EventController::class, 'store']);

/**
 * Modification d'un événement
 * PUT /api/events/{id}
 * Paramètres : titre, description, date_debut, date_fin
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
 * Récupération des devoirs
 * GET /api/homework
 * Retourne : Devoirs selon le rôle de l'utilisateur
 */
Route::get('/homework', [HomeworkController::class, 'index']);

/**
 * Création d'un devoir
 * POST /api/homework
 * Paramètres : titre, description, date_limite, classe_id, fichier (optionnel)
 * Restriction : Admin et Professeur uniquement
 */
Route::post('/homework', [HomeworkController::class, 'store']);

/**
 * Récupération d'un devoir spécifique
 * GET /api/homework/{id}
 * Retourne : Détails du devoir
 */
Route::get('/homework/{id}', [HomeworkController::class, 'show']);

/**
 * Modification d'un devoir
 * PUT /api/homework/{id}
 * Paramètres : titre, description, date_limite, classe_id, fichier (optionnel)
 * Restriction : Admin et Professeur créateur uniquement
 */
Route::put('/homework/{id}', [HomeworkController::class, 'update']);

/**
 * Suppression d'un devoir
 * DELETE /api/homework/{id}
 * Restriction : Admin et Professeur créateur uniquement
 */
Route::delete('/homework/{id}', [HomeworkController::class, 'destroy']);

/**
 * Téléchargement du fichier d'un devoir
 * GET /api/homework/{id}/download
 * Retourne : Fichier du devoir
 */
Route::get('/homework/{id}/download', [HomeworkController::class, 'downloadFile']);

// ========================================================================
// GESTION DES NOTES
// ========================================================================

/**
 * Récupération des notes
 * GET /api/grades
 * Retourne : Notes selon le rôle de l'utilisateur
 */
Route::get('/grades', [GradeController::class, 'index']);

/**
 * Création d'une note
 * POST /api/grades
 * Paramètres : eleve_id, matiere, note, coefficient, commentaire
 * Restriction : Admin et Professeur uniquement
 */
Route::post('/grades', [GradeController::class, 'store']);

/**
 * Récupération d'une note spécifique
 * GET /api/grades/{id}
 * Retourne : Détails de la note
 */
Route::get('/grades/{id}', [GradeController::class, 'show']);

/**
 * Modification d'une note
 * PUT /api/grades/{id}
 * Paramètres : eleve_id, matiere, note, coefficient, commentaire
 * Restriction : Admin et Professeur créateur uniquement
 */
Route::put('/grades/{id}', [GradeController::class, 'update']);

/**
 * Suppression d'une note
 * DELETE /api/grades/{id}
 * Restriction : Admin et Professeur créateur uniquement
 */
Route::delete('/grades/{id}', [GradeController::class, 'destroy']);
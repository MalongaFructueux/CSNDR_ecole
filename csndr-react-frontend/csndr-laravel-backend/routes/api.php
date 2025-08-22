<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UseController;
use App\Http\Controllers\ClassController;
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
 * Route d'inscription - Création de nouveaux comptes (publique)
 * POST /api/auth/register
 * Paramètres : nom, prenom, email, password, password_confirmation, role, classe_id, parent_id
 * Retourne : token JWT et informations utilisateur
 */
Route::post('/auth/register', [UseController::class, 'register']);

/**
 * Vérification de disponibilité d'email (publique)
 * POST /api/auth/check-email
 * Paramètres : email
 * Retourne : disponibilité de l'email
 */
Route::post('/auth/check-email', [UseController::class, 'checkEmail']);

/**
 * Récupération des parents disponibles pour inscription (publique)
 * GET /api/auth/available-parents
 * Retourne : Liste des parents pour sélection lors de l'inscription d'élèves
 */
Route::get('/auth/available-parents', [UseController::class, 'getAvailableParents']);

/**
 * Récupération des classes disponibles pour inscription (publique)
 * GET /api/auth/available-classes
 * Retourne : Liste des classes pour sélection lors de l'inscription
 */
Route::get('/auth/available-classes', [UseController::class, 'getAvailableClasses']);

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
     * Paramètres : nom, prenom, email, password, role, classe_id, parent_id
     * Restriction : Admin uniquement
     */
    Route::post('/users', [UserController::class, 'store']);
    
    /**
     * Modification d'un utilisateur existant
     * PUT /api/users/{id}
     * Paramètres : nom, prenom, email, password (optionnel), role, classe_id, parent_id
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
    Route::get('/classes', [ClassController::class, 'index']);
    
    /**
     * Création d'une nouvelle classe
     * POST /api/classes
     * Paramètres : nom
     * Restriction : Admin uniquement
     */
    Route::post('/classes', [ClassController::class, 'store']);

    /**
     * Récupération d'une classe spécifique
     * GET /api/classes/{id}
     * Retourne : Détails de la classe
     */
    Route::get('/classes/{id}', [ClassController::class, 'show']);

    /**
     * Modification d'une classe existante
     * PUT /api/classes/{id}
     * Paramètres : nom
     * Restriction : Admin uniquement
     */
    Route::put('/classes/{id}', [ClassController::class, 'update']);

    /**
     * Suppression d'une classe
     * DELETE /api/classes/{id}
     * Restriction : Admin uniquement
     */
    Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

    /**
     * Récupération des élèves d'une classe
     * GET /api/classes/{id}/students
     * Retourne : Liste des élèves de la classe
     */
    Route::get('/classes/{id}/students', [ClassController::class, 'getStudents']);

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
});
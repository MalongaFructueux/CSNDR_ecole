<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur EventController - Gestion des événements du Centre Scolaire
 * 
 * Ce contrôleur gère :
 * - L'affichage de tous les événements (visible par tous)
 * - La création d'événements (Admin uniquement)
 * - La modification d'événements (Admin uniquement)
 * - La suppression d'événements (Admin uniquement)
 * 
 * Fonctionnalités principales :
 * - Gestion complète du CRUD des événements
 * - Restrictions d'accès selon les rôles
 * - Validation des données
 * - Gestion des dates et descriptions
 * 
 * Restrictions par rôle :
 * - Admin : Accès complet (création, modification, suppression)
 * - Autres rôles : Lecture seule
 */
class EventController extends Controller
{
    /**
     * Constructeur - Applique le middleware d'authentification
     * Toutes les routes nécessitent une authentification
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Récupère tous les événements (visible par tous les utilisateurs)
     * Retourne les événements triés par date décroissante
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Récupération de tous les événements avec les informations de l'auteur
        $events = Event::with('auteur')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($events);
    }

    /**
     * Crée un nouvel événement (Admin uniquement)
     * Valide les données et vérifie les permissions
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Vérification que l'utilisateur est admin
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé - Admin seulement'], 403);
        }

        // Validation des données reçues
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date'
        ]);

        // Création du nouvel événement
        $event = Event::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $request->date,
            'auteur_id' => $user->id
        ]);

        // Chargement des relations pour la réponse
        $event->load('auteur');

        return response()->json($event, 201);
    }

    /**
     * Récupère un événement spécifique
     * Retourne les détails d'un événement avec les informations de l'auteur
     * 
     * @param int $id - ID de l'événement
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Récupération de l'événement avec les relations
        $event = Event::with('auteur')->findOrFail($id);
        return response()->json($event);
    }

    /**
     * Met à jour un événement existant (Admin uniquement)
     * Valide les données et vérifie les permissions
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id - ID de l'événement
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        // Vérification que l'utilisateur est admin
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé - Admin seulement'], 403);
        }

        // Récupération de l'événement
        $event = Event::findOrFail($id);

        // Validation des données reçues
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date'
        ]);

        // Mise à jour de l'événement
        $event->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'date' => $request->date
        ]);

        // Chargement des relations pour la réponse
        $event->load('auteur');

        return response()->json($event);
    }

    /**
     * Supprime un événement (Admin uniquement)
     * Vérifie les permissions avant la suppression
     * 
     * @param int $id - ID de l'événement
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Vérification que l'utilisateur est admin
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé - Admin seulement'], 403);
        }

        // Récupération et suppression de l'événement
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Événement supprimé avec succès']);
    }
}

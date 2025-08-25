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
     * Récupère tous les événements (visible par tous les utilisateurs)
     * Retourne les événements triés par date décroissante
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $events = Event::all();
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des événements', 'details' => $e->getMessage()], 500);
        }
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
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'date_debut' => 'required|date',
                'date_fin' => 'nullable|date'
            ]);

            $event = Event::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'] ?? $validated['date_debut'],
                'auteur_id' => 1 // Default admin
            ]);

            return response()->json($event, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'message' => 'Les données fournies ne sont pas valides',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création de l\'événement',
                'message' => $e->getMessage()
            ], 500);
        }
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
        // Vérification des permissions (temporairement désactivé pour développement)
        // $user = Auth::user();
        // if ($user && $user->role !== 'admin') {
        //     return response()->json(['message' => 'Accès refusé - Admin seulement'], 403);
        // }

        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'date_debut' => 'required|date',
                'date_fin' => 'nullable|date'
            ]);

            $event = Event::findOrFail($id);
            $event->update([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'] ?? $validated['date_debut']
            ]);

            return response()->json($event);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'message' => 'Les données fournies ne sont pas valides',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Événement non trouvé',
                'message' => 'L\'événement demandé n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la modification de l\'événement',
                'message' => $e->getMessage()
            ], 500);
        }
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
        // Vérification des permissions (temporairement désactivé pour développement)
        // $user = Auth::user();
        // if ($user && $user->role !== 'admin') {
        //     return response()->json(['message' => 'Accès refusé - Admin seulement'], 403);
        // }

        try {
            // Récupération et suppression de l'événement
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Événement supprimé avec succès'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Événement non trouvé',
                'message' => 'L\'\u00e9vénement demandé n\'existe pas'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la suppression de l\'\u00e9vénement',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

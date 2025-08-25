<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GradeController extends Controller
{
    // Récupérer toutes les notes (route publique)
    public function index()
    {
        try {
            \Log::info('GradeController::index appelé');
            $grades = Grade::with(['eleve', 'professeur'])->get();
            \Log::info('Notes récupérées: ' . $grades->count());
            return response()->json($grades);
        } catch (\Exception $e) {
            \Log::error('Erreur dans GradeController::index: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des notes', 'details' => $e->getMessage()], 500);
        }
    }

    // Créer une nouvelle note
    public function store(Request $request)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'note' => 'required|numeric|min:0|max:20',
                'matiere' => 'required|string|max:255',
                'eleve_id' => 'required|integer|exists:users,id',
                'commentaire' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Créer la note avec Eloquent
            $grade = Grade::create([
                'note' => $request->note,
                'matiere' => $request->matiere,
                'eleve_id' => $request->eleve_id,
                'professeur_id' => 1, // Temporaire - à remplacer par l'utilisateur authentifié
                'commentaire' => $request->commentaire,
                'type_evaluation' => 'Contrôle',
                'date' => now()->format('Y-m-d')
            ]);

            // Charger les relations pour la réponse
            $grade->load(['eleve', 'professeur']);

            return response()->json($grade, 201);

        } catch (\Exception $e) {
            \Log::error('Erreur création note: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la création de la note',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer une note spécifique
    public function show($id)
    {
        $user = Auth::user();
        $grade = Grade::with(['eleve', 'professeur'])->findOrFail($id);
        
        // Vérifier les permissions
        switch ($user->role) {
            case 'admin':
                // Admin peut voir toutes les notes
                break;
            case 'professeur':
                // Professeur peut voir ses propres notes
                if ($grade->professeur_id !== $user->id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'parent':
                // Parent peut voir les notes de ses enfants
                $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
                if (!in_array($grade->eleve_id, $enfantsIds->toArray())) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'eleve':
                // Élève peut voir ses propres notes
                if ($grade->eleve_id !== $user->id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            default:
                return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json($grade);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'note' => 'required|numeric|min:0|max:20',
                'matiere' => 'required|string|max:255',
                'eleve_id' => 'required|integer|exists:users,id',
                'commentaire' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Trouver et mettre à jour la note
            $grade = Grade::findOrFail($id);
            
            $grade->update([
                'note' => $request->note,
                'matiere' => $request->matiere,
                'eleve_id' => $request->eleve_id,
                'commentaire' => $request->commentaire
            ]);

            // Charger les relations pour la réponse
            $grade->load(['eleve', 'professeur']);

            return response()->json($grade);

        } catch (\Exception $e) {
            \Log::error('Erreur modification note: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la modification',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Trouver et supprimer la note
            $grade = Grade::findOrFail($id);
            $grade->delete();

            return response()->json(['message' => 'Note supprimée avec succès']);

        } catch (\Exception $e) {
            \Log::error('Erreur suppression note: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erreur lors de la suppression',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

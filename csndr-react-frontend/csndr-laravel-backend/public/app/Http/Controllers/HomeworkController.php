<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    // Constructeur supprimé - routes publiques

    // Récupérer tous les devoirs (route publique)
    public function index()
    {
        try {
            $homework = Homework::all();
            return response()->json($homework);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des devoirs', 'details' => $e->getMessage()], 500);
        }
    }

    // Créer un nouveau devoir (admin et professeur seulement)
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'date_limite' => 'required|date',
                'classe_id' => 'required|exists:classes,id'
            ]);

            $homework = Homework::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'date_limite' => $validated['date_limite'],
                'classe_id' => $validated['classe_id'],
                'professeur_id' => Auth::id() ?? 2 // Utiliser l'utilisateur connecté par défaut
            ]);

            return response()->json($homework, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création du devoir'], 500);
        }
    }

    // Récupérer un devoir spécifique
    public function show($id)
    {
        $user = Auth::user();
        $homework = Homework::with(['classe', 'professeur'])->findOrFail($id);
        
        // Vérifier les permissions
        switch ($user->role) {
            case 'admin':
                // Admin peut voir tous les devoirs
                break;
            case 'professeur':
                // Professeur peut voir ses propres devoirs
                if ($homework->professeur_id !== $user->id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'parent':
                // Parent peut voir les devoirs de ses enfants
                $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
                $enfantsClasses = User::whereIn('id', $enfantsIds)->pluck('classe_id');
                if (!in_array($homework->classe_id, $enfantsClasses->toArray())) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'eleve':
                // Élève peut voir les devoirs de sa classe
                if ($homework->classe_id !== $user->classe_id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            default:
                return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json($homework);
    }

    // Mettre à jour un devoir (admin et professeur créateur seulement)
    public function update(Request $request, $id)
    {
        try {
            // Validation partielle pour permettre les mises à jour partielles
            $validated = $request->validate([
                'titre' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'date_limite' => 'sometimes|date',
                'classe_id' => 'sometimes|exists:classes,id'
            ]);

            $homework = Homework::findOrFail($id);
            $homework->update($validated);

            return response()->json($homework);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la modification'], 500);
        }
    }

    // Supprimer un devoir (admin et professeur créateur seulement)
    public function destroy($id)
    {
        try {
            $homework = Homework::findOrFail($id);
            $homework->delete();

            return response()->json(['message' => 'Devoir supprimé']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression'], 500);
        }
    }

    // Télécharger le fichier d'un devoir
    public function downloadFile($id)
    {
        $user = Auth::user();
        $homework = Homework::findOrFail($id);
        
        // Vérifier les permissions (même logique que show)
        switch ($user->role) {
            case 'admin':
                break;
            case 'professeur':
                if ($homework->professeur_id !== $user->id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'parent':
                $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
                $enfantsClasses = User::whereIn('id', $enfantsIds)->pluck('classe_id');
                if (!in_array($homework->classe_id, $enfantsClasses->toArray())) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            case 'eleve':
                if ($homework->classe_id !== $user->classe_id) {
                    return response()->json(['message' => 'Accès refusé'], 403);
                }
                break;
            default:
                return response()->json(['message' => 'Accès refusé'], 403);
        }

        if (!$homework->hasAttachment()) {
            return response()->json(['message' => 'Aucun fichier joint'], 404);
        }

        $filePath = storage_path('app/public/' . $homework->fichier_attachment);
        
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }

        return response()->download($filePath, $homework->nom_fichier_original);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Récupérer les notes selon le rôle de l'utilisateur
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                // Admin voit toutes les notes
                $grades = Grade::with(['eleve', 'professeur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
                
            case 'professeur':
                // Professeur voit ses propres notes
                $grades = Grade::where('professeur_id', $user->id)
                    ->with(['eleve', 'professeur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
                
            case 'parent':
                // Parent voit les notes de ses enfants
                $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
                $grades = Grade::whereIn('eleve_id', $enfantsIds)
                    ->with(['eleve', 'professeur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
                
            case 'eleve':
                // Élève voit ses propres notes
                $grades = Grade::where('eleve_id', $user->id)
                    ->with(['eleve', 'professeur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                break;
                
            default:
                return response()->json([], 403);
        }

        return response()->json($grades);
    }

    // Créer une nouvelle note (admin et professeur seulement)
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'note' => 'required|numeric|min:0|max:20',
            'matiere' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'eleve_id' => 'required|exists:users,id'
        ]);

        // Vérifier que l'élève existe et est bien un élève
        $eleve = User::findOrFail($request->eleve_id);
        if ($eleve->role !== 'eleve') {
            return response()->json(['message' => 'L\'utilisateur doit être un élève'], 422);
        }

        $grade = Grade::create([
            'note' => $request->note,
            'matiere' => $request->matiere,
            'commentaire' => $request->commentaire,
            'eleve_id' => $request->eleve_id,
            'professeur_id' => $user->id
        ]);

        $grade->load(['eleve', 'professeur']);

        return response()->json($grade, 201);
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

    // Mettre à jour une note (admin et professeur créateur seulement)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $grade = Grade::findOrFail($id);
        
        // Vérifier que le professeur est le créateur ou admin
        if ($user->role === 'professeur' && $grade->professeur_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'note' => 'required|numeric|min:0|max:20',
            'matiere' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'eleve_id' => 'required|exists:users,id'
        ]);

        $grade->update([
            'note' => $request->note,
            'matiere' => $request->matiere,
            'commentaire' => $request->commentaire,
            'eleve_id' => $request->eleve_id
        ]);

        $grade->load(['eleve', 'professeur']);

        return response()->json($grade);
    }

    // Supprimer une note (admin et professeur créateur seulement)
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $grade = Grade::findOrFail($id);
        
        // Vérifier que le professeur est le créateur ou admin
        if ($user->role === 'professeur' && $grade->professeur_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $grade->delete();

        return response()->json(['message' => 'Note supprimée avec succès']);
    }
}

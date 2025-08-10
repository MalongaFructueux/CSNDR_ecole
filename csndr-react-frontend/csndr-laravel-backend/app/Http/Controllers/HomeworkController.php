<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Récupérer les devoirs selon le rôle de l'utilisateur
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                // Admin voit tous les devoirs
                $homeworks = Homework::with(['classe', 'professeur'])
                    ->orderBy('date_limite', 'desc')
                    ->get();
                break;
                
            case 'professeur':
                // Professeur voit ses propres devoirs
                $homeworks = Homework::where('professeur_id', $user->id)
                    ->with(['classe', 'professeur'])
                    ->orderBy('date_limite', 'desc')
                    ->get();
                break;
                
            case 'parent':
                // Parent voit les devoirs de ses enfants
                $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
                $homeworks = Homework::whereIn('classe_id', function($query) use ($enfantsIds) {
                    $query->select('classe_id')
                          ->from('users')
                          ->whereIn('id', $enfantsIds);
                })
                ->with(['classe', 'professeur'])
                ->orderBy('date_limite', 'desc')
                ->get();
                break;
                
            case 'eleve':
                // Élève voit les devoirs de sa classe
                $homeworks = Homework::where('classe_id', $user->classe_id)
                    ->with(['classe', 'professeur'])
                    ->orderBy('date_limite', 'desc')
                    ->get();
                break;
                
            default:
                return response()->json([], 403);
        }

        return response()->json($homeworks);
    }

    // Créer un nouveau devoir (admin et professeur seulement)
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_limite' => 'required|date',
            'classe_id' => 'required|exists:classes,id'
        ]);

        $homework = Homework::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_limite' => $request->date_limite,
            'classe_id' => $request->classe_id,
            'professeur_id' => $user->id
        ]);

        $homework->load(['classe', 'professeur']);

        return response()->json($homework, 201);
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
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $homework = Homework::findOrFail($id);
        
        // Vérifier que le professeur est le créateur ou admin
        if ($user->role === 'professeur' && $homework->professeur_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'date_limite' => 'required|date',
            'classe_id' => 'required|exists:classes,id'
        ]);

        $homework->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'date_limite' => $request->date_limite,
            'classe_id' => $request->classe_id
        ]);

        $homework->load(['classe', 'professeur']);

        return response()->json($homework);
    }

    // Supprimer un devoir (admin et professeur créateur seulement)
    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'professeur'])) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $homework = Homework::findOrFail($id);
        
        // Vérifier que le professeur est le créateur ou admin
        if ($user->role === 'professeur' && $homework->professeur_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $homework->delete();

        return response()->json(['message' => 'Devoir supprimé avec succès']);
    }
}

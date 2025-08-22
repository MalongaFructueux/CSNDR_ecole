<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Contrôleur pour la gestion des classes
 * 
 * Ce contrôleur gère :
 * - La création, modification et suppression des classes
 * - La récupération des classes avec leurs élèves
 * - La validation des données de classe
 */
class ClassController extends Controller
{
    /**
     * Récupération de toutes les classes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $classes = Classe::select('id', 'nom', 'created_at', 'updated_at')->get();
        return response()->json($classes);
    }

    /**
     * Création d'une nouvelle classe
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:classes,nom'],
        ]);

        $classe = Classe::create([
            'nom' => $validated['nom']
        ]);

        return response()->json([
            'message' => 'Classe créée avec succès',
            'classe' => $classe
        ], 201);
    }

    /**
     * Récupération d'une classe spécifique
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $classe = Classe::findOrFail($id);
        return response()->json($classe);
    }

    /**
     * Modification d'une classe existante
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('classes', 'nom')->ignore($id)],
        ]);

        $classe->update([
            'nom' => $validated['nom']
        ]);

        return response()->json([
            'message' => 'Classe modifiée avec succès',
            'classe' => $classe
        ]);
    }

    /**
     * Suppression d'une classe
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $classe = Classe::findOrFail($id);
        
        // Vérifier s'il y a des élèves dans cette classe
        $elevesCount = \App\Models\User::where('classe_id', $id)->count();
        
        if ($elevesCount > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette classe car elle contient des élèves'
            ], 400);
        }

        $classe->delete();

        return response()->json([
            'message' => 'Classe supprimée avec succès'
        ]);
    }

    /**
     * Récupération des élèves d'une classe
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudents($id)
    {
        $classe = Classe::findOrFail($id);
        
        $eleves = \App\Models\User::where('classe_id', $id)
            ->where('role', 'eleve')
            ->select('id', 'nom', 'prenom', 'email', 'created_at')
            ->get();

        return response()->json([
            'classe' => $classe,
            'eleves' => $eleves
        ]);
    }
}
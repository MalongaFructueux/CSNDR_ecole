<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    // Note: pas de middleware global ici pour permettre l'accès public à index().
    // Les routes protégées sont déjà encapsulées dans le groupe Route::middleware('auth') dans routes/api.php.
    /**
     * Récupération de toutes les classes
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $classes = Classe::all();
            return response()->json($classes);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des classes', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Création d'une nouvelle classe
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Vérifier les permissions (temporairement désactivé pour développement)
            // if (auth()->user() && auth()->user()->role !== 'admin') {
            //     return response()->json(['error' => 'Accès non autorisé'], 403);
            // }

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100|unique:classes,nom',
                'niveau' => 'nullable|string|max:50',
                'annee_scolaire' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $classe = Classe::create($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Classe créée avec succès',
                'data' => $classe
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de la classe',
                'error' => $e->getMessage()
            ], 500);
        }
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
        try {
            // Vérifier les permissions (temporairement désactivé pour développement)
            // if (auth()->user() && auth()->user()->role !== 'admin') {
            //     return response()->json(['error' => 'Accès non autorisé'], 403);
            // }

            $classe = Classe::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('classes', 'nom')->ignore($id)
                ],
                'niveau' => 'nullable|string|max:50',
                'annee_scolaire' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $classe->update($validator->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Classe mise à jour avec succès',
                'data' => $classe
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour de la classe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'une classe
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Vérifier les permissions (temporairement désactivé pour développement)
            // if (auth()->user() && auth()->user()->role !== 'admin') {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Accès non autorisé'
            //     ], 403);
            // }

            $classe = Classe::findOrFail($id);
            
            // Vérifier s'il y a des élèves dans cette classe
            $elevesCount = User::where('classe_id', $id)->count();
            
            if ($elevesCount > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Impossible de supprimer cette classe car elle contient des élèves associés'
                ], 400);
            }

            $classe->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Classe supprimée avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression de la classe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupération des élèves d'une classe
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudents($id)
    {
        try {
            $user = auth()->user();
            $classe = Classe::findOrFail($id);
            
            // Vérifier les permissions
            if ($user->role === 'eleve' && $user->classe_id != $id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Accès non autorisé à cette classe'
                ], 403);
            }
            
            // Si c'est un parent, vérifier qu'il a des enfants dans cette classe
            if ($user->role === 'parent') {
                $hasAccess = User::where('parent_id', $user->id)
                    ->where('classe_id', $id)
                    ->exists();
                    
                if (!$hasAccess) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Accès non autorisé à cette classe'
                    ], 403);
                }
            }
            
            $eleves = User::where('classe_id', $id)
                ->where('role', 'eleve')
                ->select('id', 'nom', 'prenom', 'email', 'date_naissance', 'created_at')
                ->orderBy('nom')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'classe' => $classe,
                    'eleves' => $eleves
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération des élèves',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
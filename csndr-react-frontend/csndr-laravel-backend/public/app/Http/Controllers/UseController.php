<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Contrôleur pour l'inscription des utilisateurs
 * 
 * Ce contrôleur gère :
 * - L'inscription publique des nouveaux utilisateurs
 * - La validation des données d'inscription
 * - La création automatique de comptes avec rôles appropriés
 */
class UseController extends Controller
{
    /**
     * Inscription d'un nouvel utilisateur (route publique)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['parent', 'eleve'])], // Seuls parents et élèves peuvent s'inscrire
            'classe_id' => ['nullable', 'integer', 'exists:classes,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('role', 'parent');
                })
            ],
        ]);

        // Créer l'utilisateur
        $user = new User();
        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->classe_id = $validated['classe_id'] ?? null;

        // Logique spéciale pour les élèves
        if ($validated['role'] === 'eleve') {
            if (empty($validated['parent_id'])) {
                // Si aucun parent spécifié, assigner un parent aléatoire
                $parent = User::where('role', 'parent')->inRandomOrder()->first();
                if ($parent) {
                    $user->parent_id = $parent->id;
                }
            } else {
                $user->parent_id = $validated['parent_id'];
            }
        } else {
            $user->parent_id = null;
        }

        $user->save();

        // Créer un token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user->only(['id', 'nom', 'prenom', 'email', 'role', 'classe_id', 'parent_id', 'created_at', 'updated_at']),
            'token' => $token
        ], 201);
    }

    /**
     * Vérification de la disponibilité d'un email
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Cet email est déjà utilisé' : 'Email disponible'
        ]);
    }

    /**
     * Récupération des parents disponibles pour l'inscription d'élèves
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableParents()
    {
        $parents = User::where('role', 'parent')
            ->select('id', 'nom', 'prenom', 'email')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        return response()->json($parents);
    }

    /**
     * Récupération des classes disponibles pour l'inscription
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableClasses()
    {
        $classes = \App\Models\Classe::select('id', 'nom')
            ->orderBy('nom')
            ->get();

        return response()->json($classes);
    }
}
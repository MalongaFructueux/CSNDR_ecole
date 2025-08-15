<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Return users without exposing mot_de_passe hash
        return response()->json(
            User::select('id','nom','prenom','email','role','classe_id','parent_id','created_at','updated_at')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required','string','max:255'],
            'prenom' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'mot_de_passe' => ['required','string','min:6'],
            'role' => ['required', Rule::in(['admin','professeur','parent','eleve'])],
            'classe_id' => ['nullable','integer','exists:classes,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('role', 'parent');
                })
            ],
        ]);

        $user = new User();
        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        $user->mot_de_passe = Hash::make($validated['mot_de_passe']);
        $user->role = $validated['role'];
        $user->classe_id = $validated['classe_id'] ?? null;

        // Si l'utilisateur est un élève et qu'aucun parent_id n'est fourni,
        // on lui assigne un parent existant de manière aléatoire.
        if ($validated['role'] === 'eleve' && empty($validated['parent_id'])) {
            $parent = User::where('role', 'parent')->inRandomOrder()->first();
            if ($parent) {
                $user->parent_id = $parent->id;
            }
        } else {
            $user->parent_id = $validated['parent_id'] ?? null;
        }

        $user->save();

        return response()->json([
            'message' => 'Utilisateur créé',
            'user' => $user->only(['id','nom','prenom','email','role','classe_id','parent_id','created_at','updated_at'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => ['required','string','max:255'],
            'prenom' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($id)],
            'mot_de_passe' => ['nullable','string','min:6'],
            'role' => ['required', Rule::in(['admin','professeur','parent','eleve'])],
            'classe_id' => ['nullable','integer','exists:classes,id'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('role', 'parent');
                })
            ],
        ]);

        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        if (!empty($validated['mot_de_passe'])) {
            $user->mot_de_passe = Hash::make($validated['mot_de_passe']);
        }
        $user->role = $validated['role'];
        $user->classe_id = $validated['classe_id'] ?? null;
        $user->parent_id = $validated['parent_id'] ?? null;
        $user->save();

        return response()->json([
            'message' => 'Utilisateur modifié',
            'user' => $user->only(['id','nom','prenom','email','role','classe_id','parent_id','created_at','updated_at'])
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    // Nouvelle méthode pour récupérer les élèves d'un professeur
    public function getStudentsByTeacher($teacherId)
    {
        $teacher = User::findOrFail($teacherId);
        
        if ($teacher->role !== 'professeur') {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $students = User::where('role', 'eleve')
            ->where('classe_id', $teacher->classe_id)
            ->select('id','nom','prenom','email','classe_id','created_at')
            ->get();

        return response()->json($students);
    }

    // Nouvelle méthode pour récupérer les enfants d'un parent
    public function getChildrenByParent($parentId)
    {
        $parent = User::findOrFail($parentId);

        // Autoriser uniquement l'admin ou le parent lui-même
        $authUser = Auth::user();
        if (!($authUser && ($authUser->role === 'admin' || (int)$authUser->id === (int)$parentId))) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $children = User::where('parent_id', $parentId)
            ->select('id','nom','prenom','email','classe_id','created_at')
            ->get();

        return response()->json($children);
    }

    // Nouvelle méthode pour rechercher des utilisateurs
    public function searchUsers(Request $request)
    {
        $query = $request->get('query', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where(function($q) use ($query) {
            $q->where('nom', 'LIKE', "%{$query}%")
              ->orWhere('prenom', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })
        ->select('id','nom','prenom','email','role','classe_id','parent_id')
        ->limit(10)
        ->get();

        return response()->json($users);
    }
}

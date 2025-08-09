<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Return users without exposing mot_de_passe hash
        return response()->json(
            User::select('id','nom','prenom','email','role','classe_id','created_at','updated_at')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required','string','max:255'],
            'prenom' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','string','min:6'],
            'role' => ['required', Rule::in(['admin','professeur','parent','eleve'])],
            'classe_id' => ['nullable','integer'],
        ]);

        $user = new User();
        $user->nom = $validated['nom'];
        $user->prenom = $validated['prenom'];
        $user->email = $validated['email'];
        $user->mot_de_passe = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->classe_id = $validated['classe_id'] ?? null;
        $user->save();

        return response()->json([
            'message' => 'Utilisateur créé',
            'user' => $user->only(['id','nom','prenom','email','role','classe_id','created_at','updated_at'])
        ], 201);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}

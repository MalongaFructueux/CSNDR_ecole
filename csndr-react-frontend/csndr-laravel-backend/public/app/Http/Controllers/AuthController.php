<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Stocker l'utilisateur en session
            session(['user' => $user]);
            session(['authenticated' => true]);

            return response()->json([
                'message' => 'Connexion réussie',
                'user' => $user
            ]);
        }

        return response()->json([
            'message' => 'Identifiants invalides'
        ], 401);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:parent,eleve',
            'classe_id' => 'nullable|exists:classes,id',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        // Validation conditionnelle selon le rôle
        if ($request->role === 'eleve') {
            $request->validate([
                'parent_id' => 'required|exists:users,id',
                'classe_id' => 'required|exists:classes,id',
            ]);
        }

        $user = User::create([
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'classe_id' => $validatedData['classe_id'] ?? null,
            'parent_id' => $validatedData['parent_id'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inscription réussie',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'available' => !$exists
        ]);
    }

    public function getAvailableParents()
    {
        $parents = User::where('role', 'parent')
            ->select('id', 'nom', 'prenom', 'email')
            ->orderBy('nom')
            ->get();

        return response()->json($parents);
    }

    public function getAvailableClasses()
    {
        $classes = Classe::select('id', 'nom')
            ->orderBy('nom')
            ->get();

        return response()->json($classes);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->flush();
        session()->regenerate();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}
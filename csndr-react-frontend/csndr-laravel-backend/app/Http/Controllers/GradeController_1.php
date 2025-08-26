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
        try {
            $user = Auth::user();
            
            // Vérification des permissions
            if (!in_array($user->role, ['admin', 'professeur'])) {
                \Log::warning('Tentative de création de note non autorisée', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Accès refusé. Seuls les administrateurs et professeurs peuvent créer des notes.'
                ], 403);
            }

            // Validation des données
            $validated = $request->validate([
                'note' => 'required|numeric|min:0|max:20',
                'matiere' => 'required|string|max:255',
                'commentaire' => 'nullable|string|max:1000',
                'eleve_id' => 'required|integer|exists:users,id',
                'date' => 'nullable|date|before_or_equal:today',
                'coefficient' => 'nullable|numeric|min:0.1|max:10',
                'type_evaluation' => 'nullable|string|max:100'
            ], [
                'note.required' => 'La note est requise',
                'note.numeric' => 'La note doit être un nombre',
                'note.min' => 'La note ne peut pas être inférieure à 0',
                'note.max' => 'La note ne peut pas dépasser 20',
                'matiere.required' => 'La matière est requise',
                'matiere.max' => 'Le nom de la matière ne doit pas dépasser 255 caractères',
                'eleve_id.required' => 'L\'élève est requis',
                'eleve_id.exists' => 'L\'élève sélectionné n\'existe pas',
                'date.before_or_equal' => 'La date d\'évaluation ne peut pas être dans le futur',
                'coefficient.numeric' => 'Le coefficient doit être un nombre',
                'coefficient.min' => 'Le coefficient doit être d\'au moins 0.1',
                'coefficient.max' => 'Le coefficient ne peut pas dépasser 10',
                'type_evaluation.max' => 'Le type d\'évaluation ne doit pas dépasser 100 caractères'
            ]);

            // Vérifier que l'utilisateur cible est bien un élève
            $eleve = User::find($validated['eleve_id']);
            if (!$eleve || $eleve->role !== 'eleve') {
                \Log::warning('Tentative d\'attribution de note à un non-élève', [
                    'user_id' => $user->id,
                    'target_user_id' => $validated['eleve_id'],
                    'target_user_role' => $eleve ? $eleve->role : 'inexistant'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les élèves peuvent recevoir des notes.'
                ], 422);
            }

            // Vérifier que le professeur a le droit d'attribuer des notes à cet élève
            if ($user->role === 'professeur') {
                // Ici, vous pouvez ajouter une logique pour vérifier si le professeur enseigne à cet élève
                // Par exemple, en vérifiant s'ils partagent la même classe ou matière
                $professeurPeutNoter = true; // À adapter selon votre logique métier
                
                if (!$professeurPeutNoter) {
                    \Log::warning('Tentative d\'attribution de note non autorisée', [
                        'professeur_id' => $user->id,
                        'eleve_id' => $eleve->id,
                        'raison' => 'Le professeur n\'a pas les droits pour noter cet élève'
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous n\'êtes pas autorisé à attribuer des notes à cet élève.'
                    ], 403);
                }
            }

            // Création de la note dans une transaction
            $grade = \DB::transaction(function () use ($user, $validated, $eleve) {
                $gradeData = [
                    'note' => round($validated['note'], 2), // Arrondir à 2 décimales
                    'matiere' => $validated['matiere'],
                    'commentaire' => $validated['commentaire'] ?? null,
                    'eleve_id' => $validated['eleve_id'],
                    'professeur_id' => $user->id,
                    'date' => $validated['date'] ?? now()->toDateString(),
                    'coefficient' => $validated['coefficient'] ?? 1.0,
                    'type_evaluation' => $validated['type_evaluation'] ?? 'Devoir',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $grade = Grade::create($gradeData);
                $grade->load(['eleve', 'professeur']);
                
                // Journalisation de l'action
                \Log::info('Nouvelle note attribuée', [
                    'grade_id' => $grade->id,
                    'eleve_id' => $grade->eleve_id,
                    'professeur_id' => $grade->professeur_id,
                    'matiere' => $grade->matiere,
                    'note' => $grade->note
                ]);
                
                return $grade;
            });

            return response()->json([
                'success' => true,
                'message' => 'Note attribuée avec succès',
                'data' => $grade
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation lors de l\'attribution de la note', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'attribution de la note: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'attribution de la note.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
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

<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            // Parent voit les devoirs des classes de ses enfants
            $enfantsIds = User::where('parent_id', $user->id)->pluck('id');
            $classesIds = User::whereIn('id', $enfantsIds)->pluck('classe_id')->filter();
            $homeworks = Homework::whereIn('classe_id', $classesIds)
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
        try {
            $user = Auth::user();
            
            if (!in_array($user->role, ['admin', 'professeur'])) {
                \Log::warning('Tentative de création de devoir non autorisée', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Accès refusé. Seuls les administrateurs et professeurs peuvent créer des devoirs.'
                ], 403);
            }

            // Validation des données
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                // Autoriser la date du jour
                'date_limite' => 'required|date|after_or_equal:today',
                'classe_id' => 'required|exists:classes,id',
                'fichier' => 'nullable|file|mimes:pdf,doc,docx,txt,rtf,zip,rar|max:20480' // 20MB max
            ], [
                'titre.required' => 'Le titre est requis',
                'description.required' => 'La description est requise',
                'date_limite.required' => 'La date limite est requise',
                'date_limite.after_or_equal' => 'La date limite doit être aujourd\'hui ou plus tard',
                'classe_id.required' => 'La classe est requise',
                'classe_id.exists' => 'La classe sélectionnée n\'existe pas',
                'fichier.mimes' => 'Le fichier doit être de type: pdf, doc, docx, txt, rtf, zip, rar',
                'fichier.max' => 'Le fichier ne doit pas dépasser 20 Mo'
            ]);

            // Vérifier que la classe existe
            $classe = \App\Models\Classe::find($validated['classe_id']);
            if (!$classe) {
                return response()->json([
                    'success' => false,
                    'message' => 'La classe sélectionnée n\'existe pas.'
                ], 422);
            }

            // Démarrer une transaction pour assurer l'intégrité des données
            $homework = \DB::transaction(function () use ($user, $validated, $request) {
                $homeworkData = [
                    'titre' => $validated['titre'],
                    'description' => $validated['description'],
                    'date_limite' => $validated['date_limite'],
                    'classe_id' => $validated['classe_id'],
                    'professeur_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Gestion du fichier uploadé
                if ($request->hasFile('fichier')) {
                    try {
                        $file = $request->file('fichier');
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $fileName = 'devoir_' . time() . '_' . uniqid() . '.' . $extension;
                        
                        // Créer le dossier s'il n'existe pas
                        $directory = 'devoirs/' . date('Y/m');
                        $filePath = $file->storeAs($directory, $fileName, 'public');
                        
                        if (!$filePath) {
                            throw new \Exception('Échec du stockage du fichier');
                        }
                        
                        $homeworkData['fichier_attachment'] = $filePath;
                        $homeworkData['nom_fichier_original'] = $originalName;
                        $homeworkData['type_fichier'] = $file->getMimeType();
                        $homeworkData['taille_fichier'] = $file->getSize();
                        
                    } catch (\Exception $e) {
                        \Log::error('Erreur lors de l\'upload du fichier: ' . $e->getMessage());
                        throw new \Exception('Erreur lors du téléchargement du fichier: ' . $e->getMessage());
                    }
                }

                // Création du devoir
                $homework = Homework::create($homeworkData);
                $homework->load(['classe', 'professeur']);
                
                // Journalisation
                \Log::info('Nouveau devoir créé', [
                    'homework_id' => $homework->id,
                    'professeur_id' => $user->id,
                    'classe_id' => $validated['classe_id'],
                    'has_file' => $request->hasFile('fichier')
                ]);
                
                return $homework;
            });

            return response()->json([
                'success' => true,
                'message' => 'Devoir créé avec succès' . ($request->hasFile('fichier') ? ' avec fichier joint' : ''),
                'data' => $homework
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation lors de la création du devoir', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du devoir: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du devoir.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }

        $homework = Homework::create($homeworkData);
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
            'classe_id' => 'required|exists:classes,id',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,txt,rtf|max:10240'
        ]);

        $updateData = [
            'titre' => $request->titre,
            'description' => $request->description,
            'date_limite' => $request->date_limite,
            'classe_id' => $request->classe_id
        ];

        // Gestion du fichier uploadé
        if ($request->hasFile('fichier')) {
            // Supprimer l'ancien fichier s'il existe
            if ($homework->fichier_attachment) {
                Storage::disk('public')->delete($homework->fichier_attachment);
            }

            $file = $request->file('fichier');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('devoirs', $fileName, 'public');
            
            $updateData['fichier_attachment'] = $filePath;
            $updateData['nom_fichier_original'] = $file->getClientOriginalName();
            $updateData['type_fichier'] = $file->getMimeType();
        }

        $homework->update($updateData);
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

        // Supprimer le fichier joint s'il existe
        if ($homework->fichier_attachment) {
            Storage::disk('public')->delete($homework->fichier_attachment);
        }

        $homework->delete();
        return response()->json(['message' => 'Devoir supprimé avec succès']);
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

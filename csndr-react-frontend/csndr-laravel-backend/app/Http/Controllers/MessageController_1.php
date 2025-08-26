<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Contrôleur MessageController - Gestion des messages et conversations
 * 
 * Ce contrôleur gère :
 * - L'envoi et la réception de messages
 * - La gestion des conversations
 * - Les restrictions d'accès selon les rôles
 * - L'API pour la messagerie
 * 
 * Fonctionnalités principales :
 * - Récupération des conversations de l'utilisateur
 * - Envoi de nouveaux messages
 * - Gestion des utilisateurs disponibles pour la discussion
 * - Validation et sécurité des messages
 * 
 * Restrictions par rôle :
 * - Admin : Peut discuter avec tout le monde
 * - Professeur : Peut discuter avec admin, autres professeurs, parents et élèves de sa classe
 * - Parent : Peut discuter avec admin, professeurs et ses enfants
 * - Élève : Accès limité aux messages
 */
class MessageController extends Controller
{
    /**
     * Constructeur - Applique le middleware d'authentification
     * Toutes les routes nécessitent une authentification
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Récupère tous les messages de l'utilisateur connecté
     * Retourne les messages envoyés et reçus triés par date
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupération des messages (envoyés ou reçus) avec les relations
        $messages = Message::where('expediteur_id', $user->id)
            ->orWhere('destinataire_id', $user->id)
            ->with(['expediteur', 'destinataire'])
            ->orderBy('date_envoi', 'desc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Récupère les conversations groupées de l'utilisateur connecté
     * Organise les messages par conversation avec le dernier message
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function conversations()
    {
        $user = Auth::user();
        
        // Récupération de tous les messages de l'utilisateur
        $messages = Message::where('expediteur_id', $user->id)
            ->orWhere('destinataire_id', $user->id)
            ->with(['expediteur', 'destinataire'])
            ->orderBy('date_envoi', 'desc')
            ->get();

        // Groupement des messages par conversation
        $conversations = [];
        foreach ($messages as $message) {
            // Détermination de l'autre utilisateur dans la conversation
            $otherUserId = $message->expediteur_id === $user->id ? $message->destinataire_id : $message->expediteur_id;
            if (!isset($conversations[$otherUserId])) {
                $conversations[$otherUserId] = [];
            }
            $conversations[$otherUserId][] = $message;
        }

        // Formatage des conversations avec les informations utilisateur
        $formattedConversations = [];
        foreach ($conversations as $otherUserId => $conversationMessages) {
            $otherUser = User::find($otherUserId);
            if ($otherUser) {
                // Compter les messages non lus dans cette conversation
                $unreadCount = collect($conversationMessages)
                    ->where('destinataire_id', $user->id)
                    ->where('lu', false)
                    ->count();

                $formattedConversations[] = [
                    'user' => $otherUser,
                    'messages' => $conversationMessages,
                    'last_message' => $conversationMessages[0], // Premier message (le plus récent)
                    'unread_count' => $unreadCount
                ];
            }
        }

        return response()->json($formattedConversations);
    }

    /**
     * Envoie un nouveau message
     * Valide les données et vérifie les permissions selon le rôle
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validation des données reçues
            $validated = $request->validate([
                'destinataire_id' => 'required|integer|exists:users,id',
                'contenu' => 'required|string|max:1000'
            ], [
                'destinataire_id.required' => 'L\'ID du destinataire est requis',
                'destinataire_id.exists' => 'Le destinataire sélectionné n\'existe pas',
                'contenu.required' => 'Le contenu du message est requis',
                'contenu.max' => 'Le message ne doit pas dépasser 1000 caractères'
            ]);

            $user = Auth::user();
            
            // Vérification des permissions selon le rôle
            if (!in_array($user->role, ['admin', 'professeur', 'parent'])) {
                \Log::warning('Tentative d\'envoi de message non autorisée', [
                    'user_id' => $user->id,
                    'role' => $user->role
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Accès refusé. Seuls les administrateurs, professeurs et parents peuvent envoyer des messages.'
                ], 403);
            }

            // Vérifier que l'utilisateur ne s'envoie pas un message à lui-même
            if ($user->id == $validated['destinataire_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas vous envoyer un message à vous-même.'
                ], 422);
            }

            // Création du nouveau message avec une transaction pour assurer l'intégrité des données
            $message = \DB::transaction(function () use ($user, $validated) {
                $message = Message::create([
                    'expediteur_id' => $user->id,
                    'destinataire_id' => $validated['destinataire_id'],
                    'contenu' => $validated['contenu'],
                    'date_envoi' => now(),
                ]);

                // Chargement des relations pour la réponse
                $message->load(['expediteur', 'destinataire']);
                
                // Journalisation de l'action
                \Log::info('Nouveau message envoyé', [
                    'message_id' => $message->id,
                    'expediteur_id' => $user->id,
                    'destinataire_id' => $validated['destinataire_id']
                ]);
                
                return $message;
            });

            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès',
                'data' => $message
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation lors de l\'envoi du message', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi du message: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi du message.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Récupère les messages d'une conversation spécifique
     * Retourne tous les messages entre l'utilisateur connecté et un autre utilisateur
     * 
     * @param int $conversationId - ID de l'utilisateur avec qui on converse
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($conversationId)
    {
        $user = Auth::user();
        
        // Récupération des messages de la conversation spécifique
        $messages = Message::where(function($query) use ($user, $conversationId) {
            $query->where('expediteur_id', $user->id)
                  ->where('destinataire_id', $conversationId);
        })->orWhere(function($query) use ($user, $conversationId) {
            $query->where('expediteur_id', $conversationId)
                  ->where('destinataire_id', $user->id);
        })
        ->with(['expediteur', 'destinataire'])
        ->orderBy('date_envoi', 'asc')
        ->get();

        return response()->json($messages);
    }

    /**
     * Récupère la liste des utilisateurs avec qui on peut discuter
     * Filtre les utilisateurs selon le rôle de l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
        public function getAvailableUsers()
    {
        $user = Auth::user();
        
        $query = User::query();
        
        // Filtrage selon le rôle de l'utilisateur connecté
        switch ($user->role) {
            case 'admin':
                // Admin peut discuter avec tout le monde
                break;
                
            case 'professeur':
                // Professeur peut discuter avec admin, autres professeurs, parents et élèves de sa classe
                $query->whereIn('role', ['admin', 'professeur', 'parent', 'eleve']);
                break;
                
            case 'parent':
                // Parent peut discuter avec admin, professeurs et ses enfants
                $query->where(function($q) {
                    $q->whereIn('role', ['admin', 'professeur'])
                      ->orWhere('parent_id', Auth::id());
                });
                break;
                
            default:
                // Élèves et autres rôles ont un accès limité
                return response()->json([], 403);
        }

        // Exclusion de l'utilisateur connecté de la liste
        $users = $query->where('id', '!=', $user->id)->get();

        return response()->json($users);
    }

    /**
     * Marque les messages d'une conversation comme lus
     * 
     * @param int $conversationId - ID de l'autre utilisateur dans la conversation
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($conversationId)
    {
        $user = Auth::user();

        Message::where('expediteur_id', $conversationId)
            ->where('destinataire_id', $user->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return response()->json(['success' => true, 'message' => 'Messages marqués comme lus.']);
    }
}

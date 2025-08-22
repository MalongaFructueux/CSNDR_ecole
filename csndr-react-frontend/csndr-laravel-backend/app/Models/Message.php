<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Message - Gestion des messages entre utilisateurs
 * 
 * Ce modèle gère :
 * - Les messages envoyés entre utilisateurs du système
 * - Les conversations et discussions
 * - Les relations avec les expéditeurs et destinataires
 * 
 * Fonctionnalités principales :
 * - Envoi de messages entre utilisateurs
 * - Gestion des conversations
 * - Historique des messages
 * - Restrictions selon les rôles
 * 
 * Relations :
 * - expediteur() : Relation avec l'utilisateur expéditeur
 * - destinataire() : Relation avec l'utilisateur destinataire
 */
class Message extends Model
{
    /**
     * Table associée au modèle
     * 
     * @var string
     */
    protected $table = 'messages';
    
    /**
     * Attributs remplissables en masse
     * 
     * @var array
     */
    protected $fillable = [
        'expediteur_id',     // ID de l'utilisateur expéditeur
        'destinataire_id',   // ID de l'utilisateur destinataire
        'contenu',           // Contenu du message
        'lu',                // Statut de lecture du message
        'date_envoi'         // Date et heure d'envoi
    ];

    /**
     * Attributs à traiter comme des dates
     * 
     * @var array
     */
    protected $dates = ['date_envoi'];

    /**
     * Relation avec l'utilisateur expéditeur
     * Un message appartient à un expéditeur
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    /**
     * Relation avec l'utilisateur destinataire
     * Un message appartient à un destinataire
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }
}

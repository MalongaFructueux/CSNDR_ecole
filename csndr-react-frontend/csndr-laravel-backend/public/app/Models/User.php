<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle User - Gestion des utilisateurs du système
 * 
 * Ce modèle gère :
 * - L'authentification des utilisateurs (Authenticatable)
 * - Les notifications (Notifiable)
 * - Les relations avec les autres modèles
 * 
 * Rôles disponibles :
 * - admin : Administrateur avec accès complet
 * - professeur : Professeur avec gestion pédagogique
 * - parent : Parent avec accès aux données de ses enfants
 * - eleve : Élève avec accès limité
 * 
 * Relations principales :
 * - classe() : Relation avec la classe de l'élève
 * - enfants() : Relation avec les enfants (pour les parents)
 * - devoirs() : Relation avec les devoirs créés (pour les professeurs)
 * - notes() : Relation avec les notes (pour les élèves)
 * - evenements() : Relation avec les événements créés
 * - messagesEnvoyes() : Messages envoyés par l'utilisateur
 * - messagesRecus() : Messages reçus par l'utilisateur
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Attributs remplissables en masse
     * 
     * @var array
     */
    protected $fillable = [
        'nom',           // Nom de famille
        'prenom',        // Prénom
        'email',         // Email unique
        'password',      // Mot de passe (hashé)
        'role',          // Rôle utilisateur (admin, professeur, parent, eleve)
        'classe_id',     // ID de la classe (pour les élèves)
        'parent_id'      // ID du parent (pour les élèves)
    ];

    /**
     * Attributs cachés lors de la sérialisation
     * 
     * @var array
     */
    protected $hidden = [
        'password',          // Mot de passe jamais exposé
        'remember_token',    // Token de "se souvenir de moi"
        'enfants',           // Cacher la relation enfants par défaut
        'parent'             // Cacher la relation parent par défaut
    ];

    /**
     * Retourne le nom de l'attribut de mot de passe pour l'authentification
     * 
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Relation avec la classe de l'élève
     * Un élève appartient à une classe
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    /**
     * Relation avec les enfants (pour les parents)
     * Un parent peut avoir plusieurs enfants
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enfants()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * Relation avec le parent (pour les élèves)
     * Un élève a un parent
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Relation avec les devoirs créés par le professeur
     * Un professeur peut créer plusieurs devoirs
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devoirs()
    {
        return $this->hasMany(Homework::class, 'professeur_id');
    }

    /**
     * Relation avec les notes de l'élève
     * Un élève peut avoir plusieurs notes
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Grade::class, 'eleve_id');
    }

    /**
     * Relation avec les événements créés par l'utilisateur
     * Un utilisateur peut créer plusieurs événements
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evenements()
    {
        return $this->hasMany(Event::class, 'auteur_id');
    }

    /**
     * Relation avec les messages envoyés par l'utilisateur
     * Un utilisateur peut envoyer plusieurs messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    /**
     * Relation avec les messages reçus par l'utilisateur
     * Un utilisateur peut recevoir plusieurs messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }
}
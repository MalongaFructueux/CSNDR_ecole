<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['nom', 'prenom', 'email', 'mot_de_passe', 'role', 'classe_id', 'parent_id'];
    protected $hidden = ['mot_de_passe', 'remember_token'];

    /**
     * Return the name of the unique identifier for the user.
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function enfants()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function devoirs()
    {
        return $this->hasMany(Homework::class, 'professeur_id');
    }

    public function notes()
    {
        return $this->hasMany(Grade::class, 'eleve_id');
    }

    public function evenements()
    {
        return $this->hasMany(Event::class, 'auteur_id');
    }

    public function messagesEnvoyes()
    {
        return $this->hasMany(Message::class, 'expediteur_id');
    }

    public function messagesRecus()
    {
        return $this->hasMany(Message::class, 'destinataire_id');
    }
}
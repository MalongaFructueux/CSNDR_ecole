<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'devoirs';
    
    protected $fillable = [
        'titre',
        'description',
        'date_limite',
        'classe_id',
        'professeur_id',
        'fichier_attachment',
        'nom_fichier_original',
        'type_fichier'
    ];

    protected $dates = ['date_limite'];

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function professeur()
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }

    /**
     * Vérifie si le devoir a un fichier joint
     */
    public function hasAttachment()
    {
        return !empty($this->fichier_attachment);
    }

    /**
     * Retourne l'URL de téléchargement du fichier
     */
    public function getDownloadUrl()
    {
        if ($this->hasAttachment()) {
            return url('storage/' . $this->fichier_attachment);
        }
        return null;
    }

    /**
     * Retourne l'extension du fichier
     */
    public function getFileExtension()
    {
        if ($this->hasAttachment()) {
            return pathinfo($this->nom_fichier_original, PATHINFO_EXTENSION);
        }
        return null;
    }
}

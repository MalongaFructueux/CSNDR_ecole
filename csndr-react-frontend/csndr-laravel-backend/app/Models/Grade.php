<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'notes';
    
    protected $fillable = [
        'note',
        'matiere',
        'commentaire',
        'eleve_id',
        'professeur_id',
        'coefficient',
        'date',
        'type_evaluation'
    ];

    public function eleve()
    {
        return $this->belongsTo(User::class, 'eleve_id');
    }

    public function professeur()
    {
        return $this->belongsTo(User::class, 'professeur_id');
    }
}

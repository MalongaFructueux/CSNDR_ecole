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
        'professeur_id'
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
}

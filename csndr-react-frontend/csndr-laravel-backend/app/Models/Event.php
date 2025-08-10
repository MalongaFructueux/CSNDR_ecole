<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'evenements';
    
    protected $fillable = [
        'titre',
        'description',
        'date',
        'auteur_id'
    ];

    protected $dates = ['date'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}

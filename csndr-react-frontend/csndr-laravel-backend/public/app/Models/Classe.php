<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'nom',
    ];

    public function eleves()
    {
        return $this->hasMany(User::class, 'classe_id')->where('role', 'eleve');
    }
}

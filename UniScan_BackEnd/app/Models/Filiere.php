<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse.
     */
    protected $fillable = [
        'nom_filiere',
        'description',
    ];

    // --- RELATION ---

    // Relation: Une filière peut être choisie dans plusieurs candidatures.
     
    public function applications()
    {
        return $this->hasMany(Application::class, 'filiere_id');
    }
}
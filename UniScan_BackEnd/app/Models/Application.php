<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    /**
     * Attributs assignables en masse.
     * (Tous les champs '..._saisi' que l'étudiant remplit)
     */
    protected $fillable = [
        'user_id',
        'filiere_id',
        'status',
        'nom_saisi',
        'prenom_saisi',
        'cin_saisi',
        'cne_saisi',
        'note_bac_saisie',
        'annee_bac_saisie',
        // 'motif_rejet' et 'admin_id' seront remplis par l'admin,
        // mais on peut les mettre ici pour les mises à jour.
        'motif_rejet',
        'admin_id',
        'processed_at',
    ];

    /**
     * Casts pour les types de données spécifiques.
     * Très utile pour les ENUM et les dates.
     */
    protected $casts = [
        'status' => 'string',
        'note_bac_saisie' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // --- RELATIONS ---

    /**
     * Relation: Une candidature "appartient à"  un étudiant.
     */
    public function etudiant()
    {
        // Le nom de la fonction est 'etudiant' pour la clarté
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation: Une candidature "appartient à"  une filière.
     */
    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    /**
     * Relation: Une candidature "appartient à"  un admin (celui qui l'a traitée).
     * C'est nullable, donc c'est normal si c'est vide.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relation: Une candidature "a plusieurs" (hasMany) documents.
     * (User Story: "Les images des documents (CIN, Bac,...) téléversés")
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'application_id');
    }
}
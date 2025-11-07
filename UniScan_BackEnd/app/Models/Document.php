<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'type_document',
        'nom_original',
        'chemin_fichier',
        'ocr_status',
        'texte_ocr_brut',
        'data_extraite_json',
    ];

    /**
     * Casts pour les ENUMs et surtout pour le JSON.
     * Le JSON sera automatiquement converti en tableau PHP (array)
     * quand tu y accéderas, c'est très pratique !
     * (User Story: "Les informations extraites par l'OCR")
     */
    protected $casts = [
        'type_document' => 'string',
        'ocr_status' => 'string',
        'data_extraite_json' => 'array', // Magique !
    ];

    // --- RELATION ---

    /**
     * Relation: Un document "appartient à"  une seule candidature.
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;    
use Illuminate\Validation\Rule; // Importer pour la validation

class ApplicationController extends Controller
{
    // Fonction pour soumettre une candidature/dossier
    public function store(Request $request)
    {
        
        $user = $request->user();

        // 2. Validation des données du formulaire
        // verifier l unicité de user_id/filiere_id)
        $validatedData = $request->validate([
            'filiere_id' => [
                'required',
                'integer',
                'exists:filieres,id', 
                
                // L'étudiant ne peut postuler qu une foit a cette filiere_id
                Rule::unique('applications')->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id);
                })
            ],
           
            'nom_saisi' => 'required|string|max:255',
            'prenom_saisi' => 'required|string|max:255',
            'cin_saisi' => 'required|string|max:50',
            'cne_saisi' => 'required|string|max:50',
            'note_bac_saisie' => 'required|numeric|min:0|max:20',
            'annee_bac_saisie' => 'required|integer|digits:4',
            
        ]);

        // creation de la candidature
        $applicationData = array_merge($validatedData, [
            'user_id' => $user->id,
            'status' => 'en_attente', 
        ]);

        $application = Application::create($applicationData);

        
        return response()->json([
            'message' => 'Candidature soumise avec succès.',
            'application' => $application
        ], 201); // 201 = "Created"
    }
}

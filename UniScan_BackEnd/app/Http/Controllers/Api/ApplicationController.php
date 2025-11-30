<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;    
use Illuminate\Validation\Rule; // Importer pour la validation
use App\Models\Document;                  
use Illuminate\Support\Facades\Storage; // pour geree le stockage des fichiers

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

    /**
     * User Story: "En tant qu'étudiant, je veux uploader ma CIN/Bac"
     * Gère l'upload d'un fichier (CIN ou Bac) et le lie à une candidature.
     *
     * @param Request 
     * @param Application 
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocument(Request $request, Application $application)
    {
        // verifie que l user ne modifie que sa propre candidature
        if ($request->user()->id !== $application->user_id) {
            return response()->json(['message' => 'Non autorisé'], 403); // 403 = Forbidden
        }
        // Validation du fichier uploade
        $validatedData = $request->validate([
            'type_document' => [
                'required',
                'string',
                Rule::in(['cin_recto', 'cin_verso', 'bac_releve'])
            ],
            'document_file' => 'required|file|image|mimes:jpeg,png,jpg|max:4048', // 2MB max
        ]);

        // 3. Stockage du fichier avec nom unique et dans 'storage/app/'
        $path = $request->file('document_file')->store(
            'uploads/' . $application->user_id . '/' . $application->id,// 'uploads/[user_id]/[application_id]'
            'local' 
        );
        //Creation de l enregistrement Document lie a la candidature
        $document = Document::create([
            'application_id' => $application->id,
            'type_document' => $validatedData['type_document'],
            'nom_original' => $request->file('document_file')->getClientOriginalName(),
            'chemin_fichier' => $path, 
            'ocr_status' => 'pending', 
        ]);

        \App\Jobs\ProcessOcrDocument::dispatch($document);

        return response()->json([
            'message' => 'Document téléversé avec succès.',
            'document' => $document
        ], 201); // 201 = Created
    }

    
    
     
    //recupere candidatur pour user connecter , incluant son statut et les documents associés.
    public function show(Request $request)
    {
        $user = $request->user();
        // Recuperer la candidature de l utilisateur avec les documents lies
        $application = Application::where('user_id', $user->id)
                                  ->with('documents') // <-- Charge les documents liés
                                  ->first();
        if (!$application) {
            return response()->json([
                'message' => 'Vous n\'avez pas encore de candidature soumise.'
            ], 404); // 404 = Not Found
        }
        return response()->json($application, 200); // 200 = OK
    }
     

}

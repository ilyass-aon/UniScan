<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\Filiere;

class AdminController extends Controller
{
    // Liste des candidatures
        public function index(Request $request)
    {
        
        $query = Application::with(['user', 'filiere']);

        // Filtre par Filière (si choisi)
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        // Filtre par Statut (si choisi)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // le tri
        $applications = $query->orderBy('created_at', 'desc')->get();

        //  On récupère la liste des filières pour le menu déroulant
        $filieres = Filiere::all();

        // On envoie tout à la vue
        return view('admin.dashboard', compact('applications', 'filieres'));
    }

    // Détail d'une candidature (Comparaison OCR)
    public function show($id)
    {
        $application = Application::with(['user', 'documents'])->findOrFail($id);
        
        // On décode le JSON de l'OCR pour l'afficher proprement
        // On suppose qu'il n'y a qu'un seul document principal pour l'instant
        $doc = $application->documents->first();
        $ocrData = $doc ? $doc->data_extraite_json : null;
        return view('admin.show', compact('application', 'doc', 'ocrData'));
    }

    // Valider
    public function validateApplication($id)
    {
        $app = Application::findOrFail($id);
        
        // Mise à jour des champs
        $app->status = 'valide';      
        $app->processed_at = now();   
       
        $app->motif_rejet = null; 

        $app->save();

        return redirect()->route('admin.dashboard')->with('success', 'Dossier validé avec succès !');
    }

    // Rejeter
    public function rejectApplication(Request $request, $id)
    {
        // 1. Validation du champ motif
        $request->validate([
            'motif_rejet' => 'required|string|max:1000',
        ]);

        $app = Application::findOrFail($id);
        
        // 2. Mise à jour des champs
        $app->status = 'rejeté';
        $app->motif_rejet = $request->motif_rejet; // On sauvegarde le motif
        $app->processed_at = now(); // On sauvegarde la date/heure actuelle (processed_at)
         

        $app->save();

        return redirect()->route('admin.dashboard')->with('error', 'Dossier rejeté. Motif enregistré.');
    }

}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    // Afficher toutes les candidatures
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'sometimes|string|in:en_attente,valide,rejete'
        ]);
        $query = Application::query();
        $query->with('etudiant', 'filiere');
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $query->orderBy('created_at', 'desc');
        $applications = $query->paginate(15);

        return response()->json($applications, 200);
    }

    // Voir les details d une candidature
    public function show(Application $application)
    {
 
        $application->load('etudiant', 'filiere', 'documents');

        return response()->json($application, 200);
    }

    public function valider(Application $application)
    {
        // verifier que le dossier est bien 'en_attente'
        if ($application->status !== 'en_attente') {
            return response()->json([
                'message' => 'Ce dossier a déjà été traité.'
            ], 422); // 422 
        }

        // Status passe a 'valide'
        $application->status = 'valide';
        $application->admin_id = Auth::id(); 
        $application->processed_at = now(); 
        $application->motif_rejet = null; 
        $application->save();

        return response()->json([
            'message' => 'Candidature validée avec succès.',
            'application' => $application
        ], 200);
    }

    public function rejeter(Request $request, Application $application)
    {
        // 1. verifier que le dossier est bien 'en_attente'
        if ($application->status !== 'en_attente') {
            return response()->json([
                'message' => 'Ce dossier a déjà été traité.'
            ], 422);
        }

        $validatedData = $request->validate([
            'motif_rejet' => 'required|string|min:10|max:1000' // Motif obligatoire 
        ]);

        // Status passe a 'rejete'
        $application->status = 'rejete';
        $application->admin_id = Auth::id();
        $application->processed_at = now();
        $application->motif_rejet = $validatedData['motif_rejet']; 
        $application->save();

        return response()->json([
            'message' => 'Candidature rejetée avec succès.',
            'application' => $application
        ], 200);
    }

}

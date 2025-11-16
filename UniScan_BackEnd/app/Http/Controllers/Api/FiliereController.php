<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Filiere;

class FiliereController extends Controller
{
    public function index()
    {
        return response()->json(Filiere::orderBy('nom_filiere', 'asc')->get(), 200);
    }

    public function store(Request $request)
    {

        // 'nom_filiere' doit etre unique
        $validatedData = $request->validate([
            'nom_filiere' => 'required|string|max:255|unique:filieres',
            'description' => 'nullable|string', ]);
        $filiere = Filiere::create($validatedData);
        return response()->json([
            'message' => 'Filière créée avec succès.',
            'filiere' => $filiere
        ], 201); // 201 = "Created"
    }
     public function destroy(Filiere $filiere)
    {
        if ($filiere->applications()->exists()) {
            return response()->json([
                'message' => 'Cette filière ne peut pas être supprimée. Elle est déjà utilisée par des candidatures.'
            ], 422); // 422 = Unprocessable Entity (logique métier)
        }

        $filiere->delete();
        return response()->json([
            'message' => 'Filière supprimée avec succès.'
        ], 200);// 200 = "OK"
    }


}

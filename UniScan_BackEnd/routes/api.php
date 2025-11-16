<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\FiliereController;

/*
|----------------------
| API Routes
|----------------------
*/


// --- Routes d Authentification (Publiques) ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- Routes Protegees (besoin d un token) ---

Route::middleware('auth:sanctum')->group(function () {
    
    // Route de deconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route simple pour tester si le token marche
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // route pour soumettre dossier de candidature
    Route::post('/application', [ApplicationController::class, 'store']);
    
    // route pour uploader un document lie a une candidature
    Route::post('/application/{application}/document', [ApplicationController::class, 'uploadDocument']);
    
    // route pour afficher les status des candidatures de l utilisateur connecte
    Route::get('/my-application', [ApplicationController::class, 'show']);
});

// --- Routes Admin ---

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    
    // Voir le tableau de bord et filtrer
    Route::get('/applications', [AdminController::class, 'index']);

    // --- Gestion des Filieres ---
    
    // Lister toutes les filieres
    Route::get('/filieres', [FiliereController::class, 'index']);
    // Admin: Ajouter une filiere
    Route::post('/filieres', [FiliereController::class, 'store']);
    // Admin: Supprimer une filiere
    Route::delete('/filieres/{filiere}', [FiliereController::class, 'destroy']);

    Route::get('/applications/{application}', [AdminController::class, 'show']);

    // Valider une candidature
    Route::post('/applications/{application}/valider', [AdminController::class, 'valider']);
    // Rejeter une candidature
    Route::post('/applications/{application}/rejeter', [AdminController::class, 'rejeter']);

});



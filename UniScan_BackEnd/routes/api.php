<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController; 

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

    // TODO: On ajoutera ici les autres routes (ex: soumettre le formulaire, voir le statut...)

});



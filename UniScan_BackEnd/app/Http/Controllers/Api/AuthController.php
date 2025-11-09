<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;   // pour hacher mot de passe 
use Illuminate\Support\Facades\Auth;   // pour la connection

class AuthController extends Controller
{
    /**
     * ----------------------------------------
     * Fonction d'Inscription (Register)
     * (User Story: "En tant qu'étudiant, je veux créer un compte...")
     * ----------------------------------------
     */

    public function register(Request $request){

        // validation des donnes recus par l app
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // email doit être unique dans la table 'users'
            'password' => 'required|string|min:8|confirmed', // confirmed vérifie que 'password_confirmation' correspond
        ]);

        // creation de user avec role etudiant
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Le cast 'hashed' dans le modèle User.php s'en occupe
            'role' => 'etudiant', // <-- Important
        ]);

        // creation du token Sanctum (c est le jeton que l app vas sauvgarder) 
        $token = $user->createToken('auth_token_etudiant')->plainTextToken;

        // reponse JSON avec token et info user
        return response()->json([
            'message' => 'Compte créé avec succès',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201); // 201 = "Created"
    }

    /**
     * ----------------------------------------
     * Fonction de Connexion (Login)
     * (User Story: "En tant qu'utilisateur, je veux me connecter...")
     * ----------------------------------------
     */

    public function login(Request $request){
        // validation des donnes recus par l app
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // cherche l'utilisateur
        $user = User::where('email', $request->email)->first();

        // verifie si user existe et mot de passe est correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Si l'un des deux est faux, on renvoie une erreur 401
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401); // 401 = "Unauthorized"
        }

        // supprime les anciens tokens et cree un nouveau token
        $user->tokens()->delete();
        $token = $user->createToken('auth_token_etudiant')->plainTextToken;

        // reponse JSON avec token et info user
        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200); // 200 = "OK"
    }

    /**
     * ----------------------------------------
     * Fonction de Déconnexion (Logout)
     * (User Story: "En tant qu'utilisateur connecté, je veux me déconnecter")
     * ----------------------------------------
     */

    public function logout(Request $request){
        // Supprime le token de l'utilisateur actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnecté avec succès'
        ]);
    }
}

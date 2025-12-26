<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Affiche le formulaire
    public function showLogin() {
        return view('auth.login');
    }

    // Traite la connexion
    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Si c'est un admin, on l'envoie au dashboard
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin');
            }
            
            // Sinon on le déconnecte (car seul l'admin a le droit d'être ici)
            Auth::logout();
            return back()->withErrors(['email' => 'Accès réservé aux administrateurs.']);
        }

        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ]);
    }

    // Déconnexion
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
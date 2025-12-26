<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Page d'accueil (Redirige vers le login)
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'Authentification
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Groupe Admin (Protégé par le middleware 'auth')
// Seuls les gens connectés peuvent voir ça
Route::middleware(['auth'])->prefix('admin')->group(function () {
    
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/application/{id}', [AdminController::class, 'show'])->name('admin.show');
    Route::post('/application/{id}/validate', [AdminController::class, 'validateApplication'])->name('admin.validate');
    Route::post('/application/{id}/reject', [AdminController::class, 'rejectApplication'])->name('admin.reject');

});
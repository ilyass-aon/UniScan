<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Important pour l'API mobile

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs qui peuvent être assignés en masse.
     * (Protection contre la "Mass Assignment Vulnerability")
     * On ajoute 'role' pour la création, mais 'role' ne devrait
     * pas être modifiable par l'étudiant après création.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Ajouté
    ];

    /**
     * Les attributs à cacher lors de la sérialisation (ex: conversion en JSON pour l'API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à "caster" (convertir) dans des types natifs.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string', // On garde 'string' car ENUM est géré comme tel
    ];

    // --- RELATIONS ---

    /**
     * Relation: Un utilisateur (étudiant) peut avoir plusieurs candidatures.
     * (User Story: "En tant qu'étudiant, je veux voir le statut de mon dossier")
     */
    public function applications()
    {
        // Un User A "plusieurs" (hasMany) Application
        // La clé étrangère dans 'applications' est 'user_id'
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Relation: Un utilisateur (admin) peut traiter plusieurs candidatures.
     * (User Story: "En tant qu'admin, je veux voir un tableau de bord...")
     */
    public function applicationsTraitees()
    {
        // Un User (admin) A "plusieurs" (hasMany) Application
        // La clé étrangère dans 'applications' est 'admin_id'
        return $this->hasMany(Application::class, 'admin_id');
    }
}
package com.example.uniscan.models;

public class ApplicationRequest {
    private int filiere_id;
    private String nom_saisi;
    private String prenom_saisi;
    private String cin_saisi;
    private String cne_saisi;
    private double note_bac_saisie;
    private int annee_bac_saisie;

    public ApplicationRequest(int filiere_id, String nom, String prenom, String cin, String cne, double note, int annee) {
        this.filiere_id = filiere_id;
        this.nom_saisi = nom;
        this.prenom_saisi = prenom;
        this.cin_saisi = cin;
        this.cne_saisi = cne;
        this.note_bac_saisie = note;
        this.annee_bac_saisie = annee;
    }
}
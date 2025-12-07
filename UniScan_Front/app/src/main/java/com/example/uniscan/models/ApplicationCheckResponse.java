package com.example.uniscan.models;

import com.google.gson.annotations.SerializedName;

public class ApplicationCheckResponse {

    private boolean exists;

    // On recupere l objet application s'il existe
    @SerializedName("application")
    private ApplicationData application;

    public boolean isExists() {
        return exists;
    }

    public ApplicationData getApplication() {
        return application;
    }

    // Petite classe interne pour stocker les infos de la candidature (ID, statut...)
    public static class ApplicationData {
        private int id;
        private String status; // ex: "pending", "validated"

        public int getId() { return id; }
        public String getStatus() { return status; }
    }
}
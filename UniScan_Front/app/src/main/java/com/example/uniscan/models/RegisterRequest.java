package com.example.uniscan.models;

public class RegisterRequest {
    private String name;
    private String email;
    private String password;
    private String password_confirmation;

    public RegisterRequest(String name, String email, String password) {
        this.name = name;
        this.email = email;
        this.password = password;
        this.password_confirmation = password; // On duplique automatiquement pour Laravel
    }
}

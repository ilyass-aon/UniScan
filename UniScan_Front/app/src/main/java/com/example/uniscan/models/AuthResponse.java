package com.example.uniscan.models;
import com.google.gson.annotations.SerializedName;

public class AuthResponse {
    @SerializedName("access_token")
    private String token;
    private String message;


    public String getToken() {
        return token;
    }

    public String getMessage() {
        return message;
    }
}

package com.example.uniscan;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.example.uniscan.activities.DashboardActivity;
import com.example.uniscan.activities.RegisterActivity;
import com.example.uniscan.api.ApiClient;
import com.example.uniscan.api.ApiService;
import com.example.uniscan.models.AuthResponse;
import com.example.uniscan.models.LoginRequest;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends AppCompatActivity {

    private EditText etEmail, etPassword;
    private Button btnLogin;
    private TextView tvRegisterLink;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Lier les variables Java aux éléments XML
        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);
        btnLogin = findViewById(R.id.btnLogin);
        tvRegisterLink = findViewById(R.id.tvRegisterLink);

        //  Action du bouton Connexion
        btnLogin.setOnClickListener(v -> loginUser());

        //  Action du lien Inscription
        tvRegisterLink.setOnClickListener(v -> {
            Intent intent = new Intent(MainActivity.this, RegisterActivity.class);
            startActivity(intent);
            Toast.makeText(this, "Vers Inscription", Toast.LENGTH_SHORT).show();
        });
    }

    private void loginUser() {
        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();

        if (email.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show();
            return;
        }

        // Création de la requête
        LoginRequest loginRequest = new LoginRequest(email, password);
        ApiService apiService = ApiClient.getClient().create(ApiService.class);

        // Envoi à Laravel
        apiService.login(loginRequest).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                if (response.isSuccessful() && response.body() != null) {

                    // SUCCÈS : On récupère le token
                    String token = response.body().getToken();

                    // IMPORTANT : On sauvegarde le token dans le téléphone
                    SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
                    prefs.edit().putString("auth_token", token).apply();

                    Toast.makeText(MainActivity.this, "Connexion réussie !", Toast.LENGTH_SHORT).show();

                    // On change d'écran vers le Dashboard
                    Intent intent = new Intent(MainActivity.this, DashboardActivity.class);
                    startActivity(intent);
                    finish(); // Empêche de revenir en arrière avec le bouton retour

                } else {
                    Toast.makeText(MainActivity.this, "Erreur : Email ou mot de passe incorrect", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<AuthResponse> call, Throwable t) {
                Toast.makeText(MainActivity.this, "Erreur Réseau : " + t.getMessage(), Toast.LENGTH_LONG).show();
            }
        });
    }
}
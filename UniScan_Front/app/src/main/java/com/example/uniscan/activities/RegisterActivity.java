package com.example.uniscan.activities;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.example.uniscan.MainActivity;
import com.example.uniscan.R;
import com.example.uniscan.api.ApiClient;
import com.example.uniscan.api.ApiService;
import com.example.uniscan.models.AuthResponse;
import com.example.uniscan.models.RegisterRequest;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterActivity extends AppCompatActivity {

    // 1. Ajout de etConfirmPassword
    private EditText etName, etEmail, etPassword, etConfirmPassword;
    private Button btnRegister;
    private TextView tvLoginLink;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        etName = findViewById(R.id.etName);
        etEmail = findViewById(R.id.etEmail);
        etPassword = findViewById(R.id.etPassword);

        // 2. Liaison avec le XML
        etConfirmPassword = findViewById(R.id.etConfirmPassword);

        btnRegister = findViewById(R.id.btnRegister);
        tvLoginLink = findViewById(R.id.tvLoginLink);

        btnRegister.setOnClickListener(v -> registerUser());

        tvLoginLink.setOnClickListener(v -> {
            Intent intent = new Intent(RegisterActivity.this, MainActivity.class);
            startActivity(intent);

            finish();
        });
    }

    private void registerUser() {
        String name = etName.getText().toString().trim();
        String email = etEmail.getText().toString().trim();
        String password = etPassword.getText().toString().trim();
        // Récupération de la confirmation
        String confirmPassword = etConfirmPassword.getText().toString().trim();

        if (name.isEmpty() || email.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
            Toast.makeText(this, "Remplissez tous les champs", Toast.LENGTH_SHORT).show();
            return;
        }

        // 3. VÉRIFICATION CRUCIALE
        if (!password.equals(confirmPassword)) {
            etConfirmPassword.setError("Les mots de passe ne correspondent pas");
            etConfirmPassword.requestFocus();
            return; // On arrête tout ici
        }

        if (password.length() < 6) {
            etPassword.setError("Minimum 6 caractères");
            return;
        }

        // Le reste est identique. Notre modèle RegisterRequest duplique déjà
        // le password pour le champ 'password_confirmation' de Laravel.
        RegisterRequest request = new RegisterRequest(name, email, password);
        ApiService apiService = ApiClient.getClient().create(ApiService.class);

        apiService.register(request).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                if (response.isSuccessful() && response.body() != null) {
                    String token = response.body().getToken();
                    if (token != null) {
                        SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
                        prefs.edit().putString("auth_token", token).apply();

                        Toast.makeText(RegisterActivity.this, "Compte créé !", Toast.LENGTH_SHORT).show();

                        Intent intent = new Intent(RegisterActivity.this, DashboardActivity.class);
                        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                        startActivity(intent);
                        finish();
                    } else {
                        Toast.makeText(RegisterActivity.this, "Erreur: Token vide", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(RegisterActivity.this, "Erreur d'inscription", Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<AuthResponse> call, Throwable t) {
                Toast.makeText(RegisterActivity.this, "Erreur Réseau : " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}
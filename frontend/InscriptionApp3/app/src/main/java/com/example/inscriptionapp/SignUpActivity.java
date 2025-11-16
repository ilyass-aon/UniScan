package com.example.inscriptionapp;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import com.google.android.material.textfield.TextInputEditText;

public class SignUpActivity extends AppCompatActivity {

    TextInputEditText editTextEmail, editTextPassword, editTextConfirmPassword;
    Button buttonSignUp;
    TextView textViewGoToLogin;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_up);

        editTextEmail = findViewById(R.id.editTextEmailSignUp);
        editTextPassword = findViewById(R.id.editTextPasswordSignUp);
        editTextConfirmPassword = findViewById(R.id.editTextConfirmPassword);
        buttonSignUp = findViewById(R.id.buttonSignUp);
        textViewGoToLogin = findViewById(R.id.textViewGoToLogin);

        // Au clic sur "Déjà un compte ?"
        textViewGoToLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // On ferme l'activité actuelle pour retourner à LoginActivity
                finish();
            }
        });

        // Au clic sur "Créer le compte"
        buttonSignUp.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String email = editTextEmail.getText().toString();
                String password = editTextPassword.getText().toString();
                String confirmPassword = editTextConfirmPassword.getText().toString();

                if (email.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
                    Toast.makeText(SignUpActivity.this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show();
                    return;
                }

                if (!password.equals(confirmPassword)) {
                    Toast.makeText(SignUpActivity.this, "Les mots de passe ne correspondent pas",Toast.LENGTH_SHORT).show();
                    return;
                }

                // *** ICI, AJOUTEZ VOTRE LOGIQUE DE CRÉATION DE COMPTE ***
                // (Voir la recommandation Firebase ci-dessous)

                // Si la création réussit :
                Toast.makeText(SignUpActivity.this, "Compte créé avec succès", Toast.LENGTH_SHORT).show();
                finish(); // On retourne à la page de connexion
            }
        });
    }
}
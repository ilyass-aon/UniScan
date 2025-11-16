package com.example.inscriptionapp;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import com.google.android.material.textfield.TextInputEditText;

public class LoginActivity extends AppCompatActivity {

    TextInputEditText editTextEmail, editTextPassword;
    Button buttonLogin;
    TextView textViewGoToSignUp;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        editTextEmail = findViewById(R.id.editTextEmailLogin);
        editTextPassword = findViewById(R.id.editTextPasswordLogin);
        buttonLogin = findViewById(R.id.buttonLogin);
        textViewGoToSignUp = findViewById(R.id.textViewGoToSignUp);

        // Au clic sur "Pas de compte ?"
        textViewGoToSignUp.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // On ouvre SignUpActivity
                Intent intent = new Intent(LoginActivity.this, SignUpActivity.class);
                startActivity(intent);
            }
        });

        // Au clic sur "Se connecter"
        buttonLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String email = editTextEmail.getText().toString();
                String password = editTextPassword.getText().toString();

                // *** ICI, AJOUTEZ VOTRE LOGIQUE DE VÉRIFICATION DE COMPTE ***


                // Si la connexion réussit :
                Toast.makeText(LoginActivity.this, "Message", Toast.LENGTH_SHORT).show();

                // On ouvre la page principale (votre RegistrationActivity)
                Intent intent = new Intent(LoginActivity.this, RegistrationActivity.class);
                startActivity(intent);
                finish(); // On ferme LoginActivity pour ne pas y revenir avec "retour"
            }
        });
    }
}
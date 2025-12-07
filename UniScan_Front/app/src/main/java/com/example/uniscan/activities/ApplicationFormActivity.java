package com.example.uniscan.activities;

import androidx.appcompat.app.AppCompatActivity;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import com.example.uniscan.R;
import com.example.uniscan.api.ApiClient;
import com.example.uniscan.api.ApiService;
import com.example.uniscan.models.ApplicationCheckResponse;
import com.example.uniscan.models.ApplicationRequest;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ApplicationFormActivity extends AppCompatActivity {

    private EditText etNom, etPrenom, etCin, etCne, etNote, etAnnee;
    private Spinner spFiliere;
    private Button btnSubmit;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_application_form);

        // 1. Initialisation des vues
        etNom = findViewById(R.id.etNom);
        etPrenom = findViewById(R.id.etPrenom);
        etCin = findViewById(R.id.etCin);
        etCne = findViewById(R.id.etCne);
        etNote = findViewById(R.id.etNote);
        etAnnee = findViewById(R.id.etAnnee);
        spFiliere = findViewById(R.id.spFiliere);
        btnSubmit = findViewById(R.id.btnSubmit);

        // 2. Remplir le Spinner des Filières (En dur pour simplifier)
        // L'index 0 correspondra à l'ID 1, l'index 1 à l'ID 2, etc.
        String[] filieres = {"Génie Informatique", "Génie Civil", "Génie Industriel"};
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_dropdown_item, filieres);
        spFiliere.setAdapter(adapter);

        // 3. Action Valider
        btnSubmit.setOnClickListener(v -> submitApplication());
    }

    private void submitApplication() {
        // Récupération des valeurs
        String nom = etNom.getText().toString().trim();
        String prenom = etPrenom.getText().toString().trim();
        String cin = etCin.getText().toString().trim();
        String cne = etCne.getText().toString().trim();
        String noteStr = etNote.getText().toString().trim();
        String anneeStr = etAnnee.getText().toString().trim();

        // Validation rapide
        if (nom.isEmpty() || prenom.isEmpty() || cin.isEmpty() || noteStr.isEmpty() || anneeStr.isEmpty()) {
            Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show();
            return;
        }

        // Conversion des chiffres
        double note = Double.parseDouble(noteStr);
        int annee = Integer.parseInt(anneeStr);

        // Calcul de l'ID Filière (Position dans la liste + 1 car les ID SQL commencent souvent à 1)
        int filiereId = spFiliere.getSelectedItemPosition() + 1;

        // Préparation de l'envoi
        ApplicationRequest request = new ApplicationRequest(filiereId, nom, prenom, cin, cne, note, annee);

        // Récupération du Token
        SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
        String token = prefs.getString("auth_token", "");

        ApiService apiService = ApiClient.getClient().create(ApiService.class);
        apiService.createApplication("Bearer " + token, request).enqueue(new Callback<ApplicationCheckResponse>() {
            @Override
            public void onResponse(Call<ApplicationCheckResponse> call, Response<ApplicationCheckResponse> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(ApplicationFormActivity.this, "Candidature créée !", Toast.LENGTH_LONG).show();

                    // On ferme cette page pour revenir au Dashboard
                    // Le Dashboard va se recharger (onResume) et voir qu'on a maintenant une candidature !
                    finish();
                } else {
                    Toast.makeText(ApplicationFormActivity.this, "Erreur création: " + response.code(), Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ApplicationCheckResponse> call, Throwable t) {
                Toast.makeText(ApplicationFormActivity.this, "Erreur Réseau", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
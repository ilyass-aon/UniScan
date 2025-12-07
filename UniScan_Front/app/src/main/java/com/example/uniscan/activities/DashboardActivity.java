package com.example.uniscan.activities;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.example.uniscan.MainActivity;
import com.example.uniscan.R;
import com.example.uniscan.api.ApiClient;
import com.example.uniscan.api.ApiService;
import com.example.uniscan.models.ApplicationCheckResponse;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DashboardActivity extends AppCompatActivity {

    private Button btnUpload, btnLogout, btnCreateApplication;
    private TextView tvStatus;
    private LinearLayout layoutHasApplication, layoutNoApplication;

    // Pour stocker l'ID de la candidature (nécessaire pour l'upload)
    private int applicationId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dashboard);

        // 1. Initialisation des vues
        btnUpload = findViewById(R.id.btnUpload);
        btnLogout = findViewById(R.id.btnLogout);
        btnCreateApplication = findViewById(R.id.btnCreateApplication);
        tvStatus = findViewById(R.id.tvStatus);
        layoutHasApplication = findViewById(R.id.layoutHasApplication);
        layoutNoApplication = findViewById(R.id.layoutNoApplication);

        // 2. Vérification Token
        SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
        String token = prefs.getString("auth_token", null);

        if (token == null) {
            goToLogin();
            return;
        }

        // 3. APPEL API : Vérifier l'état du dossier
        checkApplicationStatus("Bearer " + token);

        // 4. Actions des boutons
        btnUpload.setOnClickListener(v -> {
            if (applicationId != -1) {
                Intent intent = new Intent(DashboardActivity.this, UploadActivity.class);
                intent.putExtra("APP_ID", applicationId);
                intent.putExtra("DOC_TYPE", "bac_releve");
                startActivity(intent);
            } else {
                Toast.makeText(this, "Erreur ID candidature", Toast.LENGTH_SHORT).show();
            }
        });

        btnCreateApplication.setOnClickListener(v -> {
            Intent intent = new Intent(DashboardActivity.this, ApplicationFormActivity.class);
            startActivity(intent);
        });

        btnLogout.setOnClickListener(v -> {
            prefs.edit().clear().apply();
            goToLogin();
        });
    }

    // Méthode pour interroger l'API
    private void checkApplicationStatus(String bearerToken) {
        ApiService apiService = ApiClient.getClient().create(ApiService.class);

        apiService.checkMyApplication(bearerToken).enqueue(new Callback<ApplicationCheckResponse>() {
            @Override
            public void onResponse(Call<ApplicationCheckResponse> call, Response<ApplicationCheckResponse> response) {
                if (response.isSuccessful() && response.body() != null) {

                    ApplicationCheckResponse data = response.body();

                    if (data.isExists()) {
                        // CAS A : Il a un dossier
                        showHasApplicationUI();

                        // On met à jour les infos
                        tvStatus.setText("Statut : " + data.getApplication().getStatus());
                        applicationId = data.getApplication().getId();

                    } else {
                        // CAS B : Il a pas de dossier
                        showNoApplicationUI();
                    }
                } else {
                    Toast.makeText(DashboardActivity.this, "Erreur serveur vérification", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<ApplicationCheckResponse> call, Throwable t) {
                Toast.makeText(DashboardActivity.this, "Erreur réseau: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }


    private void showHasApplicationUI() {
        layoutHasApplication.setVisibility(View.VISIBLE);
        layoutNoApplication.setVisibility(View.GONE);
    }


    private void showNoApplicationUI() {
        layoutHasApplication.setVisibility(View.GONE);
        layoutNoApplication.setVisibility(View.VISIBLE);
    }

    private void goToLogin() {
        startActivity(new Intent(this, MainActivity.class));
        finish();
    }


    @Override
    protected void onResume() {
        super.onResume();
        SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
        String token = prefs.getString("auth_token", null);
        if (token != null) {
            checkApplicationStatus("Bearer " + token);
        }
    }
}
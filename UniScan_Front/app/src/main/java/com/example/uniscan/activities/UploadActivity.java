package com.example.uniscan.activities;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.example.uniscan.R;
import com.example.uniscan.api.ApiClient;
import com.example.uniscan.api.ApiService;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;

import okhttp3.MediaType;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import com.example.uniscan.api.ApiClient;




public class UploadActivity extends AppCompatActivity {

    private ImageView ivPreview;
    private Button btnPickImage, btnSend;
    private TextView tvTitle;

    private Uri selectedImageUri = null;
    private String docType;
    private int applicationId;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_upload);

        // 1. Récupération des données passées par le Dashboard
        Intent intent = getIntent();
        docType = intent.getStringExtra("DOC_TYPE"); // "cin_recto" ou "bac_releve"
        applicationId = intent.getIntExtra("APP_ID", -1);

        // Sécurité : Si pas d'ID, on ne peut rien faire
        if (applicationId == -1) {
            Toast.makeText(this, "Erreur ID Candidature", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // 2. Initialisation UI
        ivPreview = findViewById(R.id.ivPreview);
        btnPickImage = findViewById(R.id.btnPickImage);
        btnSend = findViewById(R.id.btnSend);
        tvTitle = findViewById(R.id.tvTitle);

        if ("cin_recto".equals(docType)) {
            tvTitle.setText("Scanner la CIN");
        } else {
            tvTitle.setText("Scanner le Bac");
        }

        // 3. Actions
        btnPickImage.setOnClickListener(v -> openGallery());

        btnSend.setOnClickListener(v -> uploadImageToServer());
    }

    private void openGallery() {
        Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
        galleryLauncher.launch(intent);
    }

    private final ActivityResultLauncher<Intent> galleryLauncher = registerForActivityResult(
            new ActivityResultContracts.StartActivityForResult(),
            result -> {
                if (result.getResultCode() == RESULT_OK && result.getData() != null) {
                    selectedImageUri = result.getData().getData();
                    ivPreview.setImageURI(selectedImageUri);
                    btnSend.setEnabled(true); // On active le bouton envoyer
                    btnSend.setBackgroundTintList(getResources().getColorStateList(com.google.android.material.R.color.design_default_color_primary_dark, getTheme())); // Petit effet visuel optionnel
                }
            }
    );



    private void uploadImageToServer() {
        if (selectedImageUri == null) return;

        // A. Préparation du fichier
        File file = uriToFile(selectedImageUri); // On convertit l'URI en vrai Fichier
        if (file == null) {
            Toast.makeText(this, "Erreur conversion image", Toast.LENGTH_SHORT).show();
            return;
        }

        // B. Préparation pour Retrofit (Multipart)
        // 1. Le corps du fichier (image/*)
        RequestBody requestFile = RequestBody.create(MediaType.parse("image/*"), file);

        // 2. L'enveloppe "document_file" (C'est le nom qu'on a mis dans Laravel $request->file('document_file'))
        MultipartBody.Part body = MultipartBody.Part.createFormData("document_file", file.getName(), requestFile);

        // 3. Le champ texte "type_document"
        RequestBody typeDocPart = RequestBody.create(MediaType.parse("text/plain"), docType);

        // C. Récupération du Token
        SharedPreferences prefs = getSharedPreferences("UniScanPrefs", MODE_PRIVATE);
        String token = prefs.getString("auth_token", "");

        // D. Envoi !
        Toast.makeText(this, "Envoi en cours...", Toast.LENGTH_LONG).show();
        btnSend.setEnabled(false); // On désactive pour éviter le double clic

        ApiService apiService = ApiClient.getClient().create(ApiService.class);
        Call<ResponseBody> call = apiService.uploadDocument("Bearer " + token, applicationId, body, typeDocPart);

        call.enqueue(new Callback<ResponseBody>() {
            @Override
            public void onResponse(Call<ResponseBody> call, Response<ResponseBody> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(UploadActivity.this, "Envoyé avec succès !", Toast.LENGTH_LONG).show();
                    finish(); // On revient au Dashboard
                } else {
                    Toast.makeText(UploadActivity.this, "Erreur serveur : " + response.code(), Toast.LENGTH_LONG).show();
                    btnSend.setEnabled(true);
                }
            }

            @Override
            public void onFailure(Call<ResponseBody> call, Throwable t) {
                Toast.makeText(UploadActivity.this, "Erreur Réseau : " + t.getMessage(), Toast.LENGTH_LONG).show();
                Log.e("UPLOAD_ERROR", t.getMessage());
                btnSend.setEnabled(true);
            }
        });
    }

    /**
     * Méthode utilitaire indispensable sur Android récent.
     * Elle copie l'image de la galerie vers un fichier temporaire dans l'application
     * pour qu'on ait le droit de l'envoyer.
     */
    private File uriToFile(Uri uri) {
        try {
            InputStream inputStream = getContentResolver().openInputStream(uri);
            // On crée un fichier vide dans le cache
            File tempFile = new File(getCacheDir(), "upload_temp.jpg");
            FileOutputStream outputStream = new FileOutputStream(tempFile);

            // On copie le contenu
            byte[] buffer = new byte[1024];
            int length;
            while ((length = inputStream.read(buffer)) > 0) {
                outputStream.write(buffer, 0, length);
            }

            outputStream.close();
            inputStream.close();
            return tempFile;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
}
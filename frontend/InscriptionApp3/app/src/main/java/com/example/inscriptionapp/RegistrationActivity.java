package com.example.inscriptionapp;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Patterns;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputLayout;

import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Pattern;

public class RegistrationActivity extends AppCompatActivity {

    // Références vues
    private TextInputLayout tilName, tilCin, tilDob, tilEmail, tilPhone;
    private EditText etName, etCin, etDob, etEmail, etPhone;
    private AutoCompleteTextView spNiveau, spFiliere;
    private MaterialButton btnSubmit;

    // Données
    private final String[] niveaux = new String[]{"Licence", "Master", "Ingénierie", "Technicien"};
    private final Map<String, String[]> filieresParNiveau = new HashMap<>();
    private static final Pattern CIN_PATTERN = Pattern.compile("^[A-Z]{1,2}[0-9]{4,8}$");

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_registration); // ton XML

        mapViews();
        initData();
        initPickers();
        initSubmit();
    }

    private void mapViews() {
        tilName = findViewById(R.id.tilName);
        tilCin = findViewById(R.id.tilCin);
        tilDob = findViewById(R.id.tilDob);
        tilEmail = findViewById(R.id.tilEmail);
        tilPhone = findViewById(R.id.tilPhone);

        etName = findViewById(R.id.etName);
        etCin = findViewById(R.id.etCin);
        etDob = findViewById(R.id.etDob);
        etEmail = findViewById(R.id.etEmail);
        etPhone = findViewById(R.id.etPhone);

        spNiveau = findViewById(R.id.spNiveau);
        spFiliere = findViewById(R.id.spFiliere);
        btnSubmit = findViewById(R.id.btnSubmit);
    }

    private void initData() {
        filieresParNiveau.put("Licence", new String[]{"Informatique", "Maths", "Éco-Gestion"});
        filieresParNiveau.put("Master", new String[]{"DSIA", "Systèmes Distribués", "Finance"});
        filieresParNiveau.put("Ingénierie", new String[]{"GI", "GE", "GTR", "Génie Civil"});
        filieresParNiveau.put("Technicien", new String[]{"Réseaux", "Développement", "Electrotech"});

        spNiveau.setAdapter(new ArrayAdapter<>(this, android.R.layout.simple_list_item_1, niveaux));

        spNiveau.setOnItemClickListener(new android.widget.AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(android.widget.AdapterView<?> parent, android.view.View view, int position, long id) {
                String niv = niveaux[position];
                String[] filieres = filieresParNiveau.get(niv);
                spFiliere.setText("", false);
                spFiliere.setAdapter(new ArrayAdapter<>(RegistrationActivity.this,
                        android.R.layout.simple_list_item_1, filieres));
            }
        });
    }

    private void initPickers() {
        etDob.setOnClickListener(v -> openDatePicker());
    }

    private void openDatePicker() {
        Calendar c = Calendar.getInstance();
        int y = c.get(Calendar.YEAR) - 18;
        int m = c.get(Calendar.MONTH);
        int d = c.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog dp = new DatePickerDialog(this, new DatePickerDialog.OnDateSetListener() {
            @Override
            public void onDateSet(DatePicker view, int year, int month, int dayOfMonth) {
                etDob.setText(String.format("%02d/%02d/%04d", dayOfMonth, month + 1, year));
            }
        }, y, m, d);
        dp.show();
    }

    private void initSubmit() {
        btnSubmit.setOnClickListener(v -> {
            clearErrors();
            if (validate()) {
                String msg = "OK ✅\n"
                        + "Nom: " + etName.getText() + "\n"
                        + "CIN: " + etCin.getText() + "\n"
                        + "DOB: " + etDob.getText() + "\n"
                        + "Email: " + etEmail.getText() + "\n"
                        + "Phone: " + etPhone.getText() + "\n"
                        + "Niveau: " + spNiveau.getText() + "\n"
                        + "Filière: " + spFiliere.getText();
                Toast.makeText(this, msg, Toast.LENGTH_LONG).show();
                Intent intent = new Intent(RegistrationActivity.this, DocumentUploadActivity.class);
                startActivity(intent);
            }
        });
    }

    private void clearErrors() {
        tilName.setError(null);
        tilCin.setError(null);
        tilDob.setError(null);
        tilEmail.setError(null);
        tilPhone.setError(null);
    }

    private boolean validate() {
        boolean ok = true;

        if (TextUtils.isEmpty(etName.getText())) { tilName.setError("Nom requis"); ok = false; }
        String cin = etCin.getText().toString().trim();
        if (TextUtils.isEmpty(cin) || !CIN_PATTERN.matcher(cin).matches()) { tilCin.setError("CIN invalide"); ok = false; }
        if (TextUtils.isEmpty(etDob.getText())) { tilDob.setError("Date requise"); ok = false; }
        String email = etEmail.getText().toString().trim();
        if (TextUtils.isEmpty(email) || !Patterns.EMAIL_ADDRESS.matcher(email).matches()) { tilEmail.setError("Email invalide"); ok = false; }
        String phoneDigits = etPhone.getText().toString().replaceAll("\\D", "");
        if (phoneDigits.length() < 8) { tilPhone.setError("Téléphone invalide"); ok = false; }
        if (TextUtils.isEmpty(spNiveau.getText())) { spNiveau.setError("Choisir un niveau"); ok = false; }
        if (TextUtils.isEmpty(spFiliere.getText())) { spFiliere.setError("Choisir une filière"); ok = false; }

        return ok;
    }
}

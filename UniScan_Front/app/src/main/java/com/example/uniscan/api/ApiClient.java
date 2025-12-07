package com.example.uniscan.api;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {


    private static final String BASE_URL = "http://10.0.2.2:8000/api/";

    private static Retrofit retrofit = null;


    //  Cette méthode crée une instance unique de Retrofit (Singleton).

    public static Retrofit getClient() {
        if (retrofit == null) {

            // On ajoute un outil pour voir les requêtes dans le "Logcat" (très utile pour débugger)
            HttpLoggingInterceptor interceptor = new HttpLoggingInterceptor();
            interceptor.setLevel(HttpLoggingInterceptor.Level.BODY);

            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(interceptor)
                    .build();

            // On construit l'objet Retrofit
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .client(client)
                    .addConverterFactory(GsonConverterFactory.create()) // Pour convertir le JSON automatiquement
                    .build();
        }
        return retrofit;
    }
}

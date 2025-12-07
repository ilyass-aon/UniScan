package com.example.uniscan.api;

import com.example.uniscan.models.ApplicationCheckResponse;
import com.example.uniscan.models.ApplicationRequest;
import com.example.uniscan.models.AuthResponse;
import com.example.uniscan.models.LoginRequest;
import com.example.uniscan.models.RegisterRequest;

import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import okhttp3.ResponseBody;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.Multipart;
import retrofit2.http.POST;
import retrofit2.http.GET;
import retrofit2.http.Header;
import retrofit2.http.Part;
import retrofit2.http.Path;


public interface ApiService {

    @POST("login")
    Call<AuthResponse> login(@Body LoginRequest request);

    @POST("register")
    Call<AuthResponse> register(@Body RegisterRequest request);

    @GET("my-application")
    Call<ApplicationCheckResponse> checkMyApplication(@Header("Authorization") String token);

    @POST("application")
    Call<ApplicationCheckResponse> createApplication(
            @Header("Authorization") String token,
            @Body ApplicationRequest request
    );

    @Multipart
    @POST("application/{id}/document")
    Call<ResponseBody> uploadDocument(
            @Header("Authorization") String token,
            @Path("id") int applicationId,
            @Part MultipartBody.Part file,
            @Part("type_document") RequestBody type
    );
}
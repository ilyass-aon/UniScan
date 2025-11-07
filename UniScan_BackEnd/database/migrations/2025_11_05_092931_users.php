<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // id BIGINT PRIMARY KEY, AUTO_INCREMENT
            $table->id(); 
            
            // name VARCHAR(255)
            $table->string('name');
            
            // email VARCHAR(255) UNIQUE
            $table->string('email')->unique();
            
            // email_verified_at TIMESTAMP (Optionnel)
            $table->timestamp('email_verified_at')->nullable();
            
            // password VARCHAR(255)
            $table->string('password');
            
            // role ENUM('etudiant', 'admin') NOT NULL, DEFAULT 'etudiant'
            $table->enum('role', ['etudiant', 'admin'])->default('etudiant');
            
            // remember_token VARCHAR(100) (Pour Laravel)
            $table->rememberToken();
            
            // created_at et updated_at TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

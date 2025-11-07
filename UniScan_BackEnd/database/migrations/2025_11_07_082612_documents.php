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
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relation vers applications
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');

            // Informations sur le document
            $table->enum('type_document', ['cin_recto', 'cin_verso', 'bac_releve']);
            $table->string('nom_original', 255);
            $table->string('chemin_fichier', 255)->unique();

            // Données extraites de l'OCR
            $table->enum('ocr_status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('texte_ocr_brut')->nullable();
            $table->json('data_extraite_json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

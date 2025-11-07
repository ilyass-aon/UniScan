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
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relations avec les autres tables
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');

            // Les informations saisies
            $table->enum('status', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->string('nom_saisi', 255);
            $table->string('prenom_saisi', 255);
            $table->string('cin_saisi', 50);
            $table->string('cne_saisi', 50);
            $table->decimal('note_bac_saisie', 4, 2);
            $table->year('annee_bac_saisie');
            $table->text('motif_rejet')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
            
            // Pour éviter un user qui applique plusieurs fois à la même filière
            $table->unique(['user_id', 'filiere_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

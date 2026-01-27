<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // attestation_travail, certificat_emploi, etc.
            $table->text('message')->nullable(); // Message de l'employé
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_response')->nullable(); // Réponse de l'admin
            $table->string('document_path')->nullable(); // Fichier joint
            $table->string('document_name')->nullable(); // Nom original du fichier
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};

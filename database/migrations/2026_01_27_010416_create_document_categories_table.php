<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catégories de documents
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->enum('owner_type', ['employee', 'company', 'both'])->default('both');
            $table->boolean('requires_validation')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Types de documents
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('document_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Règles
            $table->boolean('is_required')->default(false);
            $table->boolean('employee_can_upload')->default(true);
            $table->boolean('employee_can_view')->default(true);
            $table->boolean('employee_can_delete')->default(false);
            $table->boolean('requires_validation')->default(false);
            $table->boolean('has_expiry_date')->default(false);
            $table->boolean('is_unique')->default(true);
            
            // Fichiers
            $table->json('allowed_extensions')->nullable();
            $table->integer('max_size_mb')->default(5);
            
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Documents
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            
            // Fichier
            $table->string('title');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size');
            
            // Métadonnées
            $table->text('description')->nullable();
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Workflow de validation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            
            // Accusé de réception (pour règlement intérieur)
            $table->boolean('requires_acknowledgment')->default(false);
            $table->timestamp('acknowledged_at')->nullable();
            
            // Tracking
            $table->foreignId('uploaded_by')->constrained('users');
            $table->integer('download_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['user_id', 'document_type_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('document_categories');
    }
};

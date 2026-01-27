<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            
            // Contenu
            $table->string('title');
            $table->longText('content');
            $table->enum('type', ['info', 'success', 'warning', 'urgent', 'event'])->default('info');
            $table->enum('priority', ['normal', 'high', 'critical'])->default('normal');
            
            // Ciblage
            $table->enum('target_type', ['all', 'department', 'position', 'custom'])->default('all');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->json('target_user_ids')->nullable();
            
            // Visibilité & Planification
            $table->boolean('is_active')->default(true);
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('publish_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            // Accusé de réception
            $table->boolean('requires_acknowledgment')->default(false);
            
            // Pièces jointes
            $table->json('attachments')->nullable();
            
            // Tracking
            $table->foreignId('created_by')->constrained('users');
            $table->integer('view_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['is_active', 'publish_at', 'start_date', 'end_date']);
        });

        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamp('acknowledged_at')->nullable();
            
            $table->unique(['announcement_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');
        Schema::dropIfExists('announcements');
    }
};

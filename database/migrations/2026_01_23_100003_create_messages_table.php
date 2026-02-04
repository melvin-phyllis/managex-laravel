<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->string('type')->default('text'); // text, file, system, voice
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
            $table->index(['parent_id']);
        });

        // Fulltext index only for MySQL (SQLite doesn't support it)
        if (DB::connection()->getDriverName() === 'mysql') {
            Schema::table('messages', function (Blueprint $table) {
                $table->fullText(['content']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

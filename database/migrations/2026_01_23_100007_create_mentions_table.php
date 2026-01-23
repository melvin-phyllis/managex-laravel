<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['user', 'group', 'all'])->default('user');
            $table->unsignedBigInteger('target_id')->nullable(); // Group/team ID if type=group
            $table->timestamps();

            $table->index(['message_id']);
            $table->index(['user_id']);
            $table->index(['type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentions');
    }
};

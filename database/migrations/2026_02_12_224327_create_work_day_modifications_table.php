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
        Schema::create('work_day_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('week_start'); // Le lundi de la semaine
            $table->timestamp('modified_at');
            $table->json('old_days'); // Jours avant modification [1,2,3,4,5]
            $table->json('new_days'); // Jours après modification [1,3,5]
            $table->timestamps();

            $table->index(['user_id', 'week_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_day_modifications');
    }
};

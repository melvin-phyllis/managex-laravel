<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('stage_requests', 'source')) {
                $table->string('source')->default('form');
            }
            if (! Schema::hasColumn('stage_requests', 'source_uid')) {
                $table->string('source_uid')->nullable()->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            if (Schema::hasColumn('stage_requests', 'source_uid')) {
                $table->dropUnique(['source_uid']);
                $table->dropColumn('source_uid');
            }
            if (Schema::hasColumn('stage_requests', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};


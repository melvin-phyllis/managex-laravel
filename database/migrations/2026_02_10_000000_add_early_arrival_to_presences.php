<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->datetime('pre_check_in')->nullable()->after('check_in');
            $table->decimal('pre_check_in_latitude', 10, 7)->nullable()->after('check_in_latitude');
            $table->decimal('pre_check_in_longitude', 10, 7)->nullable()->after('pre_check_in_latitude');
            $table->boolean('is_early_arrival')->default(false)->after('is_late');
        });
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn([
                'pre_check_in',
                'pre_check_in_latitude',
                'pre_check_in_longitude',
                'is_early_arrival',
            ]);
        });
    }
};

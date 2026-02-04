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
        Schema::table('presences', function (Blueprint $table) {
            $table->decimal('check_in_latitude', 10, 8)->nullable()->after('check_in');
            $table->decimal('check_in_longitude', 11, 8)->nullable()->after('check_in_latitude');
            $table->decimal('check_out_latitude', 10, 8)->nullable()->after('check_out');
            $table->decimal('check_out_longitude', 11, 8)->nullable()->after('check_out_latitude');
            $table->foreignId('geolocation_zone_id')->nullable()->after('notes')->constrained()->nullOnDelete();
            $table->enum('check_in_status', ['in_zone', 'out_of_zone', 'unknown'])->default('unknown')->after('geolocation_zone_id');
            $table->enum('check_out_status', ['in_zone', 'out_of_zone', 'unknown'])->nullable()->after('check_in_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropForeign(['geolocation_zone_id']);
            $table->dropColumn([
                'check_in_latitude', 'check_in_longitude',
                'check_out_latitude', 'check_out_longitude',
                'geolocation_zone_id', 'check_in_status', 'check_out_status',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // CIV Fiscal fields for quotient familial calculation
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])
                  ->default('single')
                  ->after('gender');

            $table->unsignedTinyInteger('children_count')
                  ->default(0)
                  ->after('marital_status');

            $table->decimal('number_of_parts', 3, 1)
                  ->nullable()
                  ->comment('Manual override for fiscal parts calculation')
                  ->after('children_count');

            // CNPS number specific to Cote d Ivoire
            $table->string('cnps_number', 30)
                  ->nullable()
                  ->after('social_security_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'marital_status',
                'children_count',
                'number_of_parts',
                'cnps_number',
            ]);
        });
    }
};

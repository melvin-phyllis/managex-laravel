<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            $table->timestamp('retained_mail_sent_at')->nullable()->after('final_status');
        });
    }

    public function down(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            $table->dropColumn('retained_mail_sent_at');
        });
    }
};


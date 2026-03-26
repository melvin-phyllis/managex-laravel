<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            $table->timestamp('interview_at')->nullable()->after('admin_note');
            $table->string('interview_type')->nullable()->after('interview_at'); // phone, visio, onsite
            $table->unsignedTinyInteger('score_technical')->nullable()->after('interview_type'); // /10
            $table->unsignedTinyInteger('score_communication')->nullable()->after('score_technical'); // /10
            $table->unsignedTinyInteger('score_motivation')->nullable()->after('score_communication'); // /10
            $table->string('final_status')->nullable()->after('score_motivation'); // retained, waitlist, rejected
        });
    }

    public function down(): void
    {
        Schema::table('stage_requests', function (Blueprint $table) {
            $table->dropColumn([
                'interview_at',
                'interview_type',
                'score_technical',
                'score_communication',
                'score_motivation',
                'final_status',
            ]);
        });
    }
};


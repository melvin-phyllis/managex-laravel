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
        Schema::table('users', function (Blueprint $table) {
            // Informations personnelles
            $table->date('date_of_birth')->nullable()->after('telephone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('postal_code', 20)->nullable()->after('city');
            $table->string('country', 100)->default('France')->after('postal_code');

            // Contact d'urgence
            $table->string('emergency_contact_name')->nullable()->after('country');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship', 50)->nullable()->after('emergency_contact_phone');

            // Informations professionnelles
            $table->date('hire_date')->nullable()->after('emergency_contact_relationship');
            $table->date('contract_end_date')->nullable()->after('hire_date');
            $table->enum('contract_type', ['cdi', 'cdd', 'stage', 'alternance', 'freelance', 'interim'])->default('cdi')->after('contract_end_date');
            $table->decimal('base_salary', 10, 2)->nullable()->after('contract_type');
            $table->string('employee_id', 50)->nullable()->unique()->after('base_salary'); // Matricule

            // Informations administratives
            $table->string('social_security_number', 50)->nullable()->after('employee_id');
            $table->string('bank_iban', 50)->nullable()->after('social_security_number');
            $table->string('bank_bic', 20)->nullable()->after('bank_iban');

            // Soldes de congés
            $table->decimal('leave_balance', 5, 2)->default(25)->after('bank_bic'); // Congés payés
            $table->decimal('sick_leave_balance', 5, 2)->default(0)->after('leave_balance'); // Congés maladie
            $table->decimal('rtt_balance', 5, 2)->default(0)->after('sick_leave_balance'); // RTT

            // Statut
            $table->enum('status', ['active', 'on_leave', 'suspended', 'terminated'])->default('active')->after('rtt_balance');

            // Notes
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'address',
                'city',
                'postal_code',
                'country',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship',
                'hire_date',
                'contract_end_date',
                'contract_type',
                'base_salary',
                'employee_id',
                'social_security_number',
                'bank_iban',
                'bank_bic',
                'leave_balance',
                'sick_leave_balance',
                'rtt_balance',
                'status',
                'notes',
            ]);
        });
    }
};

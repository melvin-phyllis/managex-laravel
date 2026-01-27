<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Reference to contract used for this payroll
            $table->foreignId('contract_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->nullOnDelete();

            // ===== REVENUES (Gains) =====
            $table->decimal('gross_salary', 12, 2)->default(0)->after('annee');
            $table->decimal('transport_allowance', 12, 2)->default(0)->after('gross_salary');
            $table->decimal('housing_allowance', 12, 2)->default(0)->after('transport_allowance');
            $table->decimal('other_allowances', 12, 2)->default(0)->after('housing_allowance');
            $table->decimal('overtime_amount', 12, 2)->default(0)->after('other_allowances');
            $table->decimal('bonuses', 12, 2)->default(0)->after('overtime_amount');

            // ===== TAXABLE BASE =====
            $table->decimal('taxable_gross', 12, 2)->default(0)
                  ->comment('Brut Imposable')
                  ->after('bonuses');

            // ===== CIV TAXES (Retenues) =====
            // IS - Impot sur Salaire (1.2%)
            $table->decimal('tax_is', 12, 2)->default(0)
                  ->comment('Impot sur Salaire - 1.2% of taxable gross')
                  ->after('taxable_gross');

            // CN - Contribution Nationale (progressive 0-10%)
            $table->decimal('tax_cn', 12, 2)->default(0)
                  ->comment('Contribution Nationale - progressive rates')
                  ->after('tax_is');

            // IGR - Impot General sur le Revenu (quotient familial)
            $table->decimal('tax_igr', 12, 2)->default(0)
                  ->comment('Impot General sur le Revenu')
                  ->after('tax_cn');

            // CNPS - Caisse Nationale de Prevoyance Sociale
            $table->decimal('cnps_employee', 12, 2)->default(0)
                  ->comment('CNPS Employee contribution - 6.3% capped')
                  ->after('tax_igr');

            // Employer CNPS contribution (for reporting only, not deducted)
            $table->decimal('cnps_employer', 12, 2)->default(0)
                  ->comment('CNPS Employer contribution for reporting')
                  ->after('cnps_employee');

            // ===== TOTALS =====
            $table->decimal('total_deductions', 12, 2)->default(0)->after('cnps_employer');
            $table->decimal('net_salary', 12, 2)->default(0)->after('total_deductions');

            // ===== FISCAL SNAPSHOT (stored at calculation time) =====
            $table->decimal('fiscal_parts', 3, 1)->default(1)->after('net_salary');
            $table->unsignedTinyInteger('worked_days')->default(0)->after('fiscal_parts');
            $table->unsignedTinyInteger('absence_days')->default(0)->after('worked_days');

            // ===== METADATA =====
            $table->timestamp('calculated_at')->nullable()->after('absence_days');
            $table->string('calculation_version', 20)->default('v1.0')->after('calculated_at');
        });

        // Rename montant to legacy_montant for backwards compatibility
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('montant', 'legacy_montant');
        });

        // Add default value to legacy_montant
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('legacy_montant', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('legacy_montant', 'montant');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn([
                'contract_id',
                'gross_salary',
                'transport_allowance',
                'housing_allowance',
                'other_allowances',
                'overtime_amount',
                'bonuses',
                'taxable_gross',
                'tax_is',
                'tax_cn',
                'tax_igr',
                'cnps_employee',
                'cnps_employer',
                'total_deductions',
                'net_salary',
                'fiscal_parts',
                'worked_days',
                'absence_days',
                'calculated_at',
                'calculation_version',
            ]);
        });
    }
};

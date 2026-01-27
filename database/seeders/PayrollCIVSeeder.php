<?php

namespace Database\Seeders;

use App\Models\PayrollCountry;
use App\Models\PayrollCountryRule;
use App\Models\PayrollCountryField;
use App\Models\PayrollTemplate;
use Illuminate\Database\Seeder;

class PayrollCIVSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer le pays Côte d'Ivoire
        $country = PayrollCountry::updateOrCreate(
            ['code' => 'CIV'],
            [
                'name' => 'Côte d\'Ivoire',
                'currency' => 'XOF',
                'currency_symbol' => 'FCFA',
                'legal_mentions' => [
                    'company_cnps' => 'N° CNPS Employeur obligatoire',
                    'employee_cnps' => 'N° CNPS Employé obligatoire',
                ],
                'is_active' => true,
            ]
        );

        // 2. Créer les règles de calcul
        $this->createRules($country);

        // 3. Créer les champs dynamiques
        $this->createFields($country);

        // 4. Créer le template PDF
        PayrollTemplate::updateOrCreate(
            ['country_id' => $country->id, 'blade_path' => 'pdf.payroll-civ'],
            [
                'name' => 'Bulletin Standard CIV (Format RMO)',
                'description' => 'Format standard conforme à la réglementation ivoirienne',
                'is_default' => true,
            ]
        );
    }

    protected function createRules(PayrollCountry $country): void
    {
        $rules = [
            // === TAXES ===
            [
                'rule_type' => 'tax',
                'rule_category' => 'employee',
                'code' => 'IS',
                'label' => 'Impôt sur le Salaire (IS)',
                'description' => 'Impôt fixe de 1,2% sur le brut imposable',
                'calculation_type' => 'percentage',
                'rate' => 1.2,
                'base_field' => 'taxable_gross',
                'is_deductible' => false,
                'display_order' => 10,
                'pdf_code' => '370',
            ],
            [
                'rule_type' => 'tax',
                'rule_category' => 'employee',
                'code' => 'CN',
                'label' => 'Contribution Nationale (CN)',
                'description' => 'Barème progressif sur le brut imposable',
                'calculation_type' => 'bracket',
                'base_field' => 'taxable_gross',
                'brackets' => [
                    ['min' => 0, 'max' => 50000, 'rate' => 0],
                    ['min' => 50000, 'max' => 130000, 'rate' => 0.015],
                    ['min' => 130000, 'max' => 200000, 'rate' => 0.05],
                    ['min' => 200000, 'max' => null, 'rate' => 0.10],
                ],
                'is_deductible' => true,
                'display_order' => 20,
                'pdf_code' => '380',
            ],
            [
                'rule_type' => 'tax',
                'rule_category' => 'employee',
                'code' => 'IGR',
                'label' => 'Impôt Général sur le Revenu (IGR)',
                'description' => 'Barème progressif après IS et CN, divisé par nombre de parts',
                'calculation_type' => 'bracket',
                'base_field' => 'igr_base', // Calculé: taxable_gross - IS - CN - CNPS
                'brackets' => [
                    ['min' => 0, 'max' => 25000, 'rate' => 0, 'deduction' => 0],
                    ['min' => 25000, 'max' => 45583, 'rate' => 0.10, 'deduction' => 2500],
                    ['min' => 45583, 'max' => 81583, 'rate' => 0.15, 'deduction' => 4779],
                    ['min' => 81583, 'max' => 126583, 'rate' => 0.20, 'deduction' => 8858],
                    ['min' => 126583, 'max' => 220333, 'rate' => 0.25, 'deduction' => 15187],
                    ['min' => 220333, 'max' => 389083, 'rate' => 0.35, 'deduction' => 37220],
                    ['min' => 389083, 'max' => 842166, 'rate' => 0.45, 'deduction' => 76128],
                    ['min' => 842166, 'max' => null, 'rate' => 0.60, 'deduction' => 202553],
                ],
                'is_deductible' => false,
                'display_order' => 30,
                'pdf_code' => '390',
            ],

            // === COTISATIONS SOCIALES ===
            [
                'rule_type' => 'contribution',
                'rule_category' => 'employee',
                'code' => 'CNPS',
                'label' => 'CNPS Employé (Retraite)',
                'description' => 'Cotisation retraite 5,4% sur brut plafonné',
                'calculation_type' => 'percentage',
                'rate' => 5.4,
                'base_field' => 'gross_salary',
                'ceiling' => 1647315, // Plafond mensuel
                'is_deductible' => true,
                'display_order' => 40,
                'pdf_code' => '565',
            ],
            [
                'rule_type' => 'contribution',
                'rule_category' => 'employer',
                'code' => 'CNPS_PF',
                'label' => 'CNPS Prestations Familiales',
                'description' => 'Charge patronale 5,75% sur brut plafonné à 70 000',
                'calculation_type' => 'percentage',
                'rate' => 5.75,
                'base_field' => 'gross_salary',
                'ceiling' => 70000,
                'is_visible_on_payslip' => false,
                'display_order' => 50,
                'pdf_code' => '520',
            ],
            [
                'rule_type' => 'contribution',
                'rule_category' => 'employer',
                'code' => 'CNPS_AT',
                'label' => 'CNPS Accident du Travail',
                'description' => 'Charge patronale 2-5% sur brut plafonné à 70 000',
                'calculation_type' => 'percentage',
                'rate' => 5.0, // Variable selon secteur
                'base_field' => 'gross_salary',
                'ceiling' => 70000,
                'is_visible_on_payslip' => false,
                'display_order' => 51,
                'pdf_code' => '521',
            ],
            [
                'rule_type' => 'contribution',
                'rule_category' => 'employer',
                'code' => 'CNPS_RET',
                'label' => 'CNPS Retraite Employeur',
                'description' => 'Charge patronale 7,7% sur brut plafonné',
                'calculation_type' => 'percentage',
                'rate' => 7.7,
                'base_field' => 'gross_salary',
                'ceiling' => 1647315,
                'is_visible_on_payslip' => false,
                'display_order' => 52,
                'pdf_code' => '522',
            ],
        ];

        foreach ($rules as $rule) {
            PayrollCountryRule::updateOrCreate(
                ['country_id' => $country->id, 'code' => $rule['code']],
                array_merge($rule, ['country_id' => $country->id])
            );
        }
    }

    protected function createFields(PayrollCountry $country): void
    {
        $fields = [
            // Section: Indemnités
            [
                'field_name' => 'transport_allowance',
                'field_label' => 'Indemnité de Transport',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'placeholder' => 'Ex: 25000',
                'help_text' => 'Non imposable jusqu\'à 25 000 FCFA',
                'section' => 'allowances',
                'is_taxable' => false,
                'affects_gross' => false, // Ajouté au net après calcul
                'display_order' => 10,
            ],
            [
                'field_name' => 'housing_allowance',
                'field_label' => 'Indemnité de Logement',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'placeholder' => 'Ex: 50000',
                'section' => 'allowances',
                'is_taxable' => true,
                'affects_gross' => true,
                'display_order' => 20,
            ],
            [
                'field_name' => 'bonuses',
                'field_label' => 'Primes et Bonus',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'placeholder' => 'Ex: 15000',
                'section' => 'allowances',
                'is_taxable' => true,
                'affects_gross' => true,
                'display_order' => 30,
            ],
            [
                'field_name' => 'overtime_amount',
                'field_label' => 'Heures Supplémentaires',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'placeholder' => 'Montant total',
                'help_text' => 'HS 15%, 50%, 75%, 100% selon les heures',
                'section' => 'allowances',
                'is_taxable' => true,
                'affects_gross' => true,
                'display_order' => 40,
            ],

            // Section: Retenues diverses
            [
                'field_name' => 'advance_payment',
                'field_label' => 'Acompte',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'section' => 'deductions',
                'is_taxable' => false,
                'affects_gross' => false,
                'display_order' => 50,
            ],
            [
                'field_name' => 'loan_repayment',
                'field_label' => 'Remboursement Prêt',
                'field_type' => 'number',
                'is_required' => false,
                'default_value' => '0',
                'section' => 'deductions',
                'is_taxable' => false,
                'affects_gross' => false,
                'display_order' => 60,
            ],
        ];

        foreach ($fields as $field) {
            PayrollCountryField::updateOrCreate(
                ['country_id' => $country->id, 'field_name' => $field['field_name']],
                array_merge($field, ['country_id' => $country->id])
            );
        }
    }
}

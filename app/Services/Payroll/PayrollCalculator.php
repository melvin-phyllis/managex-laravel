<?php

namespace App\Services\Payroll;

use App\Models\User;
use App\Models\Payroll;
use App\Models\PayrollCountry;
use App\Models\PayrollCountryRule;

class PayrollCalculator
{
    protected PayrollCountry $country;
    protected array $rules = [];
    protected float $fiscalParts = 1;

    /**
     * Initialise le calculateur avec un pays
     */
    public function forCountry(PayrollCountry $country): self
    {
        $this->country = $country;
        $this->rules = $country->rules()->orderBy('display_order')->get()->keyBy('code')->toArray();
        return $this;
    }

    /**
     * Définit le nombre de parts fiscales
     */
    public function withParts(float $parts): self
    {
        $this->fiscalParts = max(1, $parts);
        return $this;
    }

    /**
     * Calcule le nombre de parts automatiquement depuis l'employé
     */
    public function withPartsFromUser(User $user): self
    {
        if ($user->number_of_parts) {
            $this->fiscalParts = $user->number_of_parts;
        } else {
            $parts = ($user->marital_status === 'married') ? 2.0 : 1.0;
            $childrenParts = ($user->children_count ?? 0) * 0.5;
            $this->fiscalParts = min($parts + $childrenParts, 5.0);
        }
        return $this;
    }

    /**
     * Calcule tous les éléments de paie
     */
    public function calculate(array $inputs): array
    {
        // Extraire les inputs
        $baseSalary = $inputs['base_salary'] ?? 0;
        $housingAllowance = $inputs['housing_allowance'] ?? 0;
        $bonuses = $inputs['bonuses'] ?? 0;
        $overtimeAmount = $inputs['overtime_amount'] ?? 0;
        $otherAllowances = $inputs['other_allowances'] ?? 0;
        $transportAllowance = $inputs['transport_allowance'] ?? 0;
        $advancePayment = $inputs['advance_payment'] ?? 0;
        $loanRepayment = $inputs['loan_repayment'] ?? 0;

        // 1. Brut Imposable (HORS transport)
        $taxableGross = $baseSalary + $housingAllowance + $bonuses + $overtimeAmount + $otherAllowances;

        // 2. Calcul des taxes et cotisations employé
        $taxes = $this->calculateEmployeeTaxes($taxableGross);

        // 3. Total retenues fiscales/sociales
        $totalTaxDeductions = $taxes['is'] + $taxes['cn'] + $taxes['igr'] + $taxes['cnps'];

        // 4. Total Net (avant ajouts non imposables)
        $totalNet = $taxableGross - $totalTaxDeductions;

        // 5. Retenues diverses (non fiscales)
        $otherDeductions = $advancePayment + $loanRepayment;

        // 6. Net à Payer = Net + Transport - Retenues diverses
        $netSalary = $totalNet + $transportAllowance - $otherDeductions;

        // 7. Charges patronales (pour info)
        $employerCharges = $this->calculateEmployerCharges($taxableGross);

        return [
            // Bases
            'gross_salary' => $taxableGross,
            'taxable_gross' => $taxableGross,
            'transport_allowance' => $transportAllowance,
            'housing_allowance' => $housingAllowance,
            'bonuses' => $bonuses,
            'overtime_amount' => $overtimeAmount,
            'other_allowances' => $otherAllowances,

            // Taxes employé
            'tax_is' => $taxes['is'],
            'tax_cn' => $taxes['cn'],
            'tax_igr' => $taxes['igr'],
            'cnps_employee' => $taxes['cnps'],

            // Totaux
            'total_deductions' => $totalTaxDeductions + $otherDeductions,
            'net_salary' => $netSalary,
            'fiscal_parts' => $this->fiscalParts,

            // Détails pour le PDF
            'breakdown' => [
                'tax_deductions' => $totalTaxDeductions,
                'other_deductions' => $otherDeductions,
                'total_net' => $totalNet,
                'advance_payment' => $advancePayment,
                'loan_repayment' => $loanRepayment,
            ],

            // Charges patronales
            'employer_charges' => $employerCharges,
        ];
    }

    /**
     * Calcule les taxes et cotisations employé
     */
    protected function calculateEmployeeTaxes(float $taxableGross): array
    {
        // IS (1.2%)
        $is = $this->applyRule('IS', $taxableGross);

        // CN (barème progressif)
        $cn = $this->applyRule('CN', $taxableGross);

        // CNPS (5.4% plafonné)
        $cnps = $this->applyRule('CNPS', $taxableGross);

        // IGR (base = taxableGross - IS - CN - CNPS, divisé par parts)
        $igrBase = $taxableGross - $is - $cn - $cnps;
        $igr = $this->calculateIGR($igrBase, $this->fiscalParts);

        return [
            'is' => $is,
            'cn' => $cn,
            'igr' => $igr,
            'cnps' => $cnps,
        ];
    }

    /**
     * Calcule l'IGR avec nombre de parts
     */
    protected function calculateIGR(float $base, float $parts): float
    {
        if ($parts <= 0) $parts = 1;

        $rule = $this->rules['IGR'] ?? null;
        if (!$rule || !isset($rule['brackets'])) {
            return 0;
        }

        $quotient = $base / $parts;
        $taxPerPart = 0;

        foreach ($rule['brackets'] as $bracket) {
            $min = $bracket['min'] ?? 0;
            $max = $bracket['max'] ?? PHP_INT_MAX;
            $rate = $bracket['rate'] ?? 0;
            $deduction = $bracket['deduction'] ?? 0;

            if ($quotient > $min && ($quotient <= $max || $max === null)) {
                $taxPerPart = ($quotient * $rate) - $deduction;
                break;
            }
        }

        return floor(max(0, $taxPerPart * $parts));
    }

    /**
     * Calcule les charges patronales
     */
    protected function calculateEmployerCharges(float $grossSalary): array
    {
        return [
            'cnps_pf' => $this->applyRule('CNPS_PF', $grossSalary),
            'cnps_at' => $this->applyRule('CNPS_AT', $grossSalary),
            'cnps_ret' => $this->applyRule('CNPS_RET', $grossSalary),
        ];
    }

    /**
     * Applique une règle de calcul
     */
    protected function applyRule(string $code, float $base): float
    {
        $rule = $this->rules[$code] ?? null;
        if (!$rule) {
            return 0;
        }

        // Appliquer plafond
        $effectiveBase = $base;
        if (isset($rule['ceiling']) && $rule['ceiling'] && $effectiveBase > $rule['ceiling']) {
            $effectiveBase = $rule['ceiling'];
        }
        if (isset($rule['floor']) && $rule['floor'] && $effectiveBase < $rule['floor']) {
            $effectiveBase = $rule['floor'];
        }

        switch ($rule['calculation_type']) {
            case 'percentage':
                return floor($effectiveBase * ($rule['rate'] / 100));

            case 'fixed':
                return $rule['fixed_amount'] ?? 0;

            case 'bracket':
                return $this->calculateBracket($rule['brackets'] ?? [], $effectiveBase);

            default:
                return 0;
        }
    }

    /**
     * Calcul par tranches progressives (pour CN)
     */
    protected function calculateBracket(array $brackets, float $base): float
    {
        $total = 0;

        foreach ($brackets as $bracket) {
            $min = $bracket['min'] ?? 0;
            $max = $bracket['max'] ?? $base;
            $rate = $bracket['rate'] ?? 0;

            // Si c'est un bracket avec formule directe (pour IGR), géré ailleurs
            if (isset($bracket['deduction'])) {
                continue;
            }

            if ($base > $min) {
                $taxableInBracket = min($base, $max ?? $base) - $min;
                $total += $taxableInBracket * $rate;
            }
        }

        return floor($total);
    }

    /**
     * Récupère les règles chargées
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}

<?php

namespace App\Services\Payroll;

use App\Models\User;
use App\Models\Payroll;
use App\Models\Contract;
use App\Models\Setting;
use App\Models\PayrollCountry;
use App\Services\Payroll\PayrollCalculator;

class PayrollService
{
    protected PayrollCalculator $calculator;
    protected ?PayrollCountry $country = null;

    public function __construct()
    {
        $this->calculator = new PayrollCalculator();
    }

    /**
     * Définir le pays à utiliser (ou utiliser celui par défaut)
     */
    public function forCountry(?PayrollCountry $country = null): self
    {
        $this->country = $country ?? Setting::getDefaultPayrollCountry();
        
        if ($this->country) {
            $this->calculator->forCountry($this->country);
        }
        
        return $this;
    }

    /**
     * Orchestration du calcul de la paie
     */
    public function calculatePayroll(User $user, int $month, int $year, array $options = []): Payroll
    {
        // S'assurer qu'on a un pays configuré
        if (!$this->country) {
            $this->forCountry();
        }

        // 1. Récupérer le contrat
        $contract = $user->currentContract;
        if (!$contract) {
            throw new \Exception("Aucun contrat actif pour l'employé {$user->name}.");
        }

        // 2. Préparer les inputs pour le calculateur
        $baseSalary = $contract->base_salary;
        
        $inputs = [
            'base_salary' => $baseSalary,
            'transport_allowance' => $options['transport_allowance'] ?? 0,
            'housing_allowance' => $options['housing_allowance'] ?? 0,
            'other_allowances' => $options['other_allowances'] ?? 0,
            'overtime_amount' => $options['overtime_amount'] ?? 0,
            'bonuses' => $options['bonuses'] ?? 0,
            'advance_payment' => $options['advance_payment'] ?? 0,
            'loan_repayment' => $options['loan_repayment'] ?? 0,
        ];

        // 3. Configurer le nombre de parts fiscales
        $this->calculator->withPartsFromUser($user);

        // 4. Effectuer le calcul dynamique
        if ($this->country) {
            $result = $this->calculator->calculate($inputs);
        } else {
            // Fallback: calcul manuel si pas de pays configuré
            $result = $this->calculateManually($user, $inputs);
        }

        // 5. Créer ou mettre à jour le Payroll
        $payroll = Payroll::firstOrNew([
            'user_id' => $user->id,
            'mois' => $month,
            'annee' => $year,
        ]);

        $payroll->fill([
            'user_id' => $user->id,
            'contract_id' => $contract->id,
            'country_id' => $this->country?->id,
            'mois' => $month,
            'annee' => $year,
            'statut' => 'pending',
            'workflow_status' => 'draft',
            
            'gross_salary' => $result['gross_salary'],
            'transport_allowance' => $result['transport_allowance'],
            'housing_allowance' => $result['housing_allowance'] ?? 0,
            'other_allowances' => $inputs['other_allowances'],
            'overtime_amount' => $result['overtime_amount'] ?? 0,
            'bonuses' => $result['bonuses'] ?? 0,
            
            'taxable_gross' => $result['taxable_gross'],
            
            'tax_is' => $result['tax_is'],
            'tax_cn' => $result['tax_cn'],
            'tax_igr' => $result['tax_igr'],
            'cnps_employee' => $result['cnps_employee'],
            'cnps_employer' => array_sum($result['employer_charges'] ?? []),
            
            'total_deductions' => $result['total_deductions'],
            'net_salary' => $result['net_salary'],
            
            'fiscal_parts' => $result['fiscal_parts'],
        ]);
        
        $payroll->save();
        
        // 6. Sync Items (Details)
        $this->syncPayrollItems($payroll, $baseSalary, $inputs, $result);

        return $payroll;
    }

    /**
     * Synchroniser les items de paie
     */
    protected function syncPayrollItems(Payroll $payroll, float $baseSalary, array $inputs, array $result): void
    {
        $payroll->items()->delete();
        
        // Gains
        $this->addItem($payroll, 'earning', 'BASE', 'Salaire de Base', $baseSalary);
        
        if (($inputs['transport_allowance'] ?? 0) > 0) {
            $this->addItem($payroll, 'earning', 'TRANSPORT', 'Indemnité de Transport', $inputs['transport_allowance'], false);
        }
        if (($inputs['housing_allowance'] ?? 0) > 0) {
            $this->addItem($payroll, 'earning', 'LOGT', 'Indemnité de Logement', $inputs['housing_allowance']);
        }
        if (($inputs['overtime_amount'] ?? 0) > 0) {
            $this->addItem($payroll, 'earning', 'HSUP', 'Heures Supplémentaires', $inputs['overtime_amount']);
        }
        if (($inputs['bonuses'] ?? 0) > 0) {
            $this->addItem($payroll, 'earning', 'PRIME', 'Primes et Bonus', $inputs['bonuses']);
        }
        
        // Retenues fiscales
        if ($result['cnps_employee'] > 0) {
            $this->addItem($payroll, 'deduction', 'CNPS', 'CNPS (Retraite)', $result['cnps_employee']);
        }
        if ($result['tax_is'] > 0) {
            $this->addItem($payroll, 'deduction', 'IS', 'Impôt sur Salaire (IS)', $result['tax_is']);
        }
        if ($result['tax_cn'] > 0) {
            $this->addItem($payroll, 'deduction', 'CN', 'Contribution Nationale (CN)', $result['tax_cn']);
        }
        if ($result['tax_igr'] > 0) {
            $this->addItem($payroll, 'deduction', 'IGR', 'Impôt Général sur le Revenu (IGR)', $result['tax_igr']);
        }
        
        // Retenues diverses
        if (($inputs['advance_payment'] ?? 0) > 0) {
            $this->addItem($payroll, 'deduction', 'ACOMPTE', 'Acompte', $inputs['advance_payment']);
        }
        if (($inputs['loan_repayment'] ?? 0) > 0) {
            $this->addItem($payroll, 'deduction', 'PRET', 'Remboursement Prêt', $inputs['loan_repayment']);
        }
    }

    /**
     * Calcul manuel (fallback si pas de pays configuré)
     * Utilise les constantes CIV par défaut
     */
    protected function calculateManually(User $user, array $inputs): array
    {
        $baseSalary = $inputs['base_salary'] ?? 0;
        $transportAllowance = $inputs['transport_allowance'] ?? 0;
        $housingAllowance = $inputs['housing_allowance'] ?? 0;
        $bonuses = $inputs['bonuses'] ?? 0;
        $overtimeAmount = $inputs['overtime_amount'] ?? 0;
        $otherAllowances = $inputs['other_allowances'] ?? 0;

        $taxableGross = $baseSalary + $housingAllowance + $bonuses + $overtimeAmount + $otherAllowances;

        // Constantes CIV
        $cnpsCeiling = 1647315;
        $cnpsRate = 0.054;
        $isRate = 0.012;

        $cnps = floor(min($taxableGross, $cnpsCeiling) * $cnpsRate);
        $is = floor($taxableGross * $isRate);
        $cn = $this->calculateCNManual($taxableGross);
        
        $igrBase = $taxableGross - $is - $cn - $cnps;
        $parts = $this->calculateFiscalParts($user);
        $igr = $this->calculateIGRManual($igrBase, $parts);

        $totalDeductions = $is + $cn + $igr + $cnps;
        $totalNet = $taxableGross - $totalDeductions;
        $netSalary = $totalNet + $transportAllowance;

        return [
            'gross_salary' => $taxableGross,
            'taxable_gross' => $taxableGross,
            'transport_allowance' => $transportAllowance,
            'housing_allowance' => $housingAllowance,
            'bonuses' => $bonuses,
            'overtime_amount' => $overtimeAmount,
            'tax_is' => $is,
            'tax_cn' => $cn,
            'tax_igr' => $igr,
            'cnps_employee' => $cnps,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'fiscal_parts' => $parts,
            'employer_charges' => [],
        ];
    }

    protected function calculateCNManual(float $taxableGross): float
    {
        $brackets = [
            ['min' => 0, 'max' => 50000, 'rate' => 0.00],
            ['min' => 50000, 'max' => 130000, 'rate' => 0.015],
            ['min' => 130000, 'max' => 200000, 'rate' => 0.05],
            ['min' => 200000, 'max' => null, 'rate' => 0.10],
        ];

        $tax = 0;
        foreach ($brackets as $bracket) {
            $min = $bracket['min'];
            $max = $bracket['max'] ?? $taxableGross;
            $rate = $bracket['rate'];

            if ($taxableGross > $min) {
                $base = min($taxableGross, $max) - $min;
                $tax += $base * $rate;
            }
        }
        return floor($tax);
    }

    protected function calculateIGRManual(float $igrBase, float $parts): float
    {
        if ($parts <= 0) $parts = 1;

        $table = [
            ['min' => 0, 'max' => 25000, 'rate' => 0.00, 'deduction' => 0],
            ['min' => 25000, 'max' => 45583, 'rate' => 0.10, 'deduction' => 2500],
            ['min' => 45583, 'max' => 81583, 'rate' => 0.15, 'deduction' => 4779],
            ['min' => 81583, 'max' => 126583, 'rate' => 0.20, 'deduction' => 8858],
            ['min' => 126583, 'max' => 220333, 'rate' => 0.25, 'deduction' => 15187],
            ['min' => 220333, 'max' => 389083, 'rate' => 0.35, 'deduction' => 37220],
            ['min' => 389083, 'max' => 842166, 'rate' => 0.45, 'deduction' => 76128],
            ['min' => 842166, 'max' => null, 'rate' => 0.60, 'deduction' => 202553],
        ];

        $Q = $igrBase / $parts;
        $taxPerPart = 0;
        
        foreach ($table as $bracket) {
            $min = $bracket['min'];
            $max = $bracket['max'];
            
            if ($Q > $min && ($max === null || $Q <= $max)) {
                $taxPerPart = ($Q * $bracket['rate']) - $bracket['deduction'];
                break;
            }
        }
        
        return floor(max(0, $taxPerPart * $parts));
    }

    /**
     * Calcul du nombre de parts fiscales
     */
    public function calculateFiscalParts(User $user): float
    {
        if ($user->number_of_parts) {
            return $user->number_of_parts;
        }

        $parts = ($user->marital_status === 'married') ? 2.0 : 1.0;
        $childrenParts = ($user->children_count ?? 0) * 0.5;
        
        return min($parts + $childrenParts, 5.0);
    }

    private function addItem(Payroll $payroll, string $type, string $code, string $label, float $amount, bool $isTaxable = true): void
    {
        $payroll->items()->create([
            'type' => $type,
            'code' => $code,
            'label' => $label,
            'amount' => $amount,
            'is_taxable' => ($type === 'earning' && $isTaxable),
        ]);
    }
}

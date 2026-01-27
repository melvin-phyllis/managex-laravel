<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollCountryRule extends Model
{
    protected $fillable = [
        'country_id',
        'rule_type',
        'rule_category',
        'code',
        'label',
        'description',
        'calculation_type',
        'rate',
        'fixed_amount',
        'brackets',
        'formula',
        'base_field',
        'ceiling',
        'floor',
        'is_taxable',
        'is_deductible',
        'is_mandatory',
        'is_visible_on_payslip',
        'display_order',
        'pdf_code',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'fixed_amount' => 'decimal:2',
        'brackets' => 'array',
        'ceiling' => 'decimal:2',
        'floor' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_deductible' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_visible_on_payslip' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Pays associé
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(PayrollCountry::class, 'country_id');
    }

    /**
     * Calcule le montant selon la règle
     */
    public function calculate(float $base): float
    {
        // Appliquer plafond/plancher
        $effectiveBase = $base;
        
        if ($this->ceiling && $effectiveBase > $this->ceiling) {
            $effectiveBase = $this->ceiling;
        }
        
        if ($this->floor && $effectiveBase < $this->floor) {
            $effectiveBase = $this->floor;
        }

        switch ($this->calculation_type) {
            case 'percentage':
                return floor($effectiveBase * ($this->rate / 100));
                
            case 'fixed':
                return $this->fixed_amount ?? 0;
                
            case 'bracket':
                return $this->calculateBracket($effectiveBase);
                
            case 'formula':
                // Évaluation de formule (sécurisée) - TODO
                return 0;
                
            default:
                return 0;
        }
    }

    /**
     * Calcul par tranches progressives
     */
    protected function calculateBracket(float $base): float
    {
        if (!$this->brackets || !is_array($this->brackets)) {
            return 0;
        }

        $total = 0;
        
        foreach ($this->brackets as $bracket) {
            $min = $bracket['min'] ?? 0;
            $max = $bracket['max'] ?? $base;
            $rate = $bracket['rate'] ?? 0;
            $deduction = $bracket['deduction'] ?? null;

            if ($base > $min) {
                if ($deduction !== null) {
                    // Formule directe: (Q * rate) - deduction (pour IGR)
                    if ($base <= $max || $max === null) {
                        return floor(($base * $rate) - $deduction);
                    }
                } else {
                    // Calcul par tranche classique
                    $taxableInBracket = min($base, $max) - $min;
                    $total += $taxableInBracket * $rate;
                }
            }
        }

        return floor($total);
    }

    /**
     * Scope: Taxes
     */
    public function scopeTaxes($query)
    {
        return $query->where('rule_type', 'tax');
    }

    /**
     * Scope: Cotisations
     */
    public function scopeContributions($query)
    {
        return $query->where('rule_type', 'contribution');
    }

    /**
     * Scope: Retenues employé
     */
    public function scopeEmployee($query)
    {
        return $query->whereIn('rule_category', ['employee', 'both']);
    }

    /**
     * Scope: Charges employeur
     */
    public function scopeEmployer($query)
    {
        return $query->whereIn('rule_category', ['employer', 'both']);
    }
}

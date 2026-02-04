<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contract_id',
        'country_id',
        'template_id',
        'mois',
        'annee',
        'statut', // paid, pending
        'workflow_status', // draft, pending_review, validated, rejected
        'pdf_url',
        'notes',
        'custom_fields',

        // Revenus
        'gross_salary', // Salaire Brut (Base + Sursalaire + Primes fixes)
        'transport_allowance',
        'housing_allowance',
        'other_allowances',
        'overtime_amount',
        'bonuses',

        // Bases
        'taxable_gross', // Brut Imposable

        // Taxes CIV
        'tax_is', // Impôt sur Salaire (1.2%)
        'tax_cn', // Contribution Nationale
        'tax_igr', // Impôt Général sur le Revenu
        'cnps_employee', // Retraite (6.3%)
        'cnps_employer', // Patronal (pour info)

        // Totaux
        'total_deductions', // Total retenues
        'net_salary', // Net à payer

        // Metadata
        'fiscal_parts',
        'validated_at',
        'validated_by',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'taxable_gross' => 'decimal:2',
        'tax_is' => 'decimal:2',
        'tax_cn' => 'decimal:2',
        'tax_igr' => 'decimal:2',
        'cnps_employee' => 'decimal:2',
        'cnps_employer' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'fiscal_parts' => 'decimal:1',
        'custom_fields' => 'array',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this payroll
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contract associated with this payroll
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get detailed items (earnings/deductions)
     */
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class);
    }

    public function earnings()
    {
        return $this->items()->where('type', 'earning');
    }

    public function deductions()
    {
        return $this->items()->where('type', 'deduction');
    }

    /**
     * Scopes
     */
    public function scopePaid($query)
    {
        return $query->where('statut', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    /**
     * Accessors
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'paid' => 'Payé',
            'pending' => 'En attente',
            default => 'Inconnu',
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'paid' => 'green',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    public function getMoisLabelAttribute(): string
    {
        $mois = [
            '1' => 'Janvier', '2' => 'Février', '3' => 'Mars',
            '4' => 'Avril', '5' => 'Mai', '6' => 'Juin',
            '7' => 'Juillet', '8' => 'Août', '9' => 'Septembre',
            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
        ];

        return $mois[$this->mois] ?? $this->mois;
    }

    public function getPeriodeAttribute(): string
    {
        return $this->mois_label.' '.$this->annee;
    }

    // Format helpers
    public function getNetSalaryFormattedAttribute(): string
    {
        return number_format($this->net_salary, 0, ',', ' ').' FCFA';
    }

    public function getGrossSalaryFormattedAttribute(): string
    {
        return number_format($this->gross_salary, 0, ',', ' ').' FCFA';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mois',
        'annee',
        'montant',
        'statut',
        'pdf_url',
        'notes',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'annee' => 'integer',
    ];

    /**
     * Get the user that owns this payroll
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for paid payrolls
     */
    public function scopePaid($query)
    {
        return $query->where('statut', 'paid');
    }

    /**
     * Scope for pending payrolls
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    /**
     * Get status label in French
     */
    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'paid' => 'Payé',
            'pending' => 'En attente',
            default => 'Inconnu',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'paid' => 'green',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get month label in French
     */
    public function getMoisLabelAttribute(): string
    {
        $mois = [
            '01' => 'Janvier', '02' => 'Février', '03' => 'Mars',
            '04' => 'Avril', '05' => 'Mai', '06' => 'Juin',
            '07' => 'Juillet', '08' => 'Août', '09' => 'Septembre',
            '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre',
        ];
        return $mois[$this->mois] ?? $this->mois;
    }

    /**
     * Get formatted period (e.g., "Janvier 2024")
     */
    public function getPeriodeAttribute(): string
    {
        return $this->mois_label . ' ' . $this->annee;
    }

    /**
     * Get formatted amount
     */
    public function getMontantFormattedAttribute(): string
    {
        return number_format($this->montant, 2, ',', ' ') . ' €';
    }
}

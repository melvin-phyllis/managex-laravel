<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'date_debut',
        'date_fin',
        'motif',
        'statut',
        'commentaire_admin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Get the user that owns this leave
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending leaves
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    /**
     * Scope for approved leaves
     */
    public function scopeApproved($query)
    {
        return $query->where('statut', 'approved');
    }

    /**
     * Get the duration in days
     */
    public function getDureeAttribute(): int
    {
        return $this->date_debut->diffInDays($this->date_fin) + 1;
    }

    /**
     * Get type label in French
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'conge' => 'Congé annuel',
            'maladie' => 'Congé maladie',
            'autre' => 'Autre',
            default => 'Inconnu',
        };
    }

    /**
     * Get status label in French
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Refusé',
            default => 'Inconnu',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope for leaves active today
     */
    public function scopeCurrentlyActive($query)
    {
        return $query->where('statut', 'approved')
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now());
    }
}

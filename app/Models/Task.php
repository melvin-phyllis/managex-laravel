<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'progression',
        'statut',
        'date_debut',
        'date_fin',
        'priorite',
        'rating',
        'rating_comment',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'progression' => 'integer',
    ];

    /**
     * Get the user that owns this task
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(TaskDocument::class);
    }

    /**
     * Scope for pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }

    /**
     * Scope for approved tasks
     */
    public function scopeApproved($query)
    {
        return $query->where('statut', 'approved');
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('statut', 'completed');
    }

    /**
     * Get status color for UI
     */
    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'completed' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get priority color for UI
     */
    public function getPrioriteColorAttribute(): string
    {
        return match ($this->priorite) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in French
     */
    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'pending' => 'En attente',
            'approved' => 'Approuvée',
            'rejected' => 'Rejetée',
            'completed' => 'Terminée',
            default => 'Inconnu',
        };
    }

    /**
     * Get priority label in French
     */
    public function getPrioriteLabelAttribute(): string
    {
        return match ($this->priorite) {
            'low' => 'Basse',
            'medium' => 'Moyenne',
            'high' => 'Haute',
            default => 'Inconnue',
        };
    }
}

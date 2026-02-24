<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'type',
        'duration_hours',
        'instructor',
        'location',
        'max_participants',
        'status',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_hours' => 'decimal:1',
    ];

    public const CATEGORIES = [
        'technique' => 'Technique',
        'soft-skills' => 'Soft Skills',
        'reglementaire' => 'Réglementaire',
        'securite' => 'Sécurité',
        'management' => 'Management',
        'outils' => 'Outils & Logiciels',
    ];

    public const TYPES = [
        'interne' => 'Formation interne',
        'externe' => 'Formation externe',
        'en_ligne' => 'E-learning',
    ];

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TrainingParticipant::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    // Accessors
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category ?? '-';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'published' => 'Publié',
            'archived' => 'Archivé',
            default => 'Inconnu',
        };
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->participants()->where('status', '!=', 'cancelled')->count();
    }

    public function getSpotsLeftAttribute(): ?int
    {
        if (!$this->max_participants) {
            return null;
        }
        return max(0, $this->max_participants - $this->enrolled_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->max_participants && $this->enrolled_count >= $this->max_participants;
    }
}

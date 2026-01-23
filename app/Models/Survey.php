<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'titre',
        'description',
        'is_active',
        'date_limite',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_limite' => 'date',
    ];

    /**
     * Get the admin that created this survey
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the questions for this survey
     */
    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('ordre');
    }

    /**
     * Get all responses for this survey
     */
    public function responses(): HasMany
    {
        return $this->hasManyThrough(SurveyResponse::class, SurveyQuestion::class);
    }

    /**
     * Scope for active surveys
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a user has responded to this survey
     */
    public function hasUserResponded(User $user): bool
    {
        return $this->questions()
            ->whereHas('responses', fn($q) => $q->where('user_id', $user->id))
            ->exists();
    }

    /**
     * Get the number of respondents
     */
    public function getRespondentsCountAttribute(): int
    {
        return SurveyResponse::whereIn('survey_question_id', $this->questions->pluck('id'))
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Check if survey is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->date_limite && $this->date_limite->isPast();
    }
}

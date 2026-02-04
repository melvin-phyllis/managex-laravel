<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternEvaluation extends Model
{
    protected $fillable = [
        'intern_id',
        'tutor_id',
        'week_start',
        'discipline_score',
        'behavior_score',
        'skills_score',
        'communication_score',
        'discipline_comment',
        'behavior_comment',
        'skills_comment',
        'communication_comment',
        'general_comment',
        'objectives_next_week',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'week_start' => 'date',
        'submitted_at' => 'datetime',
        'discipline_score' => 'decimal:1',
        'behavior_score' => 'decimal:1',
        'skills_score' => 'decimal:1',
        'communication_score' => 'decimal:1',
    ];

    protected $appends = ['total_score', 'week_label', 'grade_letter'];

    /**
     * Criteria labels for display
     */
    public const CRITERIA = [
        'discipline' => [
            'label' => 'Discipline',
            'description' => 'Respect des horaires, assiduité, ponctualité',
            'max' => 2.5,
        ],
        'behavior' => [
            'label' => 'Comportement',
            'description' => 'Attitude professionnelle, relations avec l\'équipe',
            'max' => 2.5,
        ],
        'skills' => [
            'label' => 'Compétences techniques',
            'description' => 'Qualité du travail, progression, autonomie',
            'max' => 2.5,
        ],
        'communication' => [
            'label' => 'Communication',
            'description' => 'Clarté, écoute, reporting',
            'max' => 2.5,
        ],
    ];

    /**
     * Grade thresholds
     */
    public const GRADES = [
        'A' => ['min' => 9, 'label' => 'Excellent', 'color' => 'green'],
        'B' => ['min' => 7, 'label' => 'Bien', 'color' => 'blue'],
        'C' => ['min' => 5, 'label' => 'Satisfaisant', 'color' => 'yellow'],
        'D' => ['min' => 3, 'label' => 'À améliorer', 'color' => 'orange'],
        'E' => ['min' => 0, 'label' => 'Insuffisant', 'color' => 'red'],
    ];

    // ==========================================
    // Relations
    // ==========================================

    /**
     * Get the intern being evaluated
     */
    public function intern(): BelongsTo
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    /**
     * Get the tutor who made the evaluation
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * Calculate total score (sum of all criteria)
     */
    public function getTotalScoreAttribute(): float
    {
        return round(
            $this->discipline_score +
            $this->behavior_score +
            $this->skills_score +
            $this->communication_score,
            1
        );
    }

    /**
     * Get human-readable week label
     */
    public function getWeekLabelAttribute(): string
    {
        return 'Semaine du '.$this->week_start->format('d/m/Y');
    }

    /**
     * Get grade letter based on total score
     */
    public function getGradeLetterAttribute(): string
    {
        $score = $this->total_score;

        return match (true) {
            $score >= 9 => 'A',
            $score >= 7 => 'B',
            $score >= 5 => 'C',
            $score >= 3 => 'D',
            default => 'E',
        };
    }

    /**
     * Get grade info (label, color)
     */
    public function getGradeInfoAttribute(): array
    {
        return self::GRADES[$this->grade_letter];
    }

    /**
     * Get week end date (Sunday)
     */
    public function getWeekEndAttribute()
    {
        return $this->week_start->copy()->endOfWeek();
    }

    /**
     * Check if evaluation is submitted
     */
    public function getIsSubmittedAttribute(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Get scores as array for charts
     */
    public function getScoresArrayAttribute(): array
    {
        return [
            'discipline' => (float) $this->discipline_score,
            'behavior' => (float) $this->behavior_score,
            'skills' => (float) $this->skills_score,
            'communication' => (float) $this->communication_score,
        ];
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * Filter by specific week
     */
    public function scopeForWeek($query, $weekStart)
    {
        return $query->where('week_start', $weekStart);
    }

    /**
     * Filter by current week
     */
    public function scopeCurrentWeek($query)
    {
        return $query->where('week_start', now()->startOfWeek());
    }

    /**
     * Filter by last week
     */
    public function scopeLastWeek($query)
    {
        return $query->where('week_start', now()->subWeek()->startOfWeek());
    }

    /**
     * Filter submitted evaluations only
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Filter draft evaluations only
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Filter by tutor
     */
    public function scopeByTutor($query, $tutorId)
    {
        return $query->where('tutor_id', $tutorId);
    }

    /**
     * Filter by intern
     */
    public function scopeByIntern($query, $internId)
    {
        return $query->where('intern_id', $internId);
    }

    /**
     * Filter by date range
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('week_start', [$startDate, $endDate]);
    }

    // ==========================================
    // Methods
    // ==========================================

    /**
     * Submit the evaluation
     */
    public function submit(): bool
    {
        if ($this->status === 'submitted') {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return true;
    }

    /**
     * Check if evaluation can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get score percentage for a criterion
     */
    public function getScorePercentage(string $criterion): float
    {
        $score = $this->{$criterion.'_score'} ?? 0;
        $max = self::CRITERIA[$criterion]['max'] ?? 2.5;

        return round(($score / $max) * 100, 1);
    }

    /**
     * Get total score percentage
     */
    public function getTotalScorePercentageAttribute(): float
    {
        return round(($this->total_score / 10) * 100, 1);
    }

    /**
     * Get the Monday of the current week
     */
    public static function getCurrentWeekStart()
    {
        return now()->startOfWeek();
    }

    /**
     * Check if an evaluation exists for intern + week
     */
    public static function existsForInternAndWeek($internId, $weekStart): bool
    {
        return self::where('intern_id', $internId)
            ->where('week_start', $weekStart)
            ->exists();
    }

    /**
     * Get or create draft evaluation for intern + week
     */
    public static function getOrCreateDraft($internId, $tutorId, $weekStart = null): self
    {
        $weekStart = $weekStart ?? self::getCurrentWeekStart();

        return self::firstOrCreate(
            [
                'intern_id' => $internId,
                'week_start' => $weekStart,
            ],
            [
                'tutor_id' => $tutorId,
                'status' => 'draft',
            ]
        );
    }
}

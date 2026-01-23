<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'options',
        'is_required',
        'ordre',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'ordre' => 'integer',
    ];

    /**
     * Get the survey that owns this question
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the responses for this question
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Get type label in French
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'text' => 'Texte libre',
            'choice' => 'Choix multiples',
            'rating' => 'Évaluation (1-5)',
            'yesno' => 'Oui/Non',
            default => 'Inconnu',
        };
    }

    /**
     * Get statistics for this question
     */
    public function getStatisticsAttribute(): array
    {
        $responses = $this->responses;

        if ($responses->isEmpty()) {
            return ['total' => 0, 'data' => []];
        }

        $total = $responses->count();

        if ($this->type === 'rating') {
            $avg = $responses->avg('reponse');
            $distribution = $responses->groupBy('reponse')
                ->map(fn($group) => $group->count());
            return [
                'total' => $total,
                'average' => round($avg, 1),
                'distribution' => $distribution,
            ];
        }

        if ($this->type === 'choice' || $this->type === 'yesno') {
            $distribution = $responses->groupBy('reponse')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $total) * 100, 1),
                ]);
            return [
                'total' => $total,
                'distribution' => $distribution,
            ];
        }

        return ['total' => $total, 'responses' => $responses->pluck('reponse')];
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BtsEvaluation extends Model
{
    protected $fillable = [
        'intern_id',
        'evaluator_id',
        'intern_bts_number',
        'intern_field',
        'stage_start_date',
        'stage_end_date',
        // Scores
        'assiduity_score',
        'assiduity_details',
        'relations_score',
        'relations_teamwork',
        'relations_hierarchy',
        'relations_courtesy',
        'relations_listening',
        'execution_score',
        'execution_details',
        'initiative_score',
        'initiative_details',
        'presentation_score',
        'presentation_dress',
        'presentation_oral',
        'presentation_attitude',
        'total_score',
        'appreciation',
        'justification_report',
        'status',
        'submitted_at',
        'signed_at',
    ];

    protected $casts = [
        'stage_start_date' => 'date',
        'stage_end_date' => 'date',
        'submitted_at' => 'datetime',
        'signed_at' => 'datetime',
        'assiduity_score' => 'decimal:1',
        'relations_score' => 'decimal:1',
        'execution_score' => 'decimal:1',
        'initiative_score' => 'decimal:1',
        'presentation_score' => 'decimal:1',
        'total_score' => 'decimal:1',
        'relations_teamwork' => 'boolean',
        'relations_hierarchy' => 'boolean',
        'relations_courtesy' => 'boolean',
        'relations_listening' => 'boolean',
        'presentation_dress' => 'boolean',
        'presentation_oral' => 'boolean',
        'presentation_attitude' => 'boolean',
    ];

    // ===== CRITÈRES OFFICIELS BTS =====

    public const CRITERIA = [
        'assiduity' => [
            'label' => 'Assiduité et ponctualité',
            'max' => 3,
            'type' => 'auto',
            'source' => 'Présences',
        ],
        'relations' => [
            'label' => 'Relations humaines et professionnelles',
            'max' => 4,
            'type' => 'manual',
            'source' => 'Sous-critères',
        ],
        'execution' => [
            'label' => "Intelligence d'exécution des tâches",
            'max' => 6,
            'type' => 'auto',
            'source' => 'Tâches',
        ],
        'initiative' => [
            'label' => "Esprit d'initiative",
            'max' => 4,
            'type' => 'semi-auto',
            'source' => 'Tâches',
        ],
        'presentation' => [
            'label' => 'Présentation',
            'max' => 3,
            'type' => 'auto',
            'source' => 'Tâches (notes)',
        ],
    ];

    public const RELATIONS_SUBCRITERIA = [
        'relations_teamwork' => "S'intègre bien dans l'équipe",
        'relations_hierarchy' => 'Respecte la hiérarchie et les collègues',
        'relations_courtesy' => 'Fait preuve de courtoisie et savoir-être',
        'relations_listening' => 'Écoute et accepte les remarques',
    ];

    // Présentation is now auto-calculated from task ratings

    public const MAX_TOTAL = 20;
    public const JUSTIFICATION_THRESHOLD = 16;

    // ===== RELATIONS =====

    public function intern(): BelongsTo
    {
        return $this->belongsTo(User::class, 'intern_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // ===== ACCESSORS =====

    public function getRequiresJustificationAttribute(): bool
    {
        return $this->total_score > self::JUSTIFICATION_THRESHOLD;
    }

    public function getStageDurationAttribute(): string
    {
        if (!$this->stage_start_date || !$this->stage_end_date) return 'N/A';
        $months = $this->stage_start_date->diffInMonths($this->stage_end_date);
        return $months . ' mois';
    }

    public function getGradeInfoAttribute(): array
    {
        $score = $this->total_score;
        return match (true) {
            $score >= 18 => ['label' => 'Excellent', 'color' => '#16a34a', 'bg' => '#dcfce7', 'letter' => 'A'],
            $score >= 16 => ['label' => 'Très Bien', 'color' => '#059669', 'bg' => '#d1fae5', 'letter' => 'B'],
            $score >= 14 => ['label' => 'Bien', 'color' => '#2563eb', 'bg' => '#dbeafe', 'letter' => 'C'],
            $score >= 12 => ['label' => 'Assez Bien', 'color' => '#ca8a04', 'bg' => '#fef9c3', 'letter' => 'D'],
            $score >= 10 => ['label' => 'Passable', 'color' => '#ea580c', 'bg' => '#ffedd5', 'letter' => 'E'],
            default => ['label' => 'Insuffisant', 'color' => '#dc2626', 'bg' => '#fee2e2', 'letter' => 'F'],
        };
    }

    // ===== AUTO-CALCULATION METHODS =====

    /**
     * Calculate assiduity score (/3) from attendance data
     */
    public static function calculateAssiduity(User $intern, Carbon $start, Carbon $end): array
    {
        $presences = Presence::where('user_id', $intern->id)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->get();

        // Calculate working days in period (Mon-Fri)
        $workingDays = 0;
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->isWeekday()) $workingDays++;
            $current->addDay();
        }

        if ($workingDays === 0) {
            return ['score' => 0, 'details' => 'Aucun jour ouvré dans la période.'];
        }

        $daysPresent = $presences->where('is_absent', false)->count();
        $daysLate = $presences->where('is_late', true)->count();
        $totalLateMinutes = $presences->sum('late_minutes');

        // Presence rate (max 2 pts)
        $presenceRate = $daysPresent / $workingDays;
        $presenceScore = round($presenceRate * 2, 1);

        // Punctuality (max 1 pt) — deduct for late arrivals
        $lateRate = $workingDays > 0 ? $daysLate / $workingDays : 0;
        $punctualityScore = round(max(0, 1 - $lateRate), 1);

        $score = min(3, $presenceScore + $punctualityScore);

        $details = "{$daysPresent}/{$workingDays} jours présent (" . round($presenceRate * 100) . "%), "
            . "{$daysLate} retard(s), {$totalLateMinutes} min de retard cumulées.";

        return ['score' => $score, 'details' => $details];
    }

    /**
     * Calculate execution score (/6) from tasks
     */
    public static function calculateExecution(User $intern, Carbon $start, Carbon $end): array
    {
        $tasks = Task::where('user_id', $intern->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('date_debut', [$start, $end])
                    ->orWhereBetween('date_fin', [$start, $end]);
            })->get();

        $total = $tasks->count();

        if ($total === 0) {
            return ['score' => 0, 'details' => 'Aucune tâche assignée durant le stage.'];
        }

        // 1. Tasks completed (/2)
        $completed = $tasks->where('statut', 'completed')->count();
        $completionRate = $completed / $total;
        $completionScore = round($completionRate * 2, 1);

        // 2. On-time completion (/2)
        $onTime = $tasks->where('statut', 'completed')
            ->filter(fn($t) => $t->date_fin && $t->updated_at && $t->updated_at->lte($t->date_fin->endOfDay()))
            ->count();
        $onTimeRate = $completed > 0 ? $onTime / $completed : 0;
        $onTimeScore = round($onTimeRate * 2, 1);

        // 3. Quality — not rejected (/2)
        $rejected = $tasks->where('statut', 'rejected')->count();
        $qualityRate = max(0, 1 - ($rejected / $total));
        $qualityScore = round($qualityRate * 2, 1);

        $score = min(6, $completionScore + $onTimeScore + $qualityScore);

        $details = "{$completed}/{$total} terminées (" . round($completionRate * 100) . "%), "
            . "{$onTime}/{$completed} dans les délais, "
            . "{$rejected} rejetée(s).";

        return ['score' => $score, 'details' => $details];
    }

    /**
     * Calculate initiative score (/4) from tasks
     */
    public static function calculateInitiative(User $intern, Carbon $start, Carbon $end): array
    {
        $tasks = Task::where('user_id', $intern->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('date_debut', [$start, $end])
                    ->orWhereBetween('date_fin', [$start, $end]);
            })->get();

        $completed = $tasks->where('statut', 'completed');
        $total = $completed->count();

        if ($total === 0) {
            return ['score' => 0, 'details' => 'Aucune tâche terminée pour évaluer l\'initiative.'];
        }

        // 1. Tasks finished early (/2)
        $earlyFinish = $completed->filter(fn($t) =>
            $t->date_fin && $t->updated_at && $t->updated_at->lt($t->date_fin)
        )->count();
        $earlyRate = $earlyFinish / $total;
        $earlyScore = round($earlyRate * 2, 1);

        // 2. High progression / proactiveness (/2)
        $highProgression = $completed->filter(fn($t) => $t->progression >= 100)->count();
        $progRate = $highProgression / $total;
        $progScore = round($progRate * 2, 1);

        $score = min(4, $earlyScore + $progScore);

        $details = "{$earlyFinish}/{$total} terminée(s) en avance, "
            . "{$highProgression}/{$total} à 100% de progression.";

        return ['score' => $score, 'details' => $details];
    }

    /**
     * Calculate relations score from sub-criteria checkboxes
     */
    public function calculateRelationsScore(): float
    {
        return ($this->relations_teamwork ? 1 : 0)
             + ($this->relations_hierarchy ? 1 : 0)
             + ($this->relations_courtesy ? 1 : 0)
             + ($this->relations_listening ? 1 : 0);
    }

    /**
     * Calculate presentation score (/3) from task ratings
     * Rating is typically 1-5 stars → mapped to /3
     */
    public static function calculatePresentation(User $intern, Carbon $start, Carbon $end): array
    {
        $tasks = Task::where('user_id', $intern->id)
            ->whereNotNull('presentation_rating')
            ->where('presentation_rating', '>', 0)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('date_debut', [$start, $end])
                    ->orWhereBetween('date_fin', [$start, $end]);
            })->get();

        $ratedCount = $tasks->count();

        if ($ratedCount === 0) {
            return ['score' => 0, 'details' => 'Aucune tâche notée en présentation durant le stage.'];
        }

        $avgRating = $tasks->avg('presentation_rating');
        // Map rating (1-5) to score (0-3)
        $score = round(($avgRating / 5) * 3, 1);
        $score = min(3, max(0, $score));

        $details = "{$ratedCount} tâche(s) notée(s) en présentation, moyenne : " . round($avgRating, 1) . "/5.";

        return ['score' => $score, 'details' => $details];
    }

    /**
     * Recalculate total score
     */
    public function recalculateTotal(): void
    {
        $this->total_score = round(
            $this->assiduity_score +
            $this->relations_score +
            $this->execution_score +
            $this->initiative_score +
            $this->presentation_score,
            1
        );
    }

    /**
     * Submit the evaluation
     */
    public function submit(): bool
    {
        if ($this->status === 'submitted') return false;

        // Validate justification requirement
        $this->recalculateTotal();
        if ($this->total_score > self::JUSTIFICATION_THRESHOLD && empty($this->justification_report)) {
            return false;
        }

        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return true;
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    // ===== SCOPES =====

    public function scopeByIntern($query, $internId)
    {
        return $query->where('intern_id', $internId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}

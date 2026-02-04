<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evaluated_by',
        'month',
        'year',
        'problem_solving',
        'objectives_respect',
        'work_under_pressure',
        'accountability',
        'total_score',
        'calculated_salary',
        'comments',
        'status',
        'validated_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'problem_solving' => 'decimal:1',
        'objectives_respect' => 'decimal:1',
        'work_under_pressure' => 'decimal:1',
        'accountability' => 'decimal:1',
        'total_score' => 'decimal:1',
        'calculated_salary' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    /**
     * Critères d'évaluation avec leurs maximums
     */
    public const CRITERIA = [
        'problem_solving' => [
            'label' => 'Capacité à résoudre les problèmes',
            'max' => 2,
            'description' => 'Aptitude à identifier et résoudre efficacement les problèmes',
        ],
        'objectives_respect' => [
            'label' => 'Respect des objectifs fixés',
            'max' => 0.5,
            'description' => 'Atteinte des objectifs dans les délais impartis',
        ],
        'work_under_pressure' => [
            'label' => 'Capacité à travailler sous pression',
            'max' => 1,
            'description' => 'Performance maintenue en situation de stress',
        ],
        'accountability' => [
            'label' => 'Capacité à rendre compte',
            'max' => 2,
            'description' => 'Communication claire et reporting régulier',
        ],
    ];

    /**
     * Score maximum possible
     */
    public const MAX_SCORE = 5.5;

    /**
     * Relation avec l'employé évalué
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'évaluateur (admin)
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Calculer le score total automatiquement
     */
    public function calculateTotalScore(): float
    {
        $total = $this->problem_solving
               + $this->objectives_respect
               + $this->work_under_pressure
               + $this->accountability;

        return min($total, self::MAX_SCORE);
    }

    /**
     * Calculer le salaire basé sur le score
     */
    public function calculateSalary(): float
    {
        $smic = Setting::getSmicAmount();
        $score = $this->calculateTotalScore();

        // Si score = 0, on garde le SMIC minimum
        $calculatedSalary = $score * $smic;

        return max($smic, $calculatedSalary);
    }

    /**
     * Mettre à jour les totaux avant sauvegarde
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $evaluation->total_score = $evaluation->calculateTotalScore();
            $evaluation->calculated_salary = $evaluation->calculateSalary();
        });
    }

    /**
     * Scope: évaluations pour un mois/année
     */
    public function scopeForPeriod($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope: évaluations validées uniquement
     */
    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    /**
     * Scope: exclure les stagiaires
     */
    public function scopeExcludeInterns($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('contract_type', '!=', 'stage');
        });
    }

    /**
     * Accesseurs
     */
    public function getPeriodeLabelAttribute(): string
    {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
            4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
            10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return ($mois[$this->month] ?? $this->month).' '.$this->year;
    }

    public function getScorePercentageAttribute(): float
    {
        return round(($this->total_score / self::MAX_SCORE) * 100, 1);
    }

    public function getCalculatedSalaryFormattedAttribute(): string
    {
        return number_format($this->calculated_salary, 0, ',', ' ').' FCFA';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'validated' => 'Validé',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'yellow',
            'validated' => 'green',
            default => 'gray',
        };
    }

    /**
     * Obtenir la note pour un critère spécifique sous forme de pourcentage
     */
    public function getCriteriaPercentage(string $criteria): float
    {
        $max = self::CRITERIA[$criteria]['max'] ?? 1;
        $value = $this->{$criteria} ?? 0;

        return round(($value / $max) * 100, 1);
    }

    /**
     * Vérifier si l'évaluation peut être modifiée
     */
    public function canBeEdited(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Valider l'évaluation
     */
    public function validate(): bool
    {
        $this->status = 'validated';
        $this->validated_at = now();

        return $this->save();
    }
}

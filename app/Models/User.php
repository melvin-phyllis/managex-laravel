<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'poste',
        'telephone',
        'avatar',
        'department_id',
        'position_id',
        'supervisor_id',
        // Champs RH personnels
        'date_of_birth',
        'gender',
        'address',
        'city',
        'postal_code',
        'country',
        // Contact d'urgence
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        // Fiscalité CIV
        'marital_status',
        'children_count',
        'number_of_parts',
        'cnps_number',
        // Informations professionnelles
        'hire_date',
        'contract_end_date',
        'contract_type',
        'base_salary',
        'employee_id',
        // Informations administratives
        'social_security_number',
        'bank_iban',
        'bank_bic',
        // Soldes de congés
        'leave_balance',
        'sick_leave_balance',
        'rtt_balance',
        // Statut
        'status',
        'notes',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'contract_end_date' => 'date',
            'base_salary' => 'decimal:2',
            'leave_balance' => 'decimal:2',
            'sick_leave_balance' => 'decimal:2',
            'rtt_balance' => 'decimal:2',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is employee
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Get user's presences
     */
    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /**
     * Get user's tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get user's leaves
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get user's payrolls
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get surveys created by admin
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class, 'admin_id');
    }

    /**
     * Get user's survey responses
     */
    public function surveyResponses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    /**
     * Get today's presence
     */
    public function todayPresence()
    {
        return $this->presences()->where('date', today())->first();
    }

    /**
     * Check if user has checked in today
     */
    public function hasCheckedInToday(): bool
    {
        return $this->todayPresence() !== null;
    }

    /**
     * Check if user has checked out today
     */
    public function hasCheckedOutToday(): bool
    {
        $presence = $this->todayPresence();
        return $presence && $presence->check_out !== null;
    }

    /**
     * Département de l'utilisateur
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Position/Poste de l'utilisateur
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Jours de travail de l'employé
     */
    public function workDays(): HasMany
    {
        return $this->hasMany(EmployeeWorkDay::class);
    }

    /**
     * Vérifier si aujourd'hui est un jour de travail pour l'employé
     */
    public function isWorkingDay(?int $dayOfWeek = null): bool
    {
        $dayOfWeek = $dayOfWeek ?? now()->dayOfWeekIso; // 1=Lundi, 7=Dimanche
        return $this->workDays()->where('day_of_week', $dayOfWeek)->exists();
    }

    /**
     * Obtenir les numéros des jours de travail
     */
    public function getWorkDayNumbers(): array
    {
        return $this->workDays()->pluck('day_of_week')->toArray();
    }

    /**
     * Obtenir les noms des jours de travail
     */
    public function getWorkDayNamesAttribute(): string
    {
        $days = $this->workDays()->orderBy('day_of_week')->get();
        return $days->map(fn($d) => EmployeeWorkDay::DAYS[$d->day_of_week] ?? '')->implode(', ');
    }

    /**
     * Nom complet du département et position
     */
    public function getDepartmentPositionAttribute(): string
    {
        $parts = [];
        if ($this->department) {
            $parts[] = $this->department->name;
        }
        if ($this->position) {
            $parts[] = $this->position->name;
        }
        return implode(' - ', $parts) ?: 'Non assigné';
    }

    // Analytics Scopes

    public function scopeHiredInPeriod($query, \Carbon\Carbon $start, \Carbon\Carbon $end)
    {
        return $query->whereBetween('hire_date', [$start, $end]);
    }

    public function scopeDepartedInPeriod($query, \Carbon\Carbon $start, \Carbon\Carbon $end)
    {
        return $query->whereBetween('contract_end_date', [$start, $end])
                     ->whereNotNull('contract_end_date');
    }

    public function scopeExpiringContracts($query, int $daysAhead = 30)
    {
        return $query->whereNotNull('contract_end_date')
                     ->where('contract_end_date', '<=', now()->addDays($daysAhead))
                     ->where('contract_end_date', '>=', now());
    }

    public function scopeUpcomingBirthdays($query, int $daysAhead = 7)
    {
        return $query->whereNotNull('date_of_birth')
            ->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') BETWEEN ? AND ?", [
                now()->format('m-d'),
                now()->addDays($daysAhead)->format('m-d')
            ]);
    }

    /**
     * Get user's contracts
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get user's current contract
     */
    public function currentContract()
    {
        return $this->hasOne(Contract::class)->where('is_current', true);
    }

    /**
     * Get current base salary
     */
    public function getCurrentBaseSalaryAttribute(): ?float
    {
        return $this->currentContract ? $this->currentContract->base_salary : $this->base_salary;
    }

    // ==========================================
    // Relations Superviseur / Stagiaires
    // ==========================================

    /**
     * Get the user's supervisor (tuteur)
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get users supervised by this user
     */
    public function supervisees(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Get intern evaluations received (as intern)
     */
    public function internEvaluations(): HasMany
    {
        return $this->hasMany(InternEvaluation::class, 'intern_id');
    }

    /**
     * Get evaluations given (as tutor)
     */
    public function givenEvaluations(): HasMany
    {
        return $this->hasMany(InternEvaluation::class, 'tutor_id');
    }

    /**
     * Scope: only interns (contract_type = stage)
     */
    public function scopeInterns($query)
    {
        return $query->where('contract_type', 'stage');
    }

    /**
     * Scope: users with a supervisor assigned
     */
    public function scopeWithSupervisor($query)
    {
        return $query->whereNotNull('supervisor_id');
    }

    /**
     * Check if user is an intern
     */
    public function isIntern(): bool
    {
        return $this->contract_type === 'stage';
    }

    /**
     * Check if user is a tutor (has interns to supervise)
     */
    public function isTutor(): bool
    {
        return $this->supervisees()->interns()->exists();
    }

    /**
     * Get the interns supervised by this user
     */
    public function getInternsAttribute()
    {
        return $this->supervisees()->interns()->get();
    }

    /**
     * Get the latest evaluation for this intern
     */
    public function getLatestEvaluationAttribute()
    {
        return $this->internEvaluations()->latest('week_start')->first();
    }

    /**
     * Get the average evaluation score
     */
    public function getAverageEvaluationScoreAttribute(): ?float
    {
        $evaluations = $this->internEvaluations()->submitted()->get();

        if ($evaluations->isEmpty()) {
            return null;
        }

        return round($evaluations->avg(function ($eval) {
            return $eval->discipline_score + $eval->behavior_score +
                   $eval->skills_score + $eval->communication_score;
        }), 1);
    }

    // =============================================
    // LATE HOURS RECOVERY TRACKING
    // =============================================

    /**
     * Get total late minutes (all time)
     */
    public function getTotalLateMinutesAttribute(): int
    {
        return (int) $this->presences()
            ->where('is_late', true)
            ->sum('late_minutes');
    }

    /**
     * Get total recovery minutes (all time)
     */
    public function getTotalRecoveryMinutesAttribute(): int
    {
        return (int) $this->presences()
            ->sum('recovery_minutes');
    }

    /**
     * Get late hours balance (minutes to recover)
     * Positive = needs to recover, Negative = has surplus
     */
    public function getLateBalanceMinutesAttribute(): int
    {
        return $this->total_late_minutes - $this->total_recovery_minutes;
    }

    /**
     * Get formatted late balance
     */
    public function getLateBalanceFormattedAttribute(): string
    {
        $balance = $this->late_balance_minutes;
        $absBalance = abs($balance);
        $hours = floor($absBalance / 60);
        $mins = $absBalance % 60;
        
        $formatted = $hours > 0 ? "{$hours}h" . ($mins > 0 ? sprintf('%02d', $mins) : '') : "{$mins} min";
        
        if ($balance > 0) {
            return "-{$formatted}"; // Deficit (needs to recover)
        } elseif ($balance < 0) {
            return "+{$formatted}"; // Surplus
        }
        return "0";
    }

    /**
     * Get late balance status
     */
    public function getLateBalanceStatusAttribute(): string
    {
        $balance = $this->late_balance_minutes;
        if ($balance > 30) return 'deficit'; // More than 30 min to recover
        if ($balance > 0) return 'warning'; // Small deficit
        return 'ok'; // No deficit or surplus
    }

    /**
     * Get monthly late minutes
     */
    public function getMonthlyLateMinutes(?int $month = null, ?int $year = null): int
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return (int) $this->presences()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('is_late', true)
            ->sum('late_minutes');
    }

    /**
     * Get monthly recovery minutes
     */
    public function getMonthlyRecoveryMinutes(?int $month = null, ?int $year = null): int
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return (int) $this->presences()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('recovery_minutes');
    }

    /**
     * Get monthly late balance
     */
    public function getMonthlyLateBalance(?int $month = null, ?int $year = null): int
    {
        return $this->getMonthlyLateMinutes($month, $year) - $this->getMonthlyRecoveryMinutes($month, $year);
    }

    /**
     * Get recovery stats for the current month
     */
    public function getRecoveryStatsAttribute(): array
    {
        $month = now()->month;
        $year = now()->year;

        $monthlyLate = $this->getMonthlyLateMinutes($month, $year);
        $monthlyRecovery = $this->getMonthlyRecoveryMinutes($month, $year);
        $monthlyBalance = $monthlyLate - $monthlyRecovery;

        return [
            'total_late' => $this->total_late_minutes,
            'total_recovery' => $this->total_recovery_minutes,
            'total_balance' => $this->late_balance_minutes,
            'monthly_late' => $monthlyLate,
            'monthly_recovery' => $monthlyRecovery,
            'monthly_balance' => $monthlyBalance,
            'status' => $this->late_balance_status,
        ];
    }

    /**
     * Check if user has late hours to recover
     */
    public function hasLateHoursToRecover(): bool
    {
        return $this->late_balance_minutes > 0;
    }

    // =============================================
    // LATE PENALTY ABSENCES
    // =============================================

    /**
     * Get user's penalty absences
     */
    public function latePenaltyAbsences(): HasMany
    {
        return $this->hasMany(LatePenaltyAbsence::class);
    }

    /**
     * Get total expired late minutes (not yet penalized)
     */
    public function getExpiredLateMinutesAttribute(): int
    {
        return (int) $this->presences()
            ->where('is_late_expired', true)
            ->sum('expired_late_minutes');
    }

    /**
     * Get late hours that are about to expire (within 2 days)
     */
    public function getExpiringLatePresences()
    {
        return $this->presences()
            ->expiringLate(2)
            ->orderBy('late_recovery_deadline')
            ->get();
    }

    /**
     * Get total minutes expiring soon
     */
    public function getExpiringLateMinutesAttribute(): int
    {
        return $this->presences()
            ->expiringLate(2)
            ->get()
            ->sum('unrecovered_minutes');
    }

    /**
     * Get unacknowledged penalty absences
     */
    public function getUnacknowledgedPenaltiesAttribute()
    {
        return $this->latePenaltyAbsences()
            ->unacknowledged()
            ->orderBy('absence_date')
            ->get();
    }

    /**
     * Get count of penalty absences
     */
    public function getPenaltyAbsencesCountAttribute(): int
    {
        return $this->latePenaltyAbsences()->count();
    }

    /**
     * Get upcoming penalty absences (future dates)
     */
    public function getUpcomingPenaltyAbsencesAttribute()
    {
        return $this->latePenaltyAbsences()
            ->where('absence_date', '>=', today())
            ->orderBy('absence_date')
            ->get();
    }

    /**
     * Get late recovery stats including expiration info
     */
    public function getFullRecoveryStatsAttribute(): array
    {
        $baseStats = $this->recovery_stats;
        $penaltyThreshold = Setting::getLatePenaltyThresholdMinutes();
        $recoveryDays = Setting::getLateRecoveryDays();

        $expiredMinutes = $this->expired_late_minutes;
        $expiringMinutes = $this->expiring_late_minutes;
        $progressTowardsPenalty = $penaltyThreshold > 0 
            ? min(100, round(($expiredMinutes / $penaltyThreshold) * 100))
            : 0;

        return array_merge($baseStats, [
            'expired_minutes' => $expiredMinutes,
            'expiring_minutes' => $expiringMinutes,
            'penalty_threshold' => $penaltyThreshold,
            'recovery_days' => $recoveryDays,
            'progress_towards_penalty' => $progressTowardsPenalty,
            'penalty_absences_count' => $this->penalty_absences_count,
            'upcoming_penalties' => $this->upcoming_penalty_absences,
            'expiring_presences' => $this->getExpiringLatePresences(),
        ]);
    }

    /**
     * Check if user has any late expiration warnings
     */
    public function hasLateExpirationWarning(): bool
    {
        return $this->expiring_late_minutes > 0;
    }
}

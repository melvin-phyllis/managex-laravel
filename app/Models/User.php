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
}

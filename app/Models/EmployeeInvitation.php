<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeInvitation extends Model
{
    protected $fillable = [
        'token',
        'email',
        'name',
        'department_id',
        'position_id',
        'poste',
        'contract_type',
        'hire_date',
        'contract_end_date',
        'work_days',
        'base_salary',
        'invited_by',
        'expires_at',
        'completed_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'work_days' => 'array',
            'hire_date' => 'date',
            'contract_end_date' => 'date',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
            'base_salary' => 'decimal:2',
        ];
    }

    // Relationships

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // State checks

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isCompleted() && ! $this->isExpired();
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->whereNull('completed_at')->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNull('completed_at')->where('expires_at', '<=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}

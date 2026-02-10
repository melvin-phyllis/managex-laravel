<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'date',
        'notes',
        // Analytics fields
        'is_late',
        'late_minutes',
        'is_early_departure',
        'early_departure_minutes',
        'departure_type',
        'early_departure_reason',
        'scheduled_start',
        'scheduled_end',
        // Recovery tracking
        'overtime_minutes',
        'recovery_minutes',
        'is_recovery_session',
        // Late expiration tracking
        'late_recovery_deadline',
        'is_late_expired',
        'expired_late_minutes',
        // Early arrival tracking
        'pre_check_in',
        'pre_check_in_latitude',
        'pre_check_in_longitude',
        'is_early_arrival',
        'is_auto_checkout',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
        'is_late' => 'boolean',
        'is_early_departure' => 'boolean',
        'is_recovery_session' => 'boolean',
        'overtime_minutes' => 'integer',
        'recovery_minutes' => 'integer',
        'late_minutes' => 'integer',
        // Late expiration
        'late_recovery_deadline' => 'date',
        'is_late_expired' => 'boolean',
        'expired_late_minutes' => 'integer',
        // Early arrival
        'pre_check_in' => 'datetime',
        'is_early_arrival' => 'boolean',
        'is_auto_checkout' => 'boolean',
    ];

    /**
     * Get the user that owns this presence
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for a specific month
     */
    public function scopeMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    /**
     * Scope for period
     */
    public function scopeInPeriod($query, Carbon $start, Carbon $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    /**
     * Scope for late arrivals
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope for department filtering
     */
    public function scopeForDepartment($query, ?int $departmentId)
    {
        if ($departmentId) {
            return $query->whereHas('user', fn ($q) => $q->where('department_id', $departmentId));
        }

        return $query;
    }

    /**
     * Get total hours worked
     */
    public function getHoursWorkedAttribute(): ?float
    {
        if (! $this->check_out) {
            return null;
        }

        return $this->check_in->diffInMinutes($this->check_out) / 60;
    }

    /**
     * Get formatted check in time
     */
    public function getCheckInFormattedAttribute(): string
    {
        return $this->check_in->format('H:i');
    }

    /**
     * Get formatted check out time
     */
    public function getCheckOutFormattedAttribute(): ?string
    {
        return $this->check_out?->format('H:i');
    }

    /**
     * Calculate overtime minutes (used when saving)
     */
    public function calculateOvertimeMinutes(): int
    {
        if (! $this->check_out || ! $this->scheduled_end) {
            return 0;
        }

        // Ensure scheduled_end is full datetime for comparison
        $scheduledEnd = Carbon::parse($this->date->format('Y-m-d').' '.$this->scheduled_end);
        $actualEnd = Carbon::parse($this->check_out);

        return max(0, $actualEnd->diffInMinutes($scheduledEnd, false));
    }

    /**
     * Scope for presences with recovery
     */
    public function scopeWithRecovery($query)
    {
        return $query->where('recovery_minutes', '>', 0);
    }

    /**
     * Scope for recovery sessions
     */
    public function scopeRecoverySessions($query)
    {
        return $query->where('is_recovery_session', true);
    }

    /**
     * Apply automatic recovery based on overtime and user's late balance
     *
     * @param  int  $userLateBalance  Current balance of late minutes to recover
     * @return int Minutes applied as recovery
     */
    public function applyAutomaticRecovery(int $userLateBalance): int
    {
        if ($userLateBalance <= 0 || $this->overtime_minutes <= 0) {
            return 0;
        }

        // Apply recovery up to the overtime worked or remaining balance
        $recoveryToApply = min($this->overtime_minutes, $userLateBalance);

        $this->recovery_minutes = $recoveryToApply;
        $this->save();

        return $recoveryToApply;
    }

    /**
     * Get formatted recovery info
     */
    public function getRecoveryInfoAttribute(): ?string
    {
        if ($this->recovery_minutes > 0) {
            $hours = floor($this->recovery_minutes / 60);
            $mins = $this->recovery_minutes % 60;

            return $hours > 0 ? "{$hours}h{$mins}" : "{$mins} min";
        }

        return null;
    }

    /**
     * Get formatted overtime info
     */
    public function getOvertimeInfoAttribute(): ?string
    {
        if ($this->overtime_minutes > 0) {
            $hours = floor($this->overtime_minutes / 60);
            $mins = $this->overtime_minutes % 60;

            return $hours > 0 ? "{$hours}h{$mins}" : "{$mins} min";
        }

        return null;
    }

    /**
     * Get formatted late info
     */
    public function getLateInfoAttribute(): ?string
    {
        if ($this->late_minutes > 0) {
            $hours = floor($this->late_minutes / 60);
            $mins = $this->late_minutes % 60;

            return $hours > 0 ? "{$hours}h{$mins}" : "{$mins} min";
        }

        return null;
    }

    /**
     * Scope for expired late hours
     */
    public function scopeExpiredLate($query)
    {
        return $query->where('is_late_expired', true);
    }

    /**
     * Scope for late hours that are about to expire
     */
    public function scopeExpiringLate($query, int $daysBeforeExpiration = 2)
    {
        $warningDate = Carbon::today()->addDays($daysBeforeExpiration);

        return $query->where('is_late', true)
            ->where('is_late_expired', false)
            ->whereNotNull('late_recovery_deadline')
            ->where('late_recovery_deadline', '<=', $warningDate)
            ->whereRaw('late_minutes > recovery_minutes');
    }

    /**
     * Get unrecovered late minutes
     */
    public function getUnrecoveredMinutesAttribute(): int
    {
        return max(0, ($this->late_minutes ?? 0) - ($this->recovery_minutes ?? 0));
    }

    /**
     * Check if this late is still recoverable
     */
    public function isRecoverable(): bool
    {
        if (! $this->is_late || $this->is_late_expired) {
            return false;
        }

        if (! $this->late_recovery_deadline) {
            return true; // No deadline set
        }

        return $this->late_recovery_deadline->isFuture() || $this->late_recovery_deadline->isToday();
    }

    /**
     * Get days remaining to recover
     */
    public function getDaysToRecoverAttribute(): ?int
    {
        if (! $this->late_recovery_deadline || $this->is_late_expired) {
            return null;
        }

        $days = Carbon::today()->diffInDays($this->late_recovery_deadline, false);

        return max(0, $days);
    }

    /**
     * Get recovery status
     */
    public function getRecoveryStatusAttribute(): string
    {
        if (! $this->is_late) {
            return 'not_applicable';
        }

        if ($this->is_late_expired) {
            return 'expired';
        }

        if ($this->unrecovered_minutes <= 0) {
            return 'recovered';
        }

        if ($this->days_to_recover !== null && $this->days_to_recover <= 2) {
            return 'expiring_soon';
        }

        return 'pending';
    }
}

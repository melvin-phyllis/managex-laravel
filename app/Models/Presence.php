<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
        'is_late' => 'boolean',
        'is_early_departure' => 'boolean',
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
            return $query->whereHas('user', fn($q) => $q->where('department_id', $departmentId));
        }
        return $query;
    }

    /**
     * Get total hours worked
     */
    public function getHoursWorkedAttribute(): ?float
    {
        if (!$this->check_out) {
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
     * Get overtime minutes
     */
    public function getOvertimeMinutesAttribute(): int
    {
        if (!$this->check_out || !$this->scheduled_end) return 0;
        
        // Ensure scheduled_end is full datetime for comparison
        $scheduledEnd = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->scheduled_end);
        $actualEnd = Carbon::parse($this->check_out);
        
        return max(0, $actualEnd->diffInMinutes($scheduledEnd, false));
    }
}

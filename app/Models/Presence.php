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
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'date' => 'date',
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
}
